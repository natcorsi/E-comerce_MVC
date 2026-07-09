<?php


error_log("Sessão atual: " . print_r($_SESSION, true)); // Adiciona log para debug

// Verifica se o usuário está logado e se é administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_admin'] != 1) {
   header('Location: usuario/entrar');
   exit();
}
?>


<h1>Painel Administrativo</h1>

<!-- Formulário para Sincronizar Produtos -->
<form action="admin/sincronizar" method="post">
    <label for="api">Selecionar API:</label>
    <select name="api" id="api">
        <option value="printful">Printful</option>
        <!-- Adicione outras opções de API aqui -->
        <option value="aliexpress">AliExpress</option>
    </select>
    <button type="submit">Sincronizar Produtos</button>
</form>

<!-- Lista de Produtos -->
<h2>Produtos Existentes</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Preço</th>
            <th>Variações</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dados['produtos'] as $produto): ?>
        <tr>
            <td><?= htmlspecialchars($produto['id'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($produto['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($produto['preco'] ?? '', ENT_QUOTES, 'UTF-8') . htmlspecialchars($produto['preco_max'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($produto['variantes'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td>
                <form action="admin/excluir" method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($produto['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</button>
                </form>
                <a href="<?= BASE_URL ?>admin/editar/<?= $produto['id'] ?>"><button>Editar</button></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>



<!-- Exibir mensagem de status, se existir -->
<?php if (isset($_SESSION['mensagem'])): ?>
<div class="mensagem">
   <?= htmlspecialchars($_SESSION['mensagem'], ENT_QUOTES, 'UTF-8') ?>
</div>
<?php unset($_SESSION['mensagem']); ?>
<?php endif; ?>
