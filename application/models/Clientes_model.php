<?php

class Clientes_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
{
    $this->db->select($fields);
    $this->db->from($table);
    $this->db->order_by('idClientes', 'desc');

    if ($perpage > 0) {
        $this->db->limit($perpage, $start);
    }

    if ($where) {

        // Remove tudo que não for número (para busca por telefone)
        $clean = preg_replace('/\D/', '', $where);

        $this->db->group_start();

            // Busca padrão
            $this->db->like('nomeCliente', $where);
            $this->db->or_like('documento', $where);
            $this->db->or_like('email', $where);
            $this->db->or_like('telefone', $where);
            $this->db->or_like('celular', $where);

            // Busca por telefone sem máscara (FORMA SEGURA)
            if (!empty($clean)) {
                $this->db->or_where(
                    "REPLACE(REPLACE(REPLACE(telefone, '(', ''), ')', ''), '-', '') LIKE '%{$clean}%'",
                    null,
                    false
                );

                $this->db->or_where(
                    "REPLACE(REPLACE(REPLACE(celular, '(', ''), ')', ''), '-', '') LIKE '%{$clean}%'",
                    null,
                    false
                );
            }

        $this->db->group_end();
    }

    $query = $this->db->get();

    if (!$query) {
        log_message(
            'error',
            'Erro na query Clientes_model::get() - ' . $this->db->last_query()
        );
        return [];
    }

    return !$one ? $query->result() : $query->row();
}


    public function getById($id)
    {
        $this->db->where('idClientes', $id);
        $this->db->limit(1);

        return $this->db->get('clientes')->row();
    }

    public function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            return $this->db->insert_id($table);
        }

        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0) {
            return true;
        }

        return false;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);

        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function count($table)
    {
        return $this->db->count_all($table);
    }

    public function getOsByCliente($id)
    {
        $this->db->where('clientes_id', $id);
        $this->db->order_by('idOs', 'desc');
        $this->db->limit(10);

        return $this->db->get('os')->result();
    }

    public function getAllOsByClient($id)
    {
        $this->db->where('clientes_id', $id);
        return $this->db->get('os')->result();
    }

    public function removeClientOs($os)
    {
        try {
            foreach ($os as $o) {

                $this->db->where('os_id', $o->idOs);
                $this->db->delete('servicos_os');

                $this->db->where('os_id', $o->idOs);
                $this->db->delete('produtos_os');

                $this->db->where('idOs', $o->idOs);
                $this->db->delete('os');
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function getAllVendasByClient($id)
    {
        $this->db->where('clientes_id', $id);
        return $this->db->get('vendas')->result();
    }

    public function removeClientVendas($vendas)
    {
        try {
            foreach ($vendas as $v) {

                $this->db->where('vendas_id', $v->idVendas);
                $this->db->delete('itens_de_vendas');

                $this->db->where('idVendas', $v->idVendas);
                $this->db->delete('vendas');
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
    public function pagar()
{
    $idOs  = $this->input->post('idOs');
    $forma = $this->input->post('forma_pagamento');
    $valor = $this->input->post('valor_pago');

    if (!$idOs || !$forma || !$valor) {
        $this->session->set_flashdata('error', 'Dados inválidos.');
        redirect('caixa');
    }

    // Normaliza valor "50,00" → 50.00
    $valor = str_replace(['R$', ' ', '.'], '', $valor);
    $valor = str_replace(',', '.', $valor);
    $valor = (float)$valor;

    if ($valor <= 0) {
        $this->session->set_flashdata('error', 'Informe um valor válido.');
        redirect('caixa/visualizar/' . $idOs);
    }

    $os = $this->os_model->getById($idOs);
    if (!$os) {
        redirect('caixa');
    }

    // 🔹 Recalcula total da OS (mesma lógica da visualização)
    $produtos = $this->os_model->getProdutos($idOs);
    $servicos = $this->os_model->getServicos($idOs);

    $totalOS = 0;
    foreach ($produtos as $p) {
        $totalOS += (float)$p->subTotal;
    }
    foreach ($servicos as $s) {
        $qtd = isset($s->quantidade) ? (int)$s->quantidade : 1;
        $preco = isset($s->preco) ? (float)$s->preco : 0;
        $totalOS += isset($s->subTotal) ? (float)$s->subTotal : ($qtd * $preco);
    }

    // 🔹 Garante venda criada
    $venda = $this->_getOrCreateVenda($os, $totalOS);
    if (!$venda) {
        $this->session->set_flashdata('error', 'Erro ao localizar venda.');
        redirect('caixa/visualizar/' . $idOs);
    }

    // 🔹 Insere lançamento (pagamento)
    $dadosLancamento = [
        'descricao'          => 'Pagamento OS #' . $os->idOs,
        'valor'              => $valor,
        'data_pagamento'     => date('Y-m-d'),
        'baixado'            => 1,
        'cliente_fornecedor' => $os->nomeCliente ?? 'Cliente',
        'forma_pgto'         => $forma,
        'tipo'               => 'receita',
        'clientes_id'        => $os->clientes_id,
        'vendas_id'          => $venda->idVendas,
        'usuarios_id'        => $this->session->userdata('id_admin'),
    ];

    $this->db->insert('lancamentos', $dadosLancamento);

    $this->session->set_flashdata('success', 'Pagamento registrado com sucesso!');
    redirect('caixa/visualizar/' . $idOs);
}


}
