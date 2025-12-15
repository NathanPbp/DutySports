<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Caixa extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Proteção por permissão
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

        // Models e menu
        $this->load->model('os_model');
        $this->data['menuCaixa'] = 'Caixa';
    }

    public function index()
    {
        $this->data['view'] = 'caixa/index';
        return $this->layout();
    }

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

        $this->data['os'] = $os;
        $this->data['produtos'] = $this->os_model->getProdutos($os->idOs);
        $this->data['servicos'] = $this->os_model->getServicos($os->idOs);

        $this->data['view'] = 'caixa/resultado';
        return $this->layout();
    
    }

    public function pagar()
{
    // Segurança extra (opcional, mas recomendado)
    if (!$this->permission->checkPermission(
        $this->session->userdata('permissao'),
        'vCaixa'
    )) {
        show_error('Acesso negado', 403);
    }

    $idOs = $this->input->post('idOs');

    if (!$idOs) {
        $this->session->set_flashdata('error', 'Pedido inválido.');
        redirect('caixa');
    }

    // Evitar pagar duas vezes
    $os = $this->db->where('idOs', $idOs)->get('os')->row();

    if ($os->status_os === 'PAGO') {
        $this->session->set_flashdata('error', 'Esse pedido já está pago.');
        redirect('caixa');
    }

    // Atualiza somente o status do pedido
    $this->db->where('idOs', $idOs);
    $this->db->update('os', [
        'status_os' => 'PAGO'
    ]);

    $this->session->set_flashdata('success', 'Pagamento confirmado com sucesso.');
    redirect('caixa');
}

}
