<?php $this->load->view('estoque/menu'); ?>
<div class="container">
    <h3>📊 Consumo de Estoque por Setor</h3>

    <form method="get" class="form-inline" style="margin-bottom:15px;">
        <label>De</label>
        <input type="date" name="data_inicio" value="<?= $dataInicio ?>" class="span2">

        <label>Até</label>
        <input type="date" name="data_fim" value="<?= $dataFim ?>" class="span2">

        <button class="btn btn-primary">Filtrar</button>
    </form>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Setor</th>
                <th>Item</th>
                <th>Total Consumido</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($relatorio)): ?>
            <?php foreach ($relatorio as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r->setor) ?></td>
                    <td><?= htmlspecialchars($r->item) ?></td>
                    <td>
                        <?= number_format($r->total_consumido, 2, ',', '.') ?>
                        <?= htmlspecialchars($r->unidade) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">Nenhum consumo no período.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
