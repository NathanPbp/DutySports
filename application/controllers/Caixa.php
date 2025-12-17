<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Caixa extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->permission->checkPermission(
            $this->session->userdata('permissao'),
            'vCaixa'
        )) {
            $this->session->set_flashdata(
                'error',
                'Você não tem permissão para acessar o Caixa.'
            );
            redirect(base_url());
        }

        $this->load->model('os_model');
        $this->data['menuCaixa'] = 'Caixa';
    }

    /* =========================
     * TELA INICIAL
     * ========================= */
    public function index()
    {
        $this->data['view'] = 'caixa/index';
        return $this->layout();
    }

    /* =========================
     * BUSCAR COMANDA
     * ========================= */
    public function buscar()
    {
        $codigo = $this->input->post('codigo_comanda');

        if (!$codigo) {
            $this->session->set_flashdata('error', 'Informe o código da comanda.');
            redirect('caixa');
        }

        $os = $this->os_model->getByCodigoComanda($codigo);

        if (!$os) {
            $this->session->set_flashdata('error', 'Comanda não encontrada.');
            redirect('caixa');
        }

        redirect('caixa/visualizar/' . $os->idOs);
    }

    /* =========================
     * VISUALIZAR COMANDA / CAIXA
     * ========================= */
    public function visualizar($idOs)
    {
        $os = $this->os_model->getById($idOs);
        if (!$os) redirect('caixa');

        $produtos = $this->os_model->getProdutos($idOs);
        $servicos = $this->os_model->getServicos($idOs);

        /* ---- calcula total da OS ---- */
        $totalOS = 0;

        foreach ($produtos as $p) {
            $totalOS += (float)$p->subTotal;
        }

        foreach ($servicos as $s) {
            $qtd   = isset($s->quantidade) ? (int)$s->quantidade : 1;
            $preco = isset($s->preco) ? (float)$s->preco : 0;
            $totalOS += isset($s->subTotal)
                ? (float)$s->subTotal
                : ($qtd * $preco);
        }

        /* ---- garante venda vinculada ---- */
        $venda = $this->_getOrCreateVenda($os, $totalOS);

        /* ---- pagamentos ---- */
        $pagamentos = $this->db
            ->where('vendas_id', $venda->idVendas)
            ->where('tipo', 'receita')
            ->order_by('idLancamentos', 'DESC')
            ->get('lancamentos')
            ->result();

        $totalPago = 0;
        foreach ($pagamentos as $p) {
            $totalPago += (float)$p->valor;
        }

        $saldo = $totalOS - $totalPago;

        /* =========================
         * FECHAMENTO AUTOMÁTICO
         * ========================= */
        if ($totalOS > 0 && $saldo <= 0) {

            // fecha OS
            $this->db->where('idOs', $idOs)->update('os', [
                'status_os' => 'PAGO',
                'status'    => 'Finalizado'
            ]);

            // fatura venda
            $this->db->where('idVendas', $venda->idVendas)->update('vendas', [
                'faturado' => 1,
                'status'   => 'Faturado'
            ]);

        } else {
            // mantém OS aguardando caixa
            $this->db->where('idOs', $idOs)->update('os', [
                'status_os' => 'AGUARDANDO_CAIXA'
            ]);
        }

        /* ---- envia para view ---- */
        $this->data = array_merge($this->data, [
            'os'         => $os,
            'produtos'   => $produtos,
            'servicos'   => $servicos,
            'totalOS'    => $totalOS,
            'totalPago'  => $totalPago,
            'saldo'      => $saldo,
            'pagamentos' => $pagamentos,
            'venda'      => $venda
        ]);

        $this->data['view'] = 'caixa/resultado';
        return $this->layout();
    }

    /* =========================
     * REGISTRAR PAGAMENTO
     * ========================= */
    public function pagar()
    {
        $idOs  = $this->input->post('idOs');
        $forma = $this->input->post('forma_pagamento');
        $valor = $this->input->post('valor_pago');

        if (!$idOs || !$forma || !$valor) {
            redirect('caixa');
        }

        // normaliza valor
        $valor = str_replace(['R$', ' ', '.'], '', $valor);
        $valor = str_replace(',', '.', $valor);
        $valor = (float)$valor;

        if ($valor <= 0) {
            redirect('caixa/visualizar/' . $idOs);
        }

        $os = $this->os_model->getById($idOs);
        if (!$os) redirect('caixa');

        $produtos = $this->os_model->getProdutos($idOs);
        $servicos = $this->os_model->getServicos($idOs);

        $totalOS = 0;
        foreach ($produtos as $p) $totalOS += (float)$p->subTotal;
        foreach ($servicos as $s) {
            $qtd   = $s->quantidade ?? 1;
            $preco = $s->preco ?? 0;
            $totalOS += $s->subTotal ?? ($qtd * $preco);
        }

        $venda = $this->_getOrCreateVenda($os, $totalOS);

        /* =========================
         * ENTRADA / PAGAMENTO / RETIRADA
         * ========================= */
        $jaPago = $this->db
            ->select_sum('valor')
            ->where('vendas_id', $venda->idVendas)
            ->where('tipo', 'receita')
            ->get('lancamentos')
            ->row();

        $totalPagoAntes = (float) ($jaPago->valor ?? 0);
        $saldoAntes = $totalOS - $totalPagoAntes;

        if ($totalPagoAntes <= 0) {
            $tipoPagamento = 'ENTRADA';
        } elseif ($valor >= $saldoAntes) {
            $tipoPagamento = 'RETIRADA';
        } else {
            $tipoPagamento = 'PAGAMENTO';
        }

        /* =========================
         * FINANCEIRO PERFEITO:
         * - nome do cliente no relatório
         * - filtro por data funcionando (usa data_vencimento)
         * ========================= */
        $clienteNome = null;
        if (isset($os->nomeCliente) && $os->nomeCliente) {
            $clienteNome = $os->nomeCliente;
        } elseif (isset($os->nome) && $os->nome) {
            $clienteNome = $os->nome;
        } else {
            $clienteNome = 'Cliente';
        }

        $dataAgora = date('Y-m-d H:i:s');

        $this->db->insert('lancamentos', [
            'descricao'          => $tipoPagamento . ' - OS #' . $os->idOs,
            'valor'              => $valor,

            // ✅ salva data/hora real
            'data_pagamento'     => $dataAgora,

            // ✅ ESSENCIAL para filtrar no Financeiro (MapOS filtra por vencimento)
            'data_vencimento'    => date('Y-m-d'),

            'baixado'            => 1,

            // ✅ nome do cliente aparece no relatório
            'cliente_fornecedor' => $clienteNome,

            'forma_pgto'         => $forma,
            'tipo'               => 'receita',
            'clientes_id'        => $os->clientes_id,
            'vendas_id'          => $venda->idVendas,
            'usuarios_id'        => $this->session->userdata('id_admin'),
        ]);

        redirect('caixa/visualizar/' . $idOs);
    }

    /* =========================
     * CRIA / BUSCA VENDA
     * ========================= */
    private function _getOrCreateVenda($os, $totalOS)
    {
        $venda = $this->db
            ->where('os_id', $os->idOs)
            ->order_by('idVendas', 'DESC')
            ->get('vendas')
            ->row();

        if ($venda) return $venda;

        $this->db->insert('vendas', [
            'dataVenda'      => date('Y-m-d'),
            'clientes_id'    => $os->clientes_id,
            'usuarios_id'    => $this->session->userdata('id_admin'),
            'os_id'          => $os->idOs,
            'valorTotal'     => $totalOS,
            'desconto'       => 0,
            'valor_desconto' => $totalOS,
            'faturado'       => 0,
            'status'         => 'Aberto',
            'garantia'       => 0,
            'observacoes'    => 'Venda criada automaticamente pelo Caixa'
        ]);

        return $this->db
            ->where('idVendas', $this->db->insert_id())
            ->get('vendas')
            ->row();
    }


    /* =========================
     * VOLTAR
     * ========================= */
    public function cancelar()
    {
        redirect('caixa');
    }
}
