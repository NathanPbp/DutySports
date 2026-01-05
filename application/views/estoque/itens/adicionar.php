<div class="container-fluid">

    <h3 style="margin-top:10px;">
        ➕ Novo Item de Estoque
    </h3>

    <form method="post">

        <!-- LINHA 1 -->
        <div class="row-fluid">

            <div class="span6">
                <label><strong>Nome do Item</strong></label>
                <input type="text"
                       name="nome"
                       class="span12"
                       required>
            </div>

            <div class="span6">
                <label><strong>Setor</strong></label>
                <select name="setor_id" class="span12" required>
                    <option value="">Selecione</option>
                    <?php foreach ($setores as $s): ?>
                        <option value="<?= $s->id ?>"><?= $s->nome ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>

        <!-- LINHA 2 -->
        <div class="row-fluid" style="margin-top:10px;">

            <div class="span6">
                <label><strong>Unidade</strong></label>
                <input type="text"
                       name="unidade"
                       class="span12"
                       placeholder="un, kg, m"
                       required>
            </div>

            <div class="span6">
                <label><strong>Estoque mínimo</strong></label>
                <input type="number"
                       name="estoque_minimo"
                       class="span12"
                       value="0"
                       min="0">
            </div>

        </div>

        <hr>

        <button type="submit" class="btn btn-success">
            💾 Salvar
        </button>

        <a href="<?= site_url('estoque_itens') ?>" class="btn">
            Voltar
        </a>

    </form>

</div>
