<?php $this->load->view('estoque/menu'); ?>

<div class="container" style="margin-left:26px;">
    <h3><i class="fa fa-pencil"></i> Editar Item</h3>

    <form method="post">

        <!-- LINHA 1 -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nome</label>
                    <input type="text"
                           name="nome"
                           class="form-control"
                           value="<?= $item->nome ?>"
                           required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Setor</label>
                    <select name="setor_id" class="form-control" required>
                        <?php foreach ($setores as $s): ?>
                            <option value="<?= $s->id ?>"
                                <?= ($s->id == $item->setor_id) ? 'selected' : '' ?>>
                                <?= $s->nome ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- LINHA 2 -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Unidade</label>
                    <input type="text"
                           name="unidade"
                           class="form-control"
                           value="<?= $item->unidade ?>"
                           required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Estoque mínimo</label>
                    <input type="number"
                           name="estoque_minimo"
                           class="form-control"
                           step="0.01"
                           value="<?= $item->estoque_minimo ?>">
                </div>
            </div>
        </div>

        <!-- BOTÕES -->
        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                💾 Atualizar
            </button>

            <a href="<?= site_url('estoque_itens') ?>" class="btn btn-default">
                ↩ Voltar
            </a>
        </div>

    </form>
</div>
