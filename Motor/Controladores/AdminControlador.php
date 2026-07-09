<?php

require_once 'Controlador.php';
require_once __DIR__ . '/../API/Gerenciadores/GerenciadorImportacao.php';
require_once __DIR__ . '/../API/Importadores/ImportadorPrintful.php';
// Adicione a importação do ImportadorAliExpress quando estiver implementado
// require_once __DIR__ . '/../API/importadores/ImportadorAliExpress.php';

class AdminControlador extends Controlador {
	private $produtoModelo;
	private $gerenciadorImportacao;

	public function __construct() {
		$this->produtoModelo = $this->modelo('ProdutoModelo');
		$this->gerenciadorImportacao = new GerenciadorImportacao();

		// Registrar importadores
		$this->gerenciadorImportacao->registrarImportador('printful', new ImportadorPrintful(PRINTFUL_API_KEY));
		// Registrar outros importadores de APIs
		// $this->gerenciadorImportacao->registrarImportador('aliexpress', new ImportadorAliExpress(ALIEXPRESS_API_KEY));
	}

	public function painel() {
		// Obter todos os produtos do banco de dados
		$produtos = $this->produtoModelo->obterTodosProdutos();

		// Exibir a vista do painel administrativo
		$this->vista('Admin/painel', ['produtos' => $produtos]);
	}

	public function editar($id) {
		// Carregar o modelo do produto
		$produtoModelo = new ProdutoModelo();
		$produto = $produtoModelo->obterProdutoPorId($id);

		if (!$produto) {
			die('Produto não encontrado');
		}

		// Carregar a visão de edição do produto
		require_once 'Motor/Visoes/Admin/editar.php';
	}

	public function sincronizar() {
		try {
			registrar_log('Iniciando sincronização', __FILE__);

			if (!isset($_POST['api'])) {
				throw new Exception("Nenhuma API selecionada");
			}

			$api = $_POST['api'];

			registrar_log("API selecionada: $api", __FILE__);

			$produtos = $this->gerenciadorImportacao->importarProdutos($api);
			registrar_log('Produtos importados da API', __FILE__);


			foreach ($produtos as $produto) {
				registrar_log('Salvando produto: ' . json_encode($produto), __FILE__);

				// Adicionar a descrição do produto detalhado
				$resultado = $this->gerenciadorImportacao->importarVariantes($api, $produto['id']);
				$produtoDetalhado = $resultado['produto_detalhado'];

				if (isset($produtoDetalhado['description'])) {
					$produto['description'] = $produtoDetalhado['description'];
				}

				// Calcular e formatar os preços mínimo e máximo das variantes
				$precoMinimo = $resultado['preco_minimo'];
				$precoMaximo = $resultado['preco_maximo'];

				// Alteração para manter o preço mínimo e máximo de acordo com a lógica
				if ($precoMinimo === $precoMaximo) {
					$produto['price'] = number_format($precoMinimo, 2, '.', '.');
					$produto['preco_max'] = null; // Não há preço máximo, portanto, deixamos como nulo
				} else {
					$produto['price'] = number_format($precoMinimo, 2, '.', '.');
					$produto['preco_max'] = number_format($precoMaximo, 2, '.', '.');
				}

				// Número de variantes
				$produto['variantes'] = isset($resultado['variantes']) ? count($resultado['variantes']) : 0;
				registrar_log('Número de variantes calculado: ' . $produto['variantes'], __FILE__);

				// Logs de informações do produto
				// registrar_log('Descrição do produto a ser salva: ' . $produto['description'], __FILE__);
				// registrar_log('Preço do produto a ser salvo: ' . $produto['price'], __FILE__);
				// registrar_log('Preço máximo do produto a ser salvo: ' . $produto['preco_max'], __FILE__);
				// registrar_log('Número de variantes do produto a ser salvo: ' . $produto['variantes'], __FILE__);

				// Salvar o produto principal
				$produtoId = $this->produtoModelo->salvarProduto($produto);
				registrar_log("Produto salvo com ID: $produtoId", __FILE__);

				// Importar e salvar variantes
				$variantes = $resultado['variantes'];
				registrar_log('Variantes importadas: ' . json_encode($variantes), __FILE__);

				foreach ($variantes as $variacao) {
					registrar_log('Salvando variante: ' . json_encode($variacao), __FILE__);
					$this->produtoModelo->salvarVariantes($variacao, $produtoId);
				}
			}

			$_SESSION['mensagem'] = 'Produtos sincronizados com sucesso!';
			registrar_log('Sincronização concluída com sucesso', __FILE__);
		} catch (Exception $e) {
			$_SESSION['mensagem'] = 'Erro ao sincronizar produtos: ' . $e->getMessage();
			registrar_log('Erro ao sincronizar produtos: ' . $e->getMessage(), __FILE__);
		}

		header('Location: /admin/painel');
		exit;
	}

