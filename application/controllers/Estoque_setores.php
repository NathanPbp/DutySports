<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Estoque_setores extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
       /*
        if (!$this->permission->checkPermission(
            $this->session->userdata('permissao'),
            'vProdutos'
        )) {
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar Estoque.');
            redirect(base_url());
        }
            */

        $this->load->model('Estoque_setores_model');
        $this->data['menuEstoque'] = 'Estoque';
    }

    public function index()
    {
        $this->data['setores'] = $this->Estoque_setores_model->getAll();
        $this->data['view'] = 'estoque/setores/listar';
        return $this->layout();
    }

    public function adicionar()
    {
        if ($this->input->post()) {
            $nome = trim($this->input->post('nome'));

            if (!$nome) {
                $this->session->set_flashdata('error', 'Informe o nome do setor.');
                redirect('estoque_setores/adicionar');
            }

            $this->Estoque_setores_model->insert([
                'nome' => $nome
            ]);

            $this->session->set_flashdata('success', 'Setor cadastrado com sucesso.');
            redirect('estoque_setores');
        }

        $this->data['view'] = 'estoque/setores/adicionar';
        return $this->layout();
    }

    public function editar($id)
    {
        $setor = $this->Estoque_setores_model->getById($id);
        if (!$setor) redirect('estoque_setores');

        if ($this->input->post()) {
            $nome = trim($this->input->post('nome'));

            if (!$nome) {
                $this->session->set_flashdata('error', 'Informe o nome do setor.');
                redirect('estoque_setores/editar/' . $id);
            }

            $this->Estoque_setores_model->update($id, [
                'nome' => $nome
            ]);

            $this->session->set_flashdata('success', 'Setor atualizado.');
            redirect('estoque_setores');
        }

        $this->data['setor'] = $setor;
        $this->data['view'] = 'estoque/setores/editar';
        return $this->layout();
    }

    public function excluir($id)
    {
        $this->Estoque_setores_model->delete($id);
        $this->session->set_flashdata('success', 'Setor removido.');
        redirect('estoque_setores');
    }
}

