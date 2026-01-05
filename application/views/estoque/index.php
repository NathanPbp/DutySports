<?php
$isItens         = ($this->uri->segment(2) === 'itens' || $this->uri->segment(2) === '');
$isMovimentacoes = ($this->uri->segment(2) === 'movimentacoes');
$isConsumo       = ($this->uri->segment(2) === 'consumo');
?>

<div class="container">

    <h3 style="margin-top:10px;">📦 Estoque</h3>

    <?php
$controller = $this->uri->segment(1);
$action     = $this->uri->segment(2);

$isItens         = ($controller === 'estoque_itens');
$isMovimentacoes = ($controller === 'estoque_movimentacoes');
$isConsumo       = ($controller === 'estoque_relatorios' && $action === 'consumo');
?>

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

<?php
if (isset($view)) {
    $this->load->view($view);
}
?>


</div>

