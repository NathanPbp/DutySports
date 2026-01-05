<?php $this->load->view('estoque/menu'); ?>

<?php
$itensPorSetor = [];

if (!empty($itens)) {
    foreach ($itens as $item) {
        $itensPorSetor[$item->setor_nome][] = $item;
    }
}
?>
<div class="container">
    <h3>📦 Itens de Estoque</h3>

    <a href="<?= site_url('estoque_itens/adicionar') ?>" class="btn btn-success" style="margin-bottom:15px;">
        ➕ Novo Item
    </a>

    <?php if (!empty($itensPorSetor)): ?>
        <?php foreach ($itensPorSetor as $setor => $lista): ?>

            <div class="card" style="margin-bottom:20px;">
                <div class="card-header" style="font-weight:bold;">
                    📦 <?= htmlspecialchars($setor) ?>
                </div>

                <div class="card-body" style="padding:0;">
                    <table class="table table-bordered table-striped" style="margin:0;">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th style="width:80px;">Un</th>
                                <th style="width:120px;">Qtd Atual</th>
                                <th style="width:120px;">Mínimo</th>
                                <th style="width:120px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($lista as $i): ?>
                            <tr class="<?= ($i->abaixo_minimo ? 'error' : '') ?>">
                                <td><?= htmlspecialchars($i->nome) ?></td>
                                <td><?= htmlspecialchars($i->unidade) ?></td>

                                <td>
                                    <?php if ($i->abaixo_minimo): ?>
                                        <span style="color:#b94a48;font-weight:bold;">
                                            <?= number_format($i->quantidade_atual, 2, ',', '.') ?> ⚠
                                        </span>
                                    <?php else: ?>
                                        <?= number_format($i->quantidade_atual, 2, ',', '.') ?>
                                    <?php endif; ?>
                                </td>

                                <td><?= number_format($i->estoque_minimo, 2, ',', '.') ?></td>

                                <td>
                                    <a href="<?= site_url('estoque_itens/editar/' . $i->id) ?>"
                                       class="btn btn-mini btn-primary">✏️</a>

                                    <a href="<?= site_url('estoque_itens/excluir/' . $i->id) ?>"
                                       class="btn btn-mini btn-danger"
                                       onclick="return confirm('Remover item?')">🗑</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">
            Nenhum item cadastrado.
        </div>
    <?php endif; ?>
</div>
