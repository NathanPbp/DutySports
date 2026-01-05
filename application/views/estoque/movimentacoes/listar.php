<?php $this->load->view('estoque/menu'); ?>

<div class="container">
    <h3>📊 Histórico de Movimentações</h3>

    <a href="<?= site_url('estoque_movimentacoes/movimentar') ?>" class="btn btn-success" style="margin-bottom:15px;">
        ➕ Nova Movimentação
    </a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Data</th>
                <th>Item</th>
                <th>Setor</th>
                <th>Tipo</th>
                <th>Qtd</th>
                <th>Origem</th>
                <th>Usuário</th>
            </tr>
        </thead>

        <tbody>
        <?php if (!empty($movimentacoes)): ?>
            <?php foreach ($movimentacoes as $m): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($m->data_movimentacao)) ?></td>
                    <td><?= htmlspecialchars($m->item_nome) ?></td>
                    <td><?= htmlspecialchars($m->setor_nome) ?></td>
                    <td><?= htmlspecialchars($m->tipo) ?></td>
                    <td><?= number_format($m->quantidade, 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($m->origem) ?></td>
                    <td><?= !empty($m->usuario_nome) ? htmlspecialchars($m->usuario_nome) : (int)$m->usuarios_id ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">Nenhuma movimentação registrada.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
