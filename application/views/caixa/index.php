<div class="container">
    <h3>Caixa — Buscar Pedido</h3>

    <?php if ($this->session->flashdata('error')) { ?>
        <div class="alert alert-danger">
            <?= $this->session->flashdata('error') ?>
        </div>
    <?php } ?>

    <form action="<?= site_url('caixa/buscar') ?>" method="post">
        <div class="form-group">
            <label>Código da Comanda</label>
            <input type="text" name="codigo_comanda" class="form-control" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary mt-2">
            Buscar Pedido
        </button>
    </form>
</div>
