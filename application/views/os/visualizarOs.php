<link href="<?= base_url('assets/css/custom.css'); ?>" rel="stylesheet">
<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: 10px 0 0">
                <div class="buttons">
                    <?php if ($editavel) {
                        echo '<a title="Editar OS" class="button btn btn-mini btn-success" href="' . base_url() . 'index.php/os/editar/' . $result->idOs . '">
                            <span class="button__icon"><i class="bx bx-edit"></i> </span> <span class="button__text">Editar</span>
                        </a>';
                    } ?>

                   <div class="button-container">
    <a target="_blank" title="Imprimir Ordem de Serviço" class="button btn btn-mini btn-inverse">
        <span class="button__icon"><i class="bx bx-printer"></i></span>
        <span class="button__text">Imprimir</span>
    </a>

    <div class="cascading-buttons">

        <!-- OS - Papel A4 -->
        <a target="_blank"
           title="Impressão em Papel A4"
           class="button btn btn-mini btn-inverse"
           href="<?php echo site_url() ?>/os/imprimir/<?php echo $result->idOs; ?>">
            <span class="button__icon"><i class='bx bx-file'></i></span>
            <span class="button__text">Papel A4</span>
        </a>

        <!-- OS - Cupom -->
        <a target="_blank"
           title="Impressão Cupom Não Fiscal"
           class="button btn btn-mini btn-inverse"
           href="<?php echo site_url() ?>/os/imprimirTermica/<?php echo $result->idOs; ?>">
            <span class="button__icon"><i class='bx bx-receipt'></i></span>
            <span class="button__text">Cupom 80mm</span>
        </a>

        <!-- PRODUÇÃO - NOVO -->
        <a target="_blank"
           title="Imprimir Ficha de Produção"
           class="button btn btn-mini btn-primary"
           href="<?php echo site_url() ?>/os/imprimirProducao/<?php echo $result->idOs; ?>">
            <span class="button__icon"><i class='bx bx-paint'></i></span>
            <span class="button__text">Produção</span>
        </a>


    </div>
</div>


                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
                        $this->load->model('os_model');
                        $zapnumber = preg_replace("/[^0-9]/", "", $result->celular_cliente);
                        $troca = [$result->nomeCliente, $result->idOs, $result->status, 'R$ ' . ($result->desconto != 0 && $result->valor_desconto != 0 ? number_format($result->valor_desconto, 2, ',', '.') : number_format($totalProdutos + $totalServico, 2, ',', '.')), strip_tags($result->descricaoProduto), ($emitente ? $emitente->nome : ''), ($emitente ? $emitente->telefone : ''), strip_tags($result->observacoes), strip_tags($result->defeito), strip_tags($result->laudoTecnico), date('d/m/Y', strtotime($result->dataFinal)), date('d/m/Y', strtotime($result->dataInicial)), $result->garantia . ' dias'];
                        $texto_de_notificacao = $this->os_model->criarTextoWhats($texto_de_notificacao, $troca);
                        if (!empty($zapnumber)) {
                            echo '<a title="Enviar Por WhatsApp" class="button btn btn-mini btn-success" id="enviarWhatsApp" target="_blank" href="https://api.whatsapp.com/send?phone=55' . $zapnumber . '&text=' . $texto_de_notificacao . '">
                                <span class="button__icon"><i class="bx bxl-whatsapp"></i></span> <span class="button__text">WhatsApp</span>
                            </a>';
                        }
                    } ?>

                    <a title="Enviar OS por E-mail" class="button btn btn-mini btn-warning" href="<?php echo site_url() ?>/os/enviar_email/<?php echo $result->idOs; ?>">
                        <span class="button__icon"><i class="bx bx-envelope"></i></span> <span class="button__text">via E-mail</span>
                    </a>

                    <a href="#modal-gerar-pagamento" id="btn-forma-pagamento" role="button" data-toggle="modal" class="button btn btn-mini btn-primary">
                        <span class="button__icon"><i class='bx bx-dollar'></i></span><span class="button__text">Gerar Pagamento</span>
                    </a>

                    <?php if ($qrCode): ?>
                        <a href="#modal-pix" id="btn-pix" role="button" data-toggle="modal" class="button btn btn-mini btn-info">
                            <span class="button__icon"><i class='bx bx-qr'></i></span><span class="button__text">Chave PIX</span>
                        </a>
                    <?php endif ?>
                </div>
            </div>

            <ul class="nav nav-tabs" style="margin-bottom:15px">
    <li class="active">
        <a href="#tab-os" data-toggle="tab">
            <i class="bx bx-file"></i> OS
        </a>
    </li>
    <li>
        <a href="#tab-producao" data-toggle="tab">
            <i class="bx bx-buildings"></i> Produção
        </a>
    </li>
