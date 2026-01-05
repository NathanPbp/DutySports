<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Estoque_itens_model extends CI_Model
{
    protected $table = 'estoque_itens';

    public function getAll()
{
    return $this->db
        ->select('i.*, s.nome AS setor_nome,
            (i.quantidade_atual > 0 AND i.quantidade_atual <= i.estoque_minimo) AS abaixo_minimo')
        ->from($this->table . ' i')
        ->join('estoque_setores s', 's.id = i.setor_id')
        ->where('i.ativo', 1)
        ->order_by('abaixo_minimo DESC, s.nome, i.nome')
        ->get()
        ->result();
}


    public function getById($id)
    {
        return $this->db
            ->where('id', $id)
            ->get($this->table)
            ->row();
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db
            ->where('id', $id)
            ->update($this->table, ['ativo' => 0]);
    }
}
