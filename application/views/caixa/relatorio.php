<div class="container" style="max-width:1100px;">

    <h3 style="margin-top:10px;">📊 Caixa — Relatório de Caixa</h3>

    <?php $isRelatorio = ($this->uri->segment(2) === 'relatorio'); ?>
    <ul class="nav nav-tabs" style="margin: 10px 0 15px;">
        <li class="<?= $isRelatorio ? '' : 'active' ?>">
            <a href="<?= site_url('caixa') ?>">Caixa</a>
        </li>
        <li class="<?= $isRelatorio ? 'active' : '' ?>">
            <a href="<?= site_url('caixa/relatorio') ?>">Relatório de Caixa</a>
        </li>
    </ul>

    <div class="well">
        <form class="form-inline" method="get" action="<?= site_url('caixa/relatorio') ?>">
            <div class="form-group" style="margin-right:10px;">
                <label for="data_inicio" style="margin-right:6px;">Data inicial</label>
                <input type="date" id="data_inicio" name="data_inicio" class="form-control" value="<?= html_escape($dataInicio) ?>">
            </div>
            <div class="form-group" style="margin-right:10px;">
                <label for="data_fim" style="margin-right:6px;">Data final</label>
                <input type="date" id="data_fim" name="data_fim" class="form-control" value="<?= html_escape($dataFim) ?>">
            </div>

            <button type="submit" class="btn btn-primary">Filtrar</button>

            <span class="help-block" style="display:inline-block; margin-left:12px; margin-bottom:0;">
                Período: <strong><?= date('d/m/Y', strtotime($dataInicio)) ?></strong> até <strong><?= date('d/m/Y', strtotime($dataFim)) ?></strong>
            </span>
        </form>
    </div>

    <?php
        $tEntrada   = (float)($totais->total_entrada ?? 0);
        $tPagamento = (float)($totais->total_pagamento ?? 0);
        $tRetirada  = (float)($totais->total_retirada ?? 0);
        $tPix       = (float)($totais->total_pix ?? 0);
        $tDinheiro  = (float)($totais->total_dinheiro ?? 0);
        $tCartao    = (float)($totais->total_cartao ?? 0);
        $tGeral     = (float)($totais->total_geral ?? 0);
    ?>

    <div class="row-fluid" style="margin-top:10px;">
        <div class="span3">
            <div class="alert alert-success" style="margin-bottom:10px;">
                <strong>Entradas</strong><br>
                R$ <?= number_format($tEntrada, 2, ',', '.') ?>
            </div>
        </div>
        <div class="span3">
            <div class="alert alert-info" style="margin-bottom:10px;">
                <strong>Pagamentos</strong><br>
                R$ <?= number_format($tPagamento, 2, ',', '.') ?>
            </div>
        </div>
        <div class="span3">
            <div class="alert alert-warning" style="margin-bottom:10px;">
                <strong>Retiradas</strong><br>
                R$ <?= number_format($tRetirada, 2, ',', '.') ?>
            </div>
        </div>
        <div class="span3">
            <div class="alert alert-block" style="margin-bottom:10px;">
                <strong>Total do período</strong><br>
                R$ <?= number_format($tGeral, 2, ',', '.') ?>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span4">
            <div class="well" style="margin-bottom:10px;">
                <strong>Por forma de pagamento</strong><br>
                <div style="margin-top:8px;">
                    PIX: <strong>R$ <?= number_format($tPix, 2, ',', '.') ?></strong><br>
                    Dinheiro: <strong>R$ <?= number_format($tDinheiro, 2, ',', '.') ?></strong><br>
                    Cartão: <strong>R$ <?= number_format($tCartao, 2, ',', '.') ?></strong>
                </div>
            </div>
        </div>
        <div class="span8">
            <div class="alert alert-info" style="margin-bottom:10px;">
                <strong>Regra:</strong> este relatório considera apenas <strong>Receitas baixadas</strong> (lancamentos.tipo = receita e baixado = 1) e filtra por <strong>data_pagamento</strong>.
            </div>
        </div>
    </div>

    <h4 style="margin-top:15px;">Lançamentos do período</h4>

    <div style="overflow:auto;">
        <table class="table table-bordered table-condensed" style="min-width:900px;">
            <thead>
                <tr>
                    <th style="width:170px;">Data/Hora</th>
                    <th style="width:120px;">Tipo</th>
                    <th style="width:120px;">Forma</th>
                    <th>Cliente</th>
                    <th style="width:140px; text-align:right;">Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($lancamentos)) : ?>
                    <?php foreach ($lancamentos as $l) :
                        $desc = (string)($l->descricao ?? '');

                        if (stripos($desc, 'ENTRADA') === 0) {
                            $tipoMov = 'ENTRADA';
                            $badge = 'success';
                        } elseif (stripos($desc, 'RETIRADA') === 0) {
                            $tipoMov = 'RETIRADA';
                            $badge = 'warning';
                        } elseif (stripos($desc, 'PAGAMENTO') === 0) {
                            $tipoMov = 'PAGAMENTO';
                            $badge = 'info';
                        } else {
                            $tipoMov = 'RECEITA';
                            $badge = 'inverse';
                        }
                    ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($l->data_pagamento)) ?></td>
                            <td><span class="badge badge-<?= $badge ?>"><?= $tipoMov ?></span></td>
                            <td><?= html_escape($l->forma_pgto) ?></td>
                            <td><?= html_escape($l->cliente_fornecedor) ?></td>
                            <td style="text-align:right;">R$ <?= number_format((float)$l->valor, 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5">Nenhum lançamento encontrado neste período.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