</ul>

<div class="tab-content">


           <div class="tab-pane active" id="tab-os">
           <div class="widget-content" id="printOs">

                <div class="invoice-content">
                    <div class="invoice-head" style="margin-bottom: 0; margin-top:-30px">
                        <table class="table table-condensed">
                            <tbody>
                                <?php if ($emitente == null) { ?>
                                    <tr>
                                        <td colspan="3" class="alert">Você precisa configurar os dados do emitente. >>><a href="<?php echo base_url(); ?>index.php/mapos/emitente">Configurar <<< </a></td>
                                    </tr>
                                <?php } ?>
                                <h3><i class='bx bx-file'></i> Ordem de Serviço #<?php echo sprintf('%04d', $result->idOs) ?></h3>
                            </tbody>
                        </table>
                        <div style="margin:10px 0">
                      <strong>COMANDA:</strong>
                         <span style="font-size:22px; font-weight:bold; color:#198754;">
                              <?php echo $result->codigo_comanda ?? '—'; ?>
                                    </span>
                                            <br>

                                            <strong>Status do Pedido:</strong>
                                              <?php echo $result->status_os ?? '—'; ?>
                                                </div>

                        <table class="table table-condensend">
                            <tbody>
                                <tr>
                                    <td style="width: 60%; padding-left: 0">
                                        <span>
                                            <h5><b>CLIENTE</b></h5>
                                            <span><i class='bx bxs-business'></i> <b><?php echo $result->nomeCliente ?></b></span><br />
                                            <?php if (!empty($result->celular_cliente) || !empty($result->telefone_cliente) || !empty($result->contato_cliente)): ?>
                                                <span><i class='bx bxs-phone'></i>
                                                    <?= !empty($result->contato_cliente) ? $result->contato_cliente . ' ' : "" ?>
                                                    <?php if ($result->celular_cliente == $result->telefone_cliente) { ?>
                                                        <?= $result->celular_cliente ?>
                                                    <?php } else { ?>
                                                        <?= !empty($result->telefone_cliente) ? $result->telefone_cliente : "" ?>
                                                        <?= !empty($result->celular_cliente) && !empty($result->telefone_cliente) ? ' / ' : "" ?>
                                                        <?= !empty($result->celular_cliente) ? $result->celular_cliente : "" ?>
                                                    <?php } ?>
                                                </span></br>
                                            <?php endif; ?>
                                            <?php
                                            $retorno_end = array_filter([$result->rua, $result->numero, $result->complemento, $result->bairro . ' - ']);
                                            $endereco = implode(', ', $retorno_end);
                                            echo '<i class="fas fa-map-marker-alt"></i> ';
                                            if (!empty($endereco)) {
                                                echo $endereco;
                                            }
                                            if (!empty($result->cidade) || !empty($result->estado) || !empty($result->cep)) {
                                                echo "<span> {$result->cep}, {$result->cidade}/{$result->estado}</span><br>";
                                            }
                                            ?>
                                            <?php if (!empty($result->email)): ?>
                                                <span><i class="fas fa-envelope"></i>
                                                    <?php echo $result->email ?></span><br>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td style="width: 40%; padding-left: 0">
                                        <ul>
                                            <li>
                                                <span>
                                                    <h5><b>RESPONSÁVEL</b></h5>
                                                </span>
                                                <span><b><i class="fas fa-user"></i>
                                                        <?php echo $result->nome ?></b></span><br />
                                                <span><i class="fas fa-phone"></i>
                                                    <?php echo $result->telefone_usuario ?></span><br />
                                                <span><i class="fas fa-envelope"></i>
                                                    <?php echo $result->email_usuario ?></span>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>

                    <div style="margin-top: 0; padding-top: 0">
                        <table class="table table-condensed">
                            <tbody>
                                <?php if ($result->dataInicial != null) { ?>
                                    <tr>
                                        <td>
                                            <b>STATUS OS: </b><br>
                                            <?php echo $result->status ?>
                                        </td>

                                        <td>
                                            <b>DATA INICIAL: </b><br>
                                            <?php echo date('d/m/Y', strtotime($result->dataInicial)); ?>
                                        </td>

                                        <td>
                                            <b>DATA FINAL: </b><br>
                                            <?php echo $result->dataFinal ? date('d/m/Y', strtotime($result->dataFinal)) : ''; ?>
                                        </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <?php if ($anotacoes != null) { ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Anotação</th>
                                        <th>Data/Hora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($anotacoes as $a) {
                                        echo '<tr>';
                                        echo '<td>' . $a->anotacao . '</td>';
                                        echo '<td>' . date('d/m/Y H:i:s', strtotime($a->data_hora)) . '</td>';
                                        echo '</tr>';
                                    }
                                    if (!$anotacoes) {
                                        echo '<tr><td colspan="2">Nenhuma anotação cadastrada</td></tr>';
                                    } ?>
                                </tbody>
                            </table>
                        <?php } ?>

                        <?php if ($anexos != null) { ?>
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th>Anexo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <th colspan="5">
                                        <?php foreach ($anexos as $a) {
                                            if ($a->thumb == null) {
                                                $thumb = base_url() . 'assets/img/icon-file.png';
                                                $link = base_url() . 'assets/img/icon-file.png';
                                            } else {
                                                $thumb = $a->url . '/thumbs/' . $a->thumb;
                                                $link = $a->url . '/' . $a->anexo;
                                            }
                                            echo '<div class="span3" style="min-height: 150px; margin-left: 0"><a style="min-height: 150px;" href="#modal-anexo" imagem="' . $a->idAnexos . '" link="' . $link . '" role="button" class="btn anexo span12" data-toggle="modal"><img src="' . $thumb . '" alt=""></a></div>';
                                        } ?>
                                    </th>
                                </tbody>
                            </table>
                        <?php } ?>

                        <?php if ($produtos != null) { ?>
                            <br />
                            <table class="table table-bordered table-condensed" id="tblProdutos">
                                <thead>
                                    <tr>
                                        <th>PRODUTO</th>
                                        <th>QTD</th>
                                        <th>UNT</th>
                                        <th>SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($produtos as $p) {
                                        echo '<tr>';
                                        echo '<td>' . $p->descricao . '</td>';
                                        echo '<td>' . $p->quantidade . '</td>';
                                        echo '<td>R$ ' . $p->preco ?: $p->precoVenda . '</td>';
                                        echo '<td>R$ ' . number_format($p->subTotal, 2, ',', '.') . '</td>';
                                        echo '</tr>';
                                    } ?>
                                    <tr>
                                        <td></td>
                                        <td colspan="2" style="text-align: right"><strong>TOTAL:</strong></td>
                                        <td><strong>R$ <?php echo number_format($totalProdutos, 2, ',', '.'); ?></strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php } ?>
                        <?php if ($servicos != null) { ?>
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th>SERVIÇO</th>
                                        <th>QTD</th>
                                        <th>UNT</th>
                                        <th>SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php setlocale(LC_MONETARY, 'en_US');
                                    foreach ($servicos as $s) {
                                        $preco = $s->preco ?: $s->precoVenda;
                                        $subtotal = $preco * ($s->quantidade ?: 1);
                                        echo '<tr>';
                                        echo '<td>' . $s->nome . '</td>';
                                        echo '<td>' . ($s->quantidade ?: 1) . '</td>';
                                        echo '<td>R$ ' . $preco . '</td>';
                                        echo '<td>R$ ' . number_format($subtotal, 2, ',', '.') . '</td>';
                                        echo '</tr>';
                                    } ?>
                                    <tr>
                                        <td colspan="3" style="text-align: right"><strong>TOTAL:</strong></td>
                                        <td><strong>R$ <?php echo number_format($totalServico, 2, ',', '.'); ?></strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php } ?>
                        <table class="table table-bordered table-condensed">
                            <?php if ($totalProdutos != 0 || $totalServico != 0) {
                                if ($result->valor_desconto != 0) {
                                    echo "<td>";
                                    echo "<h4 style='text-align: right'>SUBTOTAL: R$ " . number_format($totalProdutos + $totalServico, 2, ',', '.') . "</h4>";
                                    echo $result->valor_desconto != 0 ? "<h4 style='text-align: right'>DESCONTO: R$ " . number_format($result->valor_desconto != 0 ? $result->valor_desconto - ($totalProdutos + $totalServico) : 0.00, 2, ',', '.') . "</h4>" : "";
                                    echo "<h4 style='text-align: right'>TOTAL: R$ " . number_format($result->valor_desconto, 2, ',', '.') . "</h4>";
                                    echo "</td>";
                                } else {
                                    echo "<td>";
                                    echo "<h4 style='text-align: right'>TOTAL: R$ " . number_format($totalProdutos + $totalServico, 2, ',', '.') . "</h4>";
                                    echo "</td>";
                                }
                            } ?>
                     </table>
                    </div>
                </div>
            </div>
       </div> <!-- FIM tab-os --> 
<div class="tab-pane" id="tab-producao">
    <div class="widget-content">

        <form action="<?= site_url('os/salvarProducao') ?>"
              method="post"
              enctype="multipart/form-data">

            <input type="hidden" name="os_id" value="<?= $result->idOs ?>">

            <!-- =========================
                 ARTE + INFO TÉCNICA
            ========================== -->
            <fieldset>
                <legend><i class="bx bx-image"></i> Arte do Cliente</legend>

                <?php if (!empty($producao->arte_imagem)): ?>
                    <img src="<?= base_url($producao->arte_imagem) ?>"
                         style="max-width:300px;border:1px solid #ccc;margin-bottom:10px">
                <?php endif; ?>

                <input type="file" name="arte_imagem" accept="image/*">
            </fieldset>

            <hr>

            <fieldset>
                <legend><i class="bx bx-info-circle"></i> Informações Técnicas</legend>

                <div class="row-fluid">
                    <div class="span3">
                        <label>Tecido</label>
                        <input type="text" name="tecido" class="span12"
                               value="<?= $producao->tecido ?? '' ?>">
                    </div>

                    <div class="span3">
                        <label>Gola</label>
                        <input type="text" name="gola" class="span12"
                               value="<?= $producao->gola ?? '' ?>">
                    </div>

                    <div class="span3">
                        <label>Técnica</label>
                        <input type="text" name="tecnica" class="span12"
                               value="<?= $producao->tecnica ?? '' ?>">
                    </div>

                    <div class="span3">
                        <label>Símbolo</label>
                        <input type="text" name="simbolo" class="span12"
                               value="<?= $producao->simbolo ?? '' ?>">
                    </div>
                </div>
            </fieldset>

            <hr>

            <!-- =========================
                 OBSERVAÇÃO
            ========================== -->
            <fieldset>
                <legend><i class="bx bx-note"></i> Observações</legend>
                <textarea name="observacao"
                          class="span12"
                          rows="3"><?= $producao->observacao ?? '' ?></textarea>
            </fieldset>

            <hr>

            <!-- =========================
                 GRADE
            ========================== -->
            <fieldset>
<!-- =========================
     IMPORTAÇÃO VIA WHATSAPP
========================== -->
<div class="well" style="margin-bottom:15px">
    <label><strong>📥 Colar grade recebida do WhatsApp</strong></label>

    <textarea id="gradeWhatsapp"
              class="span12"
              rows="6"
              placeholder="Cole aqui a mensagem do WhatsApp no formato:
QTD-NOME-SUPERIOR-INFERIOR-Nº-ADICIONAL-MODELO"></textarea>

    <button type="button"
            class="btn btn-success btn-mini"
            id="btnConverterWhatsapp"
            style="margin-top:8px">
        <i class="bx bx-import"></i> Converter para grade
    </button>
</div>
<!-- =========================
     IMPORTAÇÃO VIA WHATSAPP
========================== -->
<div style="margin-bottom:12px;">
    <a href="<?= site_url('exportacaoproducao/excel/' . $result->idOs) ?>"
   class="btn btn-success btn-mini"
   onclick="return confirm('Deseja exportar a grade de produção em Excel?')">
    <i class="bx bx-spreadsheet"></i> Exportar Grade (Excel)
</a>

</div>


 
                <legend><i class="bx bx-table"></i> Grade de Produção</legend>

                <div style="margin-bottom:10px">
                    <input type="number"
                           id="qtdLinhas"
                           min="1"
                           value="1"
                           style="width:80px">

                    <button type="button"
                            class="btn btn-info btn-mini"
                            id="btnAddLinhas">
                        <i class="bx bx-plus"></i> Adicionar linhas
                    </button>
                </div>

                <table class="table table-bordered table-condensed" id="tabela-grade">
    <thead>
        <tr>
            <th>QTD</th>
            <th>NOME</th>
            <th>SUPERIOR</th>
            <th>INFERIOR</th>
            <th>Nº</th>
            <th>ADICIONAL</th>
            <th>UNISSEX</th>
            <th>Ação</th>

        </tr>
    </thead>
    <tbody>
        <?php
        $linhas = !empty($producaoGrade)
            ? $producaoGrade
            : [ [] ];

        foreach ($linhas as $i => $linha):
        ?>
        <tr>
    <td>
        <input type="number"
               name="grade[<?= $i ?>][quantidade]"
               class="span12"
               min="0"
               value="<?= $linha['quantidade'] ?? '' ?>">
    </td>

    <td>
        <input type="text"
               name="grade[<?= $i ?>][nome]"
               class="span12"
               value="<?= $linha['nome'] ?? '' ?>">
    </td>

    <td>
        <input type="text"
               name="grade[<?= $i ?>][superior]"
               class="span12"
               placeholder="P / M / G / GG"
               value="<?= $linha['superior'] ?? '' ?>">
    </td>

    <td>
        <input type="text"
               name="grade[<?= $i ?>][inferior]"
               class="span12"
               placeholder="36 / 38 / 40"
               value="<?= $linha['inferior'] ?? '' ?>">
    </td>

    <td>
        <input type="text"
               name="grade[<?= $i ?>][numero]"
               class="span12"
               value="<?= $linha['numero'] ?? '' ?>">
    </td>

    <td>
        <input type="text"
               name="grade[<?= $i ?>][adicional]"
               class="span12"
               value="<?= $linha['adicional'] ?? '' ?>">
    </td>

    <td>
        <select name="grade[<?= $i ?>][modelo]" class="span12">
            <?php foreach (['UNISSEX','FEM','MASC','INFANTIL'] as $m): ?>
                <option value="<?= $m ?>"
                    <?= ($linha['modelo'] ?? '') === $m ? 'selected' : '' ?>>
                    <?= $m ?>
                </option>
            <?php endforeach; ?>
        </select>
    </td>

    <td>
        <button type="button"
                class="btn btn-danger btn-mini remover-linha">
            X
        </button>
    </td>
</tr>
        <?php endforeach; ?>
    </tbody>
</table>

                
            </fieldset>

            <hr>

            <div style="text-align:right">
                <button type="submit" class="btn btn-success">
                    <i class="bx bx-save"></i> Salvar Ficha de Produção
                </button>
            </div>
            

        </form>
    </div>
</div>




<?= $modalGerarPagamento ?>

<!-- Modal visualizar anexo -->
<div id="modal-anexo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h3 id="myModalLabel">Visualizar Anexo</h3>
    </div>
    <div class="modal-body">
        <div class="span12" id="div-visualizar-anexo" style="text-align: center">
            <div class='progress progress-info progress-striped active'>
                <div class='bar' style='width: 100%'></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
        <a href="" id-imagem="" class="btn btn-inverse" id="download">Download</a>
        <a href="" link="" class="btn btn-danger" id="excluir-anexo">Excluir Anexo</a>
    </div>
</div>

<!-- Modal PIX -->
<div id="modal-pix" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h3 id="myModalLabel">Pagamento via PIX</h3>
    </div>
    <div class="modal-body">
        <div class="span12" id="div-pix" style="text-align: center">
            <td style="width: 15%; padding: 0;text-align:center;">
                <img src="<?php echo base_url(); ?>assets/img/logo_pix.png" alt="QR Code de Pagamento" /></br>
                <img id="qrCodeImage" width="50%" src="<?= $qrCode ?>" alt="QR Code de Pagamento" /></br>
                <?php echo '<span>Chave PIX: ' . $chaveFormatada . '</span>'; ?></br>
                <?php if ($totalProdutos != 0 || $totalServico != 0) {
                    if ($result->valor_desconto != 0) {
                        echo "Valor Total: R$ " . number_format($result->valor_desconto, 2, ',', '.');
                    } else {
                        echo "Valor Total: R$ " . number_format($totalProdutos + $totalServico, 2, ',', '.');
                    }
                } ?>
            </td>
        </div>
    </div>
    <div class="modal-footer">
        <?php if (!empty($zapnumber)) {
            echo "<button id='pixWhatsApp' class='btn btn-success' data-dismiss='modal' aria-hidden='true' style='color: #FFF'><i class='bx bxl-whatsapp'></i> WhatsApp</button>";
        } ?>
        <button class="btn btn-primary" id="copyButton" style="margin:5px; color: #FFF"><i class="fas fa-copy"></i> Copia e Cola</button>
        <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true" style="color: #FFF">Fechar</button>
    </div>
</div>
<script src="https://cdn.rawgit.com/cozmo/jsQR/master/dist/jsQR.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '.anexo', function(event) {
            event.preventDefault();
            var link = $(this).attr('link');
            var id = $(this).attr('imagem');
            var url = '<?php echo base_url(); ?>index.php/os/excluirAnexo/';
            $("#div-visualizar-anexo").html('<img src="' + link + '" alt="">');
            $("#excluir-anexo").attr('link', url + id);
            $("#download").attr('href', "<?php echo base_url(); ?>index.php/os/downloadanexo/" + id);

        });

        $(document).on('click', '#excluir-anexo', function(event) {
            event.preventDefault();

            var link = $(this).attr('link');
            var idOS = "<?php echo $result->idOs; ?>"

            $('#modal-anexo').modal('hide');
            $("#divAnexos").html("<div class='progress progress-info progress-striped active'><div class='bar' style='width: 100%'></div></div>");

            $.ajax({
                type: "POST",
                url: link,
                dataType: 'json',
                data: "idOs=" + idOS,
                success: function(data) {
                    if (data.result == true) {
                        $("#divAnexos").load("<?php echo current_url(); ?> #divAnexos");
                    } else {
                        swal({
                            type: "error",
                            title: "Atenção",
                            text: data.mensagem
                        });
                    }
                }
            });
        });
    });

    $('#copyButton').on('click', function() {
        var $qrCodeImage = $('#qrCodeImage');
        var canvas = document.createElement('canvas');
        canvas.width = $qrCodeImage.width();
        canvas.height = $qrCodeImage.height();
        var context = canvas.getContext('2d');
        context.drawImage($qrCodeImage[0], 0, 0, $qrCodeImage.width(), $qrCodeImage.height());
        var imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        var code = jsQR(imageData.data, imageData.width, imageData.height);
        if (code) {
            navigator.clipboard.writeText(code.data).then(function() {
                $('#modal-pix').modal('hide');
                swal({
                    type: "success",
                    title: "Sucesso!",
                    text: "QR Code copiado com sucesso: " + code.data,
                    icon: "success",
                    timer: 3000,
                    showConfirmButton: false,
                });

            }).catch(function(err) {
                swal({
                    type: "error",
                    title: "Atenção",
                    text: "Erro ao copiar QR Code: ",
                    err
                });
            });
        } else {
            swal({
                type: "error",
                title: "Atenção",
                text: "Não foi possível decodificar o QR Code.",
            });
        }
    });

    $('#pixWhatsApp').on('click', function() {
        var $qrCodeImage = $('#qrCodeImage');
        var canvas = document.createElement('canvas');
        canvas.width = $qrCodeImage.width();
        canvas.height = $qrCodeImage.height();
        var context = canvas.getContext('2d');
        context.drawImage($qrCodeImage[0], 0, 0, $qrCodeImage.width(), $qrCodeImage.height());
        var imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        var code = jsQR(imageData.data, imageData.width, imageData.height);
        if (code) {
            var whatsappLink = 'https://api.whatsapp.com/send?phone=55' + <?= isset($zapnumber) ? $zapnumber : "" ?> + '&text=' + code.data;
            window.open(whatsappLink, '_blank');
        } else {
            swal({
                type: "error",
                title: "Atenção",
                text: "Não foi possível decodificar o QR Code.",
            });
        }
    });
