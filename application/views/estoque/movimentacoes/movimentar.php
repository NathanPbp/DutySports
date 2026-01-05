<?php $this->load->view('estoque/menu'); ?>
<div class="container-fluid">

    <h3 style="margin-top:10px;">
        🔄 Movimentar Estoque
    </h3>

    <form method="post">

        <!-- LINHA 1 -->
        <div class="row-fluid">

            <div class="span6">
                <label><strong>Item</strong></label>
                <select name="item_id" class="span12" required>
                    <option value="">Selecione</option>
                    <?php foreach ($itens as $i): ?>
                        <option value="<?= $i->id ?>">
                            <?= $i->nome ?> (<?= $i->setor_nome ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="span6">
                <label><strong>Tipo</strong></label>
                <select name="tipo" class="span12" required>
                    <option value="ENTRADA">Entrada</option>
                    <option value="SAIDA">Saída</option>
                </select>
            </div>

        </div>

        <!-- LINHA 2 -->
        <div class="row-fluid" style="margin-top:10px;">

            <div class="span6">
                <label><strong>Quantidade</strong></label>
                <input type="number"
                       name="quantidade"
                       class="span12"
                       step="0.01"
                       min="0.01"
                       required>
            </div>

            <div class="span6">
                <label><strong>Origem</strong></label>
                <input type="text"
                       name="origem"
                       class="span12"
                       placeholder="Compra, Produção, Correio, Uso"
                       required>
            </div>

        </div>

        <hr>

        <button type="submit" class="btn btn-success">
            ✔ Registrar
        </button>

        <a href="<?= site_url('estoque_movimentacoes') ?>" class="btn">
            Voltar
        </a>

    </form>

</div>
