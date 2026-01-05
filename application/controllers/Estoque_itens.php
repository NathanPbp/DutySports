<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Estoque_itens extends MY_Controller
{
   public function __construct()
{
    parent::__construct();

    $this->load->model('Estoque_itens_model', 'estoque_itens_model');
    $this->load->model('Estoque_setores_model', 'estoque_setores_model');

    $this->data['menuEstoque'] = 'Estoque';
}


 public function index()
{
    $this->data['itens'] = $this->estoque_itens_model->getAll();

    $this->data['view'] = 'estoque/itens/listar';

    return $this->layout('estoque/index');
}






    public function adicionar()
    {
        $this->data['menuEstoqueItens'] = 'Estoque';

        $this->data['setores'] = $this->estoque_setores_model->getAll();


        if ($this->input->post()) {
            $data = [
                'nome'             => trim($this->input->post('nome')),
                'descricao'        => $this->input->post('descricao'),
                'setor_id'         => $this->input->post('setor_id'),
                'unidade'          => $this->input->post('unidade'),
                'estoque_minimo'   => $this->input->post('estoque_minimo') ?: 0,
                'quantidade_atual' => 0
            ];

            if (!$data['nome'] || !$data['setor_id']) {
                $this->session->set_flashdata('error', 'Preencha os campos obrigatórios.');
                redirect('estoque_itens/adicionar');
            }

            $this->Estoque_itens_model->insert($data);
            $this->session->set_flashdata('success', 'Item cadastrado.');
            redirect('estoque_itens');
        }

        $this->data['view'] = 'estoque/itens/adicionar';
        return $this->layout();
    }

    public function editar($id)
    { 
        $this->data['menuEstoqueItens'] = 'Estoque';

        $item = $this->Estoque_itens_model->getById($id);
        if (!$item) redirect('estoque_itens');

        $this->data['setores'] = $this->Estoque_setores_model->getAll();

        if ($this->input->post()) {
            $data = [
                'nome'           => trim($this->input->post('nome')),
                'descricao'      => $this->input->post('descricao'),
                'setor_id'       => $this->input->post('setor_id'),
                'unidade'        => $this->input->post('unidade'),
                'estoque_minimo' => $this->input->post('estoque_minimo') ?: 0
            ];

            $this->Estoque_itens_model->update($id, $data);
            $this->session->set_flashdata('success', 'Item atualizado.');
            redirect('estoque_itens');
        }

        $this->data['item'] = $item;
        $this->data['view'] = 'estoque/itens/editar';
        return $this->layout();
    }

    public function excluir($id)
    {
        $this->Estoque_itens_model->delete($id);
        $this->session->set_flashdata('success', 'Item removido.');
        redirect('estoque_itens');
    }
}