</script>

<script type="text/javascript">
let indexLinha = <?= isset($linhas) ? count($linhas) : 0 ?>;

function novaLinha() {
    return `
    <tr>
        <td><input type="number" name="grade[${indexLinha}][quantidade]" class="span12" min="0"></td>
        <td><input type="text" name="grade[${indexLinha}][nome]" class="span12"></td>
        <td><input type="text" name="grade[${indexLinha}][superior]" class="span12"></td>
        <td><input type="text" name="grade[${indexLinha}][inferior]" class="span12"></td>
        <td><input type="text" name="grade[${indexLinha}][numero]" class="span12"></td>
        <td><input type="text" name="grade[${indexLinha}][adicional]" class="span12"></td>
        <td>
            <select name="grade[${indexLinha}][modelo]" class="span12">
                <option value="UNISSEX">UNISSEX</option>
                <option value="FEM">FEM</option>
                <option value="MASC">MASC</option>
                <option value="INFANTIL">INFANTIL</option>
            </select>
        </td>
        <td><button type="button" class="btn btn-mini btn-danger remover-linha">X</button></td>
    </tr>`;
}

/* Adicionar linhas manualmente */
document.getElementById('btnAddLinhas').addEventListener('click', function () {
    let qtd = parseInt(document.getElementById('qtdLinhas').value) || 1;
    for (let i = 0; i < qtd; i++) {
        document.querySelector('#tabela-grade tbody')
            .insertAdjacentHTML('beforeend', novaLinha());
        indexLinha++;
    }
});

