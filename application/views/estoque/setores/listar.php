<div class="container">
    <h3>📦 Setores de Estoque</h3>

    <a href="<?= site_url('estoque_setores/adicionar') ?>" class="btn btn-success">
        ➕ Novo Setor
    </a>

    <table class="table table-bordered table-striped" style="margin-top:15px;">
        <thead>
            <tr>
                <th>Nome</th>
                <th style="width:150px;">Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($setores)): ?>
            <?php foreach ($setores as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s->nome) ?></td>
                    <td>
                        <a href="<?= site_url('estoque_setores/editar/' . $s->id) ?>" class="btn btn-mini btn-primary">
                            ✏️
                        </a>
                        <a href="<?= site_url('estoque_setores/excluir/' . $s->id) ?>" 
                           class="btn btn-mini btn-danger"
                           onclick="return confirm('Remover este setor?')">
                            🗑
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="2">Nenhum setor cadastrado.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
