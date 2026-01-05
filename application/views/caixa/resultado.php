<?php
$comanda = $this->session->userdata('caixa_comanda');

// fallback absoluto (NUNCA deixa vazio)
$clienteNome = $comanda['cliente_nome']
    ?? ($os->nomeCliente ?? ($os->nome ?? '-'));
?>

<div class="alert alert-info">
    <strong>Cliente:</strong> <?= htmlspecialchars($clienteNome) ?><br>

    <strong>Status OS:</strong> <?= $os->status ?><br>
    <strong>Status Pedido:</strong> <?= $os->status_os ?><br>

    <strong>Venda vinculada:</strong>
    #<?= $venda->idVendas ?> (<?= $venda->status ?>)
</div>



    <h3 style="margin-top:10px;">
    🧾 Caixa — Comanda <?= $os->codigo_comanda ?>
    <small style="display:block;margin-top:6px;color:#555;">
        Cliente: <strong><?= htmlspecialchars($clienteNome) ?></strong>
    </small>
</h3>



    <?php $isRelatorio = ($this->uri->segment(2) === 'relatorio'); ?>
    <ul class="nav nav-tabs" style="margin: 10px 0 15px;">
        <li class="<?= $isRelatorio ? '' : 'active' ?>">
            <a href="<?= site_url('caixa') ?>">Caixa</a>
        </li>
        <li class="<?= $isRelatorio ? 'active' : '' ?>">
            <a href="<?= site_url('caixa/relatorio') ?>">Relatório de Caixa</a>
        </li>
    </ul>

    <?php $comanda = $this->session->userdata('caixa_comanda'); ?>

<?php if ($comanda): ?>
<div class="alert alert-info">
    <strong>Cliente:</strong> <?= htmlspecialchars($comanda['cliente_nome']) ?><br>
    <strong>Status OS:</strong> <?= $os->status ?><br>
    <strong>Status Pedido:</strong> <?= $os->status_os ?><br>
    <strong>Venda vinculada:</strong>
        #<?= $venda->idVendas ?> (<?= $venda->status ?>)
</div>
<?php endif; ?>


    <!-- PRODUTOS -->
    <h4>Produtos</h4>
    <div style="overflow:auto;">
        <table class="table table-bordered table-condensed" style="min-width:750px;">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th style="width:70px;">Qtd</th>
                    <th style="width:120px;">Unitário</th>
                    <th style="width:130px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($produtos)): ?>
                <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td><?= $p->descricao ?></td>
                        <td><?= $p->quantidade ?></td>
                        <td>R$ <?= number_format((float)($p->precoVenda ?? $p->preco ?? 0), 2, ',', '.') ?></td>
                        <td>R$ <?= number_format((float)$p->subTotal, 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Nenhum produto.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- SERVIÇOS -->
    <h4>Serviços</h4>
    <div style="overflow:auto;">
        <table class="table table-bordered table-condensed" style="min-width:750px;">
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th style="width:70px;">Qtd</th>
                    <th style="width:120px;">Unitário</th>
                    <th style="width:130px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($servicos)): ?>
                <?php foreach ($servicos as $s):
                    $qtd = (isset($s->quantidade) && (int)$s->quantidade > 0) ? (int)$s->quantidade : 1;
                    $preco = isset($s->preco) ? (float)$s->preco : 0.0;
                    $sub = (isset($s->subTotal) && $s->subTotal !== null && $s->subTotal !== '') ? (float)$s->subTotal : ($preco * $qtd);
                ?>
                    <tr>
                        <td><?= $s->nome ?></td>
                        <td><?= $qtd ?></td>
                        <td>R$ <?= number_format($preco, 2, ',', '.') ?></td>
                        <td>R$ <?= number_format($sub, 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Nenhum serviço.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <hr>

    <!-- RESUMO -->
    <div class="row-fluid">
        <div class="span12" style="text-align:right;">
            <h4 style="margin:0;">Total da OS: <strong>R$ <?= number_format((float)$totalOS, 2, ',', '.') ?></strong></h4>
            <h4 style="margin:0;">Pago: <strong>R$ <?= number_format((float)$totalPago, 2, ',', '.') ?></strong></h4>
            <h3 style="margin-top:8px;">
                Saldo: <strong>R$ <?= number_format((float)$saldo, 2, ',', '.') ?></strong>
            </h3>
        </div>
    </div>

    <?php if (!empty($porForma)): ?>
        <div class="alert alert-warning">
            <strong>Pagamentos por forma:</strong><br>
            <?php foreach ($porForma as $forma => $valor): ?>
                • <?= htmlspecialchars($forma) ?>: <strong>R$ <?= number_format((float)$valor, 2, ',', '.') ?></strong><br>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($totalOS > 0 && $saldo <= 0): ?>
        <div class="alert alert-success">
            ✅ Comanda totalmente paga e faturada.
        </div>
    <?php endif; ?>

    <!-- LISTA DE PAGAMENTOS -->
    <h4>Histórico de Pagamentos</h4>
    <div style="overflow:auto;">
        <table class="table table-bordered table-condensed" style="min-width:750px;">
            <thead>
                <tr>
                    <th style="width:140px;">Data</th>
                    <th>Descrição</th>
                    <th style="width:140px;">Forma</th>
                    <th style="width:140px;">Valor</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($pagamentos)): ?>
                <?php foreach ($pagamentos as $pg): ?>
                    <tr><td><?= date('d/m/Y H:i', strtotime($pg->data_pagamento)) ?></td>
                        <td><?= $pg->descricao ?></td>
                        <td><?= $pg->forma_pgto ?: '-' ?></td>
                        <td>R$ <?= number_format((float)$pg->valor, 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Nenhum pagamento registrado ainda.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <hr>

    <!-- FORM PAGAMENTO (permite várias entradas) -->
    <form method="post" action="<?= site_url('caixa/pagar') ?>">
        <input type="hidden" name="idOs" value="<?= $os->idOs ?>">

        <div class="row-fluid">
            <div class="span4">
                <label><strong>Valor pago</strong></label>
                <input type="text" name="valor_pago" id="valor_pago" class="span12" placeholder="0,00" required>
            </div>


            <div class="span4">
                <label><strong>Forma de pagamento</strong></label>
                <select name="forma_pagamento" class="span12" required>
                    <option value="">Selecione</option>
                    <option value="PIX">PIX</option>
                    <option value="DINHEIRO">Dinheiro</option>
                    <option value="CARTAO">Cartão</option>
                </select>
            </div>

            <div class="span4" style="margin-top:24px;">
                <button type="submit" class="btn btn-success">
                    💰 Registrar Pagamento
                </button>

                <a href="<?= site_url('caixa/cancelar') ?>" class="btn btn-secondary">
                    Voltar
                </a>
            </div>
        </div>
    </form>

</div>


<script>
/**
 * Máscara simples BRL: 1234 -> 12,34 / 123456 -> 1.234,56
 * Digita só número e ele formata.
 */
(function(){
    var el = document.getElementById('valor_pago');
    if (!el) return;

    function formatBRL(v){
        v = (v || '').replace(/\D/g,'');
        if (v.length === 0) return '0,00';
        while (v.length < 3) v = '0' + v;
        var cents = v.slice(-2);
        var ints = v.slice(0, -2);
        ints = ints.replace(/^0+(?=\d)/,'');
        ints = ints.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        return (ints ? ints : '0') + ',' + cents;
    }

    el.addEventListener('input', function(){
        el.value = formatBRL(el.value);
    });

    // inicia bonitinho
    el.value = '0,00';
})();
</script>