/* Remover linha */
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remover-linha')) {
        e.target.closest('tr').remove();
    }
});

/* Converter WhatsApp */
document.getElementById('btnConverterWhatsapp').addEventListener('click', function () {

    const texto = document.getElementById('gradeWhatsapp').value.trim();
    if (!texto) return alert('Cole a mensagem do WhatsApp.');

    texto.split('\n').forEach(linha => {
        linha = linha.trim();
        if (!linha || linha.toUpperCase().includes('QTD')) return;

        const p = linha.split('-');
        if (p.length < 7) return;

        document.querySelector('#tabela-grade tbody')
            .insertAdjacentHTML('beforeend', novaLinha());

        const row = document.querySelectorAll('#tabela-grade tbody tr')[indexLinha];
        const n = v => (v === '*' ? '' : v.trim());

        row.querySelector(`[name="grade[${indexLinha}][quantidade]"]`).value = n(p[0]);
        row.querySelector(`[name="grade[${indexLinha}][nome]"]`).value = n(p[1]);
        row.querySelector(`[name="grade[${indexLinha}][superior]"]`).value = n(p[2]);
        row.querySelector(`[name="grade[${indexLinha}][inferior]"]`).value = n(p[3]);
        row.querySelector(`[name="grade[${indexLinha}][numero]"]`).value = n(p[4]);
        row.querySelector(`[name="grade[${indexLinha}][adicional]"]`).value = n(p[5]);

        const modelo = n(p[6]).toUpperCase();
        const select = row.querySelector(`[name="grade[${indexLinha}][modelo]"]`);
        if ([...select.options].some(o => o.value === modelo)) {
            select.value = modelo;
        }

        indexLinha++;
    });

    document.getElementById('gradeWhatsapp').value = '';
});
</script>







