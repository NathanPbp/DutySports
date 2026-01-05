<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Estoque_movimentacoes_model extends CI_Model
{
    protected $table = 'estoque_movimentacoes';

    public function getAll()
    {
        return $this->db
            ->select('
                m.*,
                i.nome AS item_nome,
                s.nome AS setor_nome,
                u.nome AS usuario_nome
            ')
            ->from($this->table . ' m')
            ->join('estoque_itens i', 'i.id = m.item_id')
            ->join('estoque_setores s', 's.id = i.setor_id')
            ->join('usuarios u', 'u.idUsuarios = m.usuarios_id', 'left')
            ->order_by('m.data_movimentacao', 'DESC')
            ->get()
            ->result();
    }

    public function registrar($data)
    {
        $this->db->insert($this->table, $data);

        if ($data['tipo'] === 'ENTRADA') {
            $this->db->set('quantidade_atual', 'quantidade_atual + ' . (float)$data['quantidade'], false);
        } else {
            $this->db->set('quantidade_atual', 'quantidade_atual - ' . (float)$data['quantidade'], false);
        }

        $this->db
            ->where('id', $data['item_id'])
            ->update('estoque_itens');
    }
}
