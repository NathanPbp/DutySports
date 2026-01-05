<?php $this->load->view('estoque/menu'); ?>
<div class="container">
    <h3>✏️ Editar Item</h3>

    <form method="post">
        <label>Nome</label>
        <input type="text" name="nome" class="span6"
               value="<?= htmlspecialchars($item->nome) ?>" required>

        <label>Setor</label>
        <select name="setor_id" class="span6" required>
            <?php foreach ($setores as $s): ?>
                <option value="<?= $s->id ?>"
                    <?= $s->id == $item->setor_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s->nome) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Unidade</label>
        <input type="text" name="unidade" class="span2"
               value="<?= $item->unidade ?>">

        <label>Estoque mínimo</label>
        <input type="number" step="0.01" name="estoque_minimo"
               value="<?= $item->estoque_minimo ?>" class="span2">

        <br><br>
        <button class="btn btn-primary">Atualizar</button>
        <a href="<?= site_url('estoque_itens') ?>" class="btn">Voltar</a>
    </form>
</div>
