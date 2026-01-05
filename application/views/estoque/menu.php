<?php
$controller = $this->uri->segment(1);
$action     = $this->uri->segment(2);

$isItens         = ($controller === 'estoque_itens');
$isMovimentacoes = ($controller === 'estoque_movimentacoes');
$isConsumo       = ($controller === 'estoque_relatorios' && $action === 'consumo');
?>

<div class="container">
    <h3 style="margin-top:10px;">📦 Estoque</h3>

    <ul class="nav nav-tabs" style="margin: 10px 0 15px;">
        <li class="<?= $isItens ? 'active' : '' ?>">
            <a href="<?= site_url('estoque_itens') ?>">📦 Itens</a>
        </li>

        <li class="<?= $isMovimentacoes ? 'active' : '' ?>">
            <a href="<?= site_url('estoque_movimentacoes') ?>">🔄 Movimentações</a>
        </li>

        <li class="<?= $isConsumo ? 'active' : '' ?>">
            <a href="<?= site_url('estoque_relatorios/consumo') ?>">🏭 Consumo</a>
        </li>
    </ul>
</div>