	public function excluir() {
		try {
			if (!isset($_POST['id'])) {
				throw new Exception("ID do produto não fornecido");
			}

			$produtoId = $_POST['id'];
			registrar_log("Excluindo produto com ID: $produtoId", __FILE__);

			// Excluir variantes do produto
			$this->produtoModelo->excluirVariantes($produtoId);

			// Excluir o produto principal
			$this->produtoModelo->excluirProduto($produtoId);

			$_SESSION['mensagem'] = 'Produto excluído com sucesso!';
			registrar_log('Produto excluído com sucesso', __FILE__);
		} catch (Exception $e) {
			$_SESSION['mensagem'] = 'Erro ao excluir produto: ' . $e->getMessage();
			registrar_log('Erro ao excluir produto: ' . $e->getMessage(), __FILE__);
		}

		header('Location: /admin/painel');
		exit;
	}


	public function importarProduto($produto) {
		// Calcular os preços mínimo e máximo antes de enviar para o modelo
		$precoMin = isset($produto['price']) && is_numeric($produto['price']) ? $produto['price'] : 0.00;
		$precoMax = $precoMin;

		if (isset($produto['variantes']) && is_array($produto['variantes'])) {
			foreach ($produto['variantes'] as $variacao) {
				if (isset($variacao['price']) && is_numeric($variacao['price']) && $variacao['price'] > 0) {
					$preco = $variacao['price'];
					$precoMin = min($precoMin, $preco); // Atualizar preço mínimo
					$precoMax = max($precoMax, $preco); // Atualizar preço máximo
				}
			}
		}

		// Garantir que o preço máximo seja maior que o mínimo
		if ($precoMin == $precoMax) {
			$precoMax = $precoMin + 0.01;
		}

		// Passar os dados para o modelo
		$produto['preco_min'] = round($precoMin, 2);  // Preço mínimo
		$produto['preco_max'] = round($precoMax, 2);  // Preço máximo

		// Agora chamamos o modelo para salvar
		$produtoModelo = new ProdutoModelo();
		$produtoId = $produtoModelo->salvarProduto($produto);

		return $produtoId;
	}

public function pedidos() {
    // Carregar o modelo de pedidos
    $pedidoModelo = $this->modelo('PedidoModelo');

    // Carregar o modelo de usuários
    $usuarioModelo = $this->modelo('UsuarioModelo');

    // Obter todos os pedidos do banco de dados
    $pedidos = $pedidoModelo->buscarTodosPedidos();

    // Carregar o modelo de produtos
    $produtoModelo = $this->modelo('ProdutoModelo');

    // Montar os produtos detalhados
    foreach ($pedidos as &$pedido) {
        $usuario = $usuarioModelo->recuperarUsuarioPorId($pedido['usuario_id']);
        registrar_log("Dados do usuário: " . json_encode($usuario), __FILE__);
        if ($usuario) {
            $pedido['usuario'] = $usuario;
        } else {
            registrar_log("Usuário não encontrado para ID: " . $pedido['usuario_id'], __FILE__);
            $pedido['usuario'] = ['nome' => 'Usuário desconhecido'];
        }

        $itens = json_decode($pedido['itens'], true);
        foreach ($itens as &$item) {
            $produto = $produtoModelo->obterProdutoPorSku($item['sku']);
            if ($produto) {
                $item['nome'] = $produto['nome'];
                $item['descricao'] = $produto['descricao'];
                $item['imagem'] = $produto['imagem'];
            } else {
                $item['nome'] = "Produto desconhecido";
            }
        }
        $pedido['itens'] = $itens;
    }

    // Renderizar a página de pedidos com os dados
    $this->vista('Admin/pedidos', ['pedidos' => $pedidos]);
}
}
?>
