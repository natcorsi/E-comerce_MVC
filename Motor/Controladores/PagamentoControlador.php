<?php

require_once './vendor/autoload.php';
require_once './config.php'; // Inclua o arquivo de configuração

use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;

$url = isset($url) ? $url : 'default_url';

class PagamentoControlador extends Controlador {
	private $produtoModelo;
	private $carrinhoModelo;

	public function __construct() {
		$this->produtoModelo = $this->modelo('ProdutoModelo');
		$this->carrinhoModelo = $this->modelo('CarrinhoModelo');
	}

	public function index() {
		$carrinho = $this->carrinhoModelo->obterCarrinho();
		$produtosDetalhados = [];

		foreach ($carrinho as $sku => $quantidade) {
			$produto = $this->produtoModelo->obterProdutoPorSku($sku);
			if ($produto) {
				$produto['quantidade'] = $quantidade;
				$produtosDetalhados[] = $produto;
			}
		}

		$this->vista('Loja/pagamento', ['produtos' => $produtosDetalhados]);
	}

public function criarPagamento() {
    // Salvar informações do cliente
    $usuario_id = $_SESSION['usuario_id'];
    $dadosUsuario = [
        'nome' => $_POST['nome'],
        'email' => $_POST['email'],
        'telefone' => $_POST['telefone'],
        'endereco' => $_POST['endereco'],
        'complemento' => $_POST['complemento'],
        'cidade' => $_POST['cidade'],
        'estado' => $_POST['estado'],
        'pais' => $_POST['pais'],
        'cep' => $_POST['cep']
    ];

    $usuarioModelo = new UsuarioModelo();
    $usuarioModelo->atualizarDados($usuario_id, $dadosUsuario['nome'], $dadosUsuario['email'], $dadosUsuario['telefone'], $dadosUsuario['endereco'], $dadosUsuario['complemento'], $dadosUsuario['cidade'], $dadosUsuario['estado'], $dadosUsuario['pais'], $dadosUsuario['cep']);

    // Criar o pedido
    $pedido = $_POST['pedido'] ?? null;
    $_SESSION['pedido'] = $pedido;

    if ($pedido === null) {
        registrar_log("Erro: Nenhum pedido enviado para criar pagamento.");
        echo "Pedido não encontrado.";
        return;
    }

    registrar_log("Pedido salvo na sessão: " . json_encode($pedido));

    // Configurar Mercado Pago
    MercadoPagoConfig::setAccessToken('TEST-3968226300877701-062114-30547912a9e949d94fd74c0fde4c47cc-1337868673');
    $client = new PreferenceClient();

    $dadosPreferencia = [
        "items" => [],
        "payer" => [
            "name" => $dadosUsuario['nome'],
            "email" => $dadosUsuario['email'],
            "address" => [
                "street_name" => $dadosUsuario['endereco'],
                "street_number" => $dadosUsuario['complemento'],
                "zip_code" => $dadosUsuario['cep']
            ]
        ]
    ];

    foreach ($pedido['itens'] as $item) {
        $dadosPreferencia['items'][] = [
            "title" => $item['nome'],
            "quantity" => (int)$item['quantidade'],
            "unit_price" => (float)$item['preco'],
            "sku" => $item['sku'] ?? 'SKU-Desconhecido' // Adiciona o SKU do produto
        ];
    }

    $baseUrl = 'https://tocadosmagos.sytes.net';
    $dadosPreferencia['back_urls'] = [
        "success" => $baseUrl . "/pagamento/sucesso",
        "failure" => $baseUrl . "/pagamento/falha",
        "pending" => $baseUrl . "/pagamento/pendente"
    ];
    $dadosPreferencia['auto_return'] = "approved";

    try {
        $preference = $client->create($dadosPreferencia);
        header("Location: " . $preference->init_point);
        exit();
    } catch (MPApiException $e) {
        echo "Erro ao processar o pagamento. Tente novamente mais tarde.";
    } catch (\Exception $e) {
        echo "Erro ao processar o pagamento. Tente novamente mais tarde.";
    }
}

public function sucesso() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /usuario/entrar');
        return;
    }

    $usuarioId = $_SESSION['usuario_id'];
    $status = $_GET['collection_status'] ?? 'pendente';
    $pedido = $_SESSION['pedido'] ?? null;

    if (!$pedido) {
        echo "Erro: Detalhes do pedido não encontrados. Tente novamente.";
        registrar_log("Erro: Detalhes do pedido não encontrados no método sucesso.");
        return;
    }

    // Diagnóstico do pedido recebido
    registrar_log("Pedido recebido no método sucesso: " . json_encode($pedido));

    // Traduzir status
    $statusTraduzido = match ($status) {
        'approved' => 'processando',
        'failure' => 'recusado',
        'pending' => 'pendente',
        default => 'pendente'
    };

    // Calcular total do pedido e montar os itens
    $itens = [];
    $total = 0.0;

    foreach ($pedido['itens'] as $sku => $item) {
        if (empty($sku) || !isset($item['quantidade'], $item['preco'])) {
            registrar_log("Erro: Dados incompletos no item do pedido. sku=$sku, dados=" . json_encode($item));
            continue; // Pula este item e processa o próximo
        }

        $quantidade = (int)$item['quantidade'];
        $precoUnitario = (float)$item['preco'];
        $subtotal = $quantidade * $precoUnitario;

        $itens[] = [
			  'produto' => $item['nome'], // Certifique-se de que o SKU está sendo usado aqui
            'quantidade' => $quantidade,
            'preco_unitario' => $precoUnitario,
            'sku' => $sku,
            'subtotal' => $subtotal
        ];

        $total += $subtotal;
    }

    // Inserir pedido no banco de dados
    try {
        $this->modelo('CompraModelo')->registrarPedido($usuarioId, $itens, $total, $statusTraduzido);
        registrar_log("Pedido registrado com sucesso: usuario_id=$usuarioId, total=$total, status=$statusTraduzido");
    } catch (Exception $e) {
        registrar_log("Erro ao tentar registrar pedido: usuario_id=$usuarioId, erro: " . $e->getMessage());
        echo "Erro ao registrar o pedido. Por favor, tente novamente.";
        return;
    }

    // Limpar carrinho e pedido da sessão
    $_SESSION['carrinho'] = [];
    unset($_SESSION['pedido']);

    echo "Pagamento bem-sucedido. Pedido registrado com sucesso.";
}



	public function falha() {
		echo "Pagamento com falha.";
	}

	public function pendente() {
		echo "Pagamento pendente.";
	}



}


?>
