<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Estoque_relatorios extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Estoque_relatorios_model', 'relatorios_model');
        $this->data['menuEstoque'] = 'Estoque';
    }

    public function consumo()
    {
        $dataInicio = $this->input->get('data_inicio') ?: date('Y-m-01');
        $dataFim    = $this->input->get('data_fim') ?: date('Y-m-d');

        $this->data['relatorio'] = $this->relatorios_model->consumoPorSetor($dataInicio, $dataFim);
        $this->data['dataInicio'] = $dataInicio;
        $this->data['dataFim']    = $dataFim;

        $this->data['view'] = 'estoque/relatorios/consumo';
        return $this->layout('estoque/index');
    }
}
