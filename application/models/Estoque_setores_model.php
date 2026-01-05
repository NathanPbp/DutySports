<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Estoque_setores_model extends CI_Model
{
    protected $table = 'estoque_setores';

    public function getAll()
    {
        return $this->db
            ->where('ativo', 1)
            ->order_by('nome', 'ASC')
            ->get($this->table)
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
