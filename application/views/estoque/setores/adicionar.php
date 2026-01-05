<div class="container">
    <h3>➕ Novo Setor</h3>

    <form method="post">
        <div class="control-group">
            <label>Nome do setor</label>
            <input type="text" name="nome" class="span6" required>
        </div>

        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="<?= site_url('estoque_setores') ?>" class="btn">Voltar</a>
    </form>
</div>
