<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_admin'] != 1) {
	header('Location: /usuario/entrar');
	exit();
}

require_once 'Motor/Modelos/UsuarioModelo.php';
require_once 'Motor/Modelos/ProdutoModelo.php';

$usuarioId = $_SESSION['usuario_id'];
$nomeUsuario = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Usuário';
$produtoModelo = new ProdutoModelo();
$usuarioModelo = new UsuarioModelo();


// Recuperar pedidos do usuário
$pedidos = $usuarioModelo->buscarPedidosPorUsuarioId($usuarioId);
$usuario = $usuarioModelo->recuperarUsuarioPorId($usuarioId);

// var_dump($pedidos);



?>

<form id="form-pedidos" method="POST" action="/admin/excluirPedidos">
    <table>
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th> <!-- Checkbox principal -->
                <th>ID</th>
                <th>Cliente</th>
                <th>Pedido</th>
                <th>Total</th>
                <th>Status</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($pedidos)): ?>
            <?php foreach ($pedidos as $pedido): ?>
            <tr>
                <td><input type="checkbox" name="pedidos[]" value="<?php echo htmlspecialchars($pedido['id']); ?>"></td> <!-- Checkbox do pedido -->
                <td><?php echo htmlspecialchars($pedido['id']); ?></td>
                <td><?php echo $pedido['usuario_id']; ?></td>
                <td>
                    <?php 
						 $itens = json_decode($pedido['itens'], true); 

                    if (is_array($itens)): 
                    foreach ($itens as $item): 
                    $nomeProduto = $item['produto'];
                    ?>
                    <?php echo htmlspecialchars($nomeProduto); ?> 
                    - <?php echo htmlspecialchars($item['quantidade']); ?><br> 
                    <?php 
                    endforeach; 
                    endif; 
                    ?>
                </td>
                <td>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars($pedido['status']); ?></td>
                <td><?php echo htmlspecialchars($pedido['data']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="7">Nenhum pedido encontrado.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <button type="submit" name="acao" value="excluir">Excluir Selecionados</button>
    <button type="button" onclick="sincronizarPedidos()">Sincronizar Pedidos</button>
</form>

<script>
    // Selecionar todos os pedidos
    document.getElementById('select-all').addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('input[name="pedidos[]"]');
        for (const checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    });

    // Função para sincronizar pedidos
    function sincronizarPedidos() {
        fetch('/admin/sincronizarPedidos', { method: 'POST' })
            .then(response => response.json())
            .then(data => alert(data.mensagem))
            .catch(error => console.error('Erro ao sincronizar:', error));
    }
</script>


