<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Estoque_relatorios_model extends CI_Model
{
    public function consumoPorSetor($dataInicio, $dataFim)
    {
        return $this->db
            ->select('
                s.nome AS setor,
                i.nome AS item,
                SUM(m.quantidade) AS total_consumido,
                i.unidade
            ')
            ->from('estoque_movimentacoes m')
            ->join('estoque_itens i', 'i.id = m.item_id')
            ->join('estoque_setores s', 's.id = i.setor_id')
            ->where('m.tipo', 'SAIDA')
            ->where('m.data_movimentacao >=', $dataInicio . ' 00:00:00')
            ->where('m.data_movimentacao <=', $dataFim . ' 23:59:59')
            ->group_by('s.nome, i.nome, i.unidade')
            ->order_by('s.nome, total_consumido DESC')
            ->get()
            ->result();
    }
}
