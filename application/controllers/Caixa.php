<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Caixa extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (
            !$this->permission->checkPermission(
                $this->session->userdata('permissao'),
                'vCaixa'
            )
        ) {
            $this->session->set_flashdata(
                'error',
                'Você não tem permissão para acessar o Caixa.'
            );
            redirect(base_url());
        }

        $this->load->model('os_model');
        $this->data['menuCaixa'] = 'Caixa';
    }

    public function index()
{
    $codigo = $this->session->userdata('caixa_codigo_comanda');

    if (!$codigo) {
        $this->data['view'] = 'caixa/index';
        return $this->layout();
    }

    $os = $this->os_model->getByCodigoComanda($codigo);
    if (!$os) {
        $this->session->unset_userdata('caixa_codigo_comanda');
        redirect('caixa');
    }

    $produtos = $this->os_model->getProdutos($os->idOs);
    $servicos = $this->os_model->getServicos($os->idOs);

    // TOTAL DA OS
    $totalOS = 0;
    foreach ($produtos as $p) {
        $totalOS += (float)$p->subTotal;
    }
    foreach ($servicos as $s) {
        $qtd = $s->quantidade ?: 1;
        $totalOS += (float)$s->preco * $qtd;
    }

    // PAGAMENTOS
    $pagamentos = $this->db
        ->where('vendas_id', $os->idOs)
        ->where('baixado', 1)
        ->get('lancamentos')
        ->result();

    $totalPago = 0;
    foreach ($pagamentos as $p) {
        $totalPago += (float)$p->valor;
    }

    $saldo = $totalOS - $totalPago;

    // STATUS (NÃO FECHA AUTOMATICAMENTE)
    $status = $saldo <= 0 ? 'PAGO' : 'AGUARDANDO_CAIXA';
    $this->db->where('idOs', $os->idOs)->update('os', [
        'status_os' => $status
    ]);

    $this->data = array_merge($this->data, [
        'os'         => $os,
        'produtos'   => $produtos,
        'servicos'   => $servicos,
        'totalOS'    => $totalOS,
        'totalPago'  => $totalPago,
        'saldo'      => $saldo,
        'pagamentos' => $pagamentos,
        'view'       => 'caixa/resultado'
    ]);

    return $this->layout();
}

   public function buscar()
{
    $codigo = $this->input->post('codigo_comanda');

    if (!$codigo) {
        redirect('caixa');
    }

    $os = $this->os_model->getByCodigoComanda($codigo);
    if (!$os) {
        redirect('caixa');
    }

    $produtos = $this->os_model->getProdutos($os->idOs);
    $servicos = $this->os_model->getServicos($os->idOs);

    // Total da OS
    $totalOS = (float) $os->valorTotal;

    // 🔥 SOMA PAGAMENTOS PELO vendas_id
    $row = $this->db
        ->select_sum('valor')
        ->where('vendas_id', $os->idOs)
        ->where('tipo', 'entrada')
        ->get('lancamentos')
        ->row();

    $totalPago = $row && $row->valor ? (float)$row->valor : 0;
    $saldo = $totalOS - $totalPago;

    // Histórico de pagamentos
    $pagamentos = $this->db
        ->where('vendas_id', $os->idOs)
        ->where('tipo', 'entrada')
        ->order_by('idLancamentos', 'ASC')
        ->get('lancamentos')
        ->result();

    $this->data['os'] = $os;
    $this->data['produtos'] = $produtos;
    $this->data['servicos'] = $servicos;
    $this->data['totalOS'] = $totalOS;
    $this->data['totalPago'] = $totalPago;
    $this->data['saldo'] = $saldo;
    $this->data['pagamentos'] = $pagamentos;

    $this->data['view'] = 'caixa/resultado';
    return $this->layout();
}


   public function pagar()
{
    $idOs  = $this->input->post('idOs');
    $valor = str_replace(['.', ','], ['', '.'], $this->input->post('valor_pago'));
    $forma = $this->input->post('forma_pagamento');

    if (!$idOs || !$valor || !$forma) {
        redirect('caixa');
    }

    // 🔹 INSERE NO FINANCEIRO (PADRÃO MAPOS)
    $this->db->insert('lancamentos', [
        'descricao'     => 'Pagamento OS #' . $idOs,
        'valor'         => $valor,
        'tipo'          => 'entrada',
        'forma_pgto'    => $forma,
        'data_pagamento'=> date('Y-m-d'),
        'vendas_id'     => $idOs,
        'usuarios_id'   => $this->session->userdata('id'),
    ]);

    // NÃO FECHA A COMANDA AUTOMATICAMENTE
    redirect('caixa');
}


    public function cancelar()
    {
        $this->session->unset_userdata('caixa_codigo_comanda');
        redirect('caixa');
    }
}
