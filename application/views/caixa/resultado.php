<div class="container">

    <h3>🧾 Caixa — Comanda <?= $os->codigo_comanda ?></h3>

    <div class="alert alert-info">
        <strong>Cliente:</strong> <?= $os->nomeCliente ?? '-' ?><br>
        <strong>Status:</strong> <?= $os->status_os ?>
    </div>

    <!-- PRODUTOS -->
    <h4>Produtos</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Qtd</th>
                <th>Unitário</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $p): ?>
                <tr>
                    <td><?= $p->descricao ?></td>
                    <td><?= $p->quantidade ?></td>
                    <td>R$ <?= number_format($p->precoVenda, 2, ',', '.') ?></td>
                    <td>R$ <?= number_format($p->subTotal, 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- SERVIÇOS -->
    <h4>Serviços</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Serviço</th>
                <th>Qtd</th>
                <th>Unitário</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servicos as $s): 
                $qtd = $s->quantidade ?: 1;
                $sub = $s->preco * $qtd;
            ?>
                <tr>
                    <td><?= $s->nome ?></td>
                    <td><?= $qtd ?></td>
                    <td>R$ <?= number_format($s->preco, 2, ',', '.') ?></td>
                    <td>R$ <?= number_format($sub, 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <hr>

    <!-- TOTAIS -->
    <h4>Total da OS: R$ <?= number_format($totalOS, 2, ',', '.') ?></h4>
    <h4>Pago: R$ <?= number_format($totalPago, 2, ',', '.') ?></h4>
    <h3><strong>Saldo: R$ <?= number_format($saldo, 2, ',', '.') ?></strong></h3>

    <!-- HISTÓRICO DE PAGAMENTOS -->
    <?php if (!empty($pagamentos)): ?>
        <hr>
        <h5>Histórico de Pagamentos</h5>
        <ul>
            <?php foreach ($pagamentos as $p): ?>
                <li>
                    <?= strtoupper($p->forma_pgto) ?> —
                    R$ <?= number_format($p->valor, 2, ',', '.') ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <hr>

    <!-- FORM PAGAMENTO -->
    <?php if ($saldo > 0): ?>
        <form method="post" action="<?= site_url('caixa/pagar') ?>">
            <input type="hidden" name="idOs" value="<?= $os->idOs ?>">

            <div class="form-group">
                <label><strong>Valor pago</strong></label>
                <input type="text"
                       name="valor_pago"
                       class="form-control money"
                       placeholder="R$ 0,00"
                       required>
            </div>

            <div class="form-group">
                <label><strong>Forma de pagamento</strong></label>
                <select name="forma_pagamento" class="form-control" required>
                    <option value="">Selecione</option>
                    <option value="PIX">PIX</option>
                    <option value="DINHEIRO">Dinheiro</option>
                    <option value="CARTAO">Cartão</option>
                </select>
            </div>

            <br>

            <button type="submit" class="btn btn-success">
                💰 Registrar Pagamento
            </button>

            <a href="<?= site_url('caixa/cancelar') ?>" class="btn btn-secondary">
                Cancelar
            </a>
        </form>
    <?php else: ?>
        <div class="alert alert-success">
            ✅ Comanda totalmente paga.
        </div>
    <?php endif; ?>

</div>

<!-- MÁSCARA DE DINHEIRO -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $('.money').mask('000.000.000,00', {reverse: true});
</script>
