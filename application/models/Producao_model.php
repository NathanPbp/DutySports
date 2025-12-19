<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Producao_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /* ===============================
     *  FICHA DE PRODUÇÃO
     * =============================== */

    public function getProducaoByOs($osId)
    {
        return $this->db
            ->where('os_id', $osId)
            ->get('os_producao')
            ->row();
    }

    public function saveProducao($osId, $data)
    {
        $exists = $this->getProducaoByOs($osId);

        $data['os_id'] = $osId;

        if ($exists) {
            $this->db->where('os_id', $osId);
            return $this->db->update('os_producao', $data);
        }

        return $this->db->insert('os_producao', $data);
    }

    /* ===============================
     *  GRADE DE PRODUÇÃO
     * =============================== */
public function getGradeByOs($osId)
{
    return $this->db
       ->select('id, os_id, tamanho, quantidade, nome, adicional, numero, modelo')
        ->from('os_producao_grade')
        ->where('os_id', $osId)
        ->order_by('id', 'ASC')
        ->get()
        ->result();
}

    public function saveGrade($osId, $grade)
{
    // 1) Apaga grade antiga dessa OS (pra salvar “limpo”)
    $this->db->where('os_id', (int)$osId)->delete('os_producao_grade');

    // 2) Insere novamente as linhas válidas
    if (!is_array($grade)) {
        return;
    }

    foreach ($grade as $linha) {
        $tamanho = trim($linha['tamanho'] ?? '');
        $quantidade = (int)($linha['quantidade'] ?? 0);

        // ignora linha vazia
        if ($tamanho === '' && $quantidade <= 0) {
            continue;
        }

        $this->db->insert('os_producao_grade', [
            'os_id'      => (int)$osId,
            'tamanho'    => $tamanho,
            'quantidade' => $quantidade
        ]);
    }
}


    /* ===============================
     *  TÉCNICAS DE PRODUÇÃO
     * =============================== */

    public function getTecnicas()
    {
        return $this->db
            ->where('ativo', 1)
            ->order_by('tipo, nome')
            ->get('producao_tecnicas')
            ->result();
    }

    public function getTecnicasByOs($osId)
    {
        return $this->db
            ->select('t.*')
            ->from('os_producao_tecnicas opt')
            ->join('producao_tecnicas t', 't.id = opt.tecnica_id')
            ->where('opt.os_id', $osId)
            ->get()
            ->result();
    }

    public function saveTecnicas($osId, $tecnicas)
    {
        // remove técnicas antigas
        $this->db->where('os_id', $osId)->delete('os_producao_tecnicas');

        if (!is_array($tecnicas)) {
            return;
        }

        foreach ($tecnicas as $tecnicaId) {
            $this->db->insert('os_producao_tecnicas', [
                'os_id'      => $osId,
                'tecnica_id' => $tecnicaId
            ]);
        }
    }

    /* ===============================
     *  VÍNCULO SERVIÇO → TÉCNICA
     * =============================== */

    public function getTecnicasByServicos($servicosIds)
    {
        if (empty($servicosIds)) {
            return [];
        }

        return $this->db
            ->select('DISTINCT(tecnica_id)')
            ->where_in('servico_id', $servicosIds)
            ->get('servico_tecnica_vinculo')
            ->result();
    }

    /* ===============================
     *  IMAGEM DA ARTE
     * =============================== */

    public function updateArte($osId, $path)
    {
        $this->db->where('os_id', $osId);
        return $this->db->update('os_producao', [
            'arte_imagem' => $path
        ]);
    }
}
