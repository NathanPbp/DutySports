<script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/funcoes.js"></script>

<style>
    #imgSenha {
        width: 18px;
        cursor: pointer;
    }

    .badgebox {
        opacity: 0;
    }

    .badgebox + .badge {
        text-indent: -999999px;
        width: 27px;
    }

    .badgebox:checked + .badge {
        text-indent: 0;
    }

    .control-group.error .help-inline {
        display: block;
        color: #b94a48;
    }

    .form-horizontal .controls {
        margin-left: 20px;
    }

    .form-horizontal .control-label {
        text-align: left;
        padding-top: 15px;
    }

    .nopadding {
        padding: 0 20px !important;
        margin-right: 20px;
    }

    .widget-title h5 {
        padding-bottom: 30px;
        font-size: 2em;
        font-weight: 500;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-user"></i></span>
                <h5>Cadastro de Cliente</h5>
            </div>

            <form action="<?php echo current_url(); ?>" id="formCliente" method="post" class="form-horizontal">

                <div class="widget-content nopadding">
                    <div class="span6">

                        <div class="control-group">
                            <label class="control-label">CPF/CNPJ *</label>
                            <div class="controls">
                                <input type="text" id="documento" name="documento" class="cpfcnpj" />
                                <button type="button" class="btn btn-xs" id="buscar_info_cnpj">Buscar(CNPJ)</button>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Nome/Razão Social *</label>
                            <div class="controls">
                                <input type="text" id="nomeCliente" name="nomeCliente" />
                            </div>
                        </div>
                        <div class="control-group">
    <label for="nomeFantasia" class="control-label">Nome Fantasia</label>
    <div class="controls">
        <input id="nomeFantasia" type="text" name="nomeFantasia" value="<?php echo set_value('nomeFantasia'); ?>" />
    </div>
</div>


                        <div class="control-group">
                            <label class="control-label">Telefone *</label>
                            <div class="controls">
                                <input type="text" id="telefone" name="telefone" />
                            </div>
                        </div>
                        
                        <div class="control-group">
    

<div class="control-group">
    <label class="control-label">Celular</label>
    <div class="controls">
        <input type="text" id="celular" name="celular" />
    </div>
</div>
<div class="control-group">
    <label for="contato" class="control-label">Origem do Cliente</label>
    <div class="controls">
        <select name="contato" id="contato">
            <option value="">Selecione...</option>
            <option value="Instagram">Instagram</option>
            <option value="Google">Google</option>
            <option value="WhatsApp">WhatsApp</option>
            <option value="Indicação">Indicação</option>
            <option value="Loja física">Loja física</option>
            <option value="Outro">Outro</option>
        </select>
    </div>
</div>
</div>

                        <div class="control-group">
                            <label class="control-label">Email</label>
                            <div class="controls">
                                <input type="text" name="email" />
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Senha</label>
                            <div class="controls">
                                <input type="password" id="senha" name="senha" />
                                <img id="imgSenha" src="<?php echo base_url() ?>assets/img/eye.svg">
                            </div>
                        </div>

                    </div>

                    <div class="span6">

                        <div class="control-group">
                            <label class="control-label">CEP</label>
                            <div class="controls">
                                <input type="text" id="cep" name="cep">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Rua</label>
                            <div class="controls">
                                <input type="text" id="rua" name="rua">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Número</label>
                            <div class="controls">
                                <input type="text" id="numero" name="numero">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Bairro</label>
                            <div class="controls">
                                <input type="text" id="bairro" name="bairro">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Cidade</label>
                            <div class="controls">
                                <input type="text" id="cidade" name="cidade">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Estado</label>
                            <div class="controls">
                                <select id="estado" name="estado">
                                    <option value="">Selecione...</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="form-actions" style="text-align:center">
                    <button type="submit" class="btn btn-success">Salvar</button>
                    <a href="<?php echo site_url('clientes'); ?>" class="btn btn-warning">Voltar</a>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>

<script>
$(document).ready(function () {

    // GARANTE que nenhum submit antigo interfira
    $('#formCliente').off('submit');

    $('#formCliente').validate({
        ignore: [],
        rules: {
            documento: {
                required: true
            },
            nomeCliente: {
                required: true
            },
            telefone: {
                required: true
            }
        },
        messages: {
            documento: 'Informe o CPF ou CNPJ.',
            nomeCliente: 'Informe o nome do cliente.',
            telefone: 'Informe o telefone.'
        },
        errorClass: 'help-inline',
        errorElement: 'span',

        highlight: function (element) {
            $(element).closest('.control-group').addClass('error');
        },

        unhighlight: function (element) {
            $(element).closest('.control-group').removeClass('error');
        },

        invalidHandler: function () {
            Swal.fire({
                icon: 'warning',
                title: 'Campos obrigatórios',
                text: 'CPF/CNPJ, Nome e Telefone são obrigatórios.'
            });
        },

        submitHandler: function (form, event) {
            event.preventDefault();

            // remove QUALQUER handler antigo
            $(form).off('submit');

            // envia de verdade
            form.submit();
        }
    });

});
</script>
<script>
$(document).ready(function () {

    const estados = [
        { sigla: 'AC', nome: 'Acre' },
        { sigla: 'AL', nome: 'Alagoas' },
        { sigla: 'AP', nome: 'Amapá' },
        { sigla: 'AM', nome: 'Amazonas' },
        { sigla: 'BA', nome: 'Bahia' },
        { sigla: 'CE', nome: 'Ceará' },
        { sigla: 'DF', nome: 'Distrito Federal' },
        { sigla: 'ES', nome: 'Espírito Santo' },
        { sigla: 'GO', nome: 'Goiás' },
        { sigla: 'MA', nome: 'Maranhão' },
        { sigla: 'MT', nome: 'Mato Grosso' },
        { sigla: 'MS', nome: 'Mato Grosso do Sul' },
        { sigla: 'MG', nome: 'Minas Gerais' },
        { sigla: 'PA', nome: 'Pará' },
        { sigla: 'PB', nome: 'Paraíba' },
        { sigla: 'PR', nome: 'Paraná' },
        { sigla: 'PE', nome: 'Pernambuco' },
        { sigla: 'PI', nome: 'Piauí' },
        { sigla: 'RJ', nome: 'Rio de Janeiro' },
        { sigla: 'RN', nome: 'Rio Grande do Norte' },
        { sigla: 'RS', nome: 'Rio Grande do Sul' },
        { sigla: 'RO', nome: 'Rondônia' },
        { sigla: 'RR', nome: 'Roraima' },
        { sigla: 'SC', nome: 'Santa Catarina' },
        { sigla: 'SP', nome: 'São Paulo' },
        { sigla: 'SE', nome: 'Sergipe' },
        { sigla: 'TO', nome: 'Tocantins' }
    ];

    const $estado = $('#estado');
    $estado.empty().append('<option value="">Selecione...</option>');

    estados.forEach(function (e) {
        $estado.append(`<option value="${e.sigla}">${e.nome}</option>`);
    });

});
</script>


