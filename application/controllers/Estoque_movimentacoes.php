<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Estoque_movimentacoes extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Estoque_movimentacoes_model', 'movimentacoes_model');
        $this->load->model('Estoque_itens_model', 'itens_model');
        $this->load->model('Estoque_setores_model', 'setores_model');

        $this->data['menuEstoque'] = 'Estoque';
    }

    public function index()
    {
        $this->data['movimentacoes'] = $this->movimentacoes_model->getAll();
        $this->data['view'] = 'estoque/movimentacoes/listar';

        return $this->layout('estoque/index');
    }

    public function movimentar()
    {
        $this->data['itens'] = $this->itens_model->getAll();

        if ($this->input->post()) {

            $data = [
                'item_id'     => (int) $this->input->post('item_id'),
                'tipo'        => $this->input->post('tipo'),
                'quantidade'  => (float) $this->input->post('quantidade'),
                'origem'      => $this->input->post('origem'),
                // se você decidiu remover observação do form, pode manter vazio:
                'observacao'  => $this->input->post('observacao'),
                'usuarios_id' => (int) $this->session->userdata('id_admin'),
            ];

            if (empty($data['item_id']) || empty($data['tipo']) || $data['quantidade'] <= 0) {
                $this->session->set_flashdata('error', 'Preencha os campos obrigatórios.');
                redirect('estoque_movimentacoes/movimentar');
            }

            $this->movimentacoes_model->registrar($data);

            $this->session->set_flashdata('success', 'Movimentação registrada com sucesso.');
            redirect('estoque_movimentacoes');
        }

        $this->data['view'] = 'estoque/movimentacoes/movimentar';
        return $this->layout('estoque/index');
    }
}
