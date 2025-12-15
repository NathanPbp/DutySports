<div class="container">
    <h3>Pedido Encontrado</h3>

    <div class="alert alert-info">
        <strong>Comanda:</strong> <?= $os->codigo_comanda ?><br>
        <strong>Cliente:</strong> <?= $os->nomeCliente ?? '-' ?><br>
        <strong>Status:</strong> <?= $os->status_os ?>
    </div>

    <h4>Produtos</h4>
    <ul>
        <?php foreach ($produtos as $p) { ?>
            <li><?= $p->descricao ?> — Qtd: <?= $p->quantidade ?></li>
        <?php } ?>
    </ul>

    <h4>Serviços</h4>
    <ul>
        <?php foreach ($servicos as $s) { ?>
            <li><?= $s->nome ?> — Qtd: <?= $s->quantidade ?></li>
        <?php } ?>
    </ul>

    <a href="<?= site_url('caixa') ?>" class="btn btn-secondary">
        Nova Busca
    </a>
</div>
