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

    $payload = [
        'os_id'       => $osId,
        'modelo'      => $data['modelo'] ?? null,
        'tecido'      => $data['tecido'] ?? null,
        'gola'        => $data['gola'] ?? null,
        'tecnica'     => $data['tecnica'] ?? null,
        'simbolo'     => $data['simbolo'] ?? null,
        'observacao'  => $data['observacao'] ?? null,
    ];

    if ($exists) {
        $this->db->where('os_id', $osId);
        return $this->db->update('os_producao', $payload);
    }

    return $this->db->insert('os_producao', $payload);
}

    /* ===============================
     *  GRADE DE PRODUÇÃO
     * =============================== */
public function getGradeByOs($osId)
{
    return $this->db
        ->select('id, os_id, quantidade, nome, superior, inferior, numero, adicional, modelo')
        ->from('os_producao_grade')
        ->where('os_id', $osId)
        ->order_by('id', 'ASC')
        ->get()
        ->result_array();
}


  public function saveGrade($osId, $grade)
{
    // 🔴 LOG 1 — confirma entrada no método
    log_message('error', 'ENTROU NO saveGrade | OS_ID = ' . $osId);

    // segurança
    if (!is_array($grade)) {
        log_message('error', 'GRADE NÃO É ARRAY');
        return;
    }

    // 🔴 LOG 2 — quantidade de linhas recebidas
    log_message('error', 'TOTAL DE LINHAS DA GRADE: ' . count($grade));

    // 1) Remove grade antiga da OS
    $this->db->where('os_id', (int) $osId)
             ->delete('os_producao_grade');

    // 🔴 LOG 3 — delete executado
    log_message('error', 'GRADE ANTIGA REMOVIDA');

    foreach ($grade as $index => $linha) {

        // 🔴 LOG 4 — dump da linha
        log_message('error', 'LINHA ' . $index . ': ' . print_r($linha, true));

        $quantidade = (int) ($linha['quantidade'] ?? 0);
        $nome       = trim($linha['nome'] ?? '');
        $superior   = trim($linha['superior'] ?? '');
        $inferior   = trim($linha['inferior'] ?? '');
        $numero     = trim($linha['numero'] ?? '');
        $adicional  = trim($linha['adicional'] ?? '');
        $modelo     = trim($linha['modelo'] ?? '');

        // ignora linha completamente vazia
        if (
            $quantidade <= 0 &&
            $nome === '' &&
            $superior === '' &&
            $inferior === ''
        ) {
            log_message('error', 'LINHA IGNORADA (VAZIA)');
            continue;
        }

        $insert = [
            'os_id'      => (int) $osId,
            'quantidade' => $quantidade,
            'nome'       => $nome,
            'superior'   => $superior ?: null,
            'inferior'   => $inferior ?: null,
            'numero'     => $numero,
            'adicional'  => $adicional,
            'modelo'     => $modelo
        ];

        $this->db->insert('os_producao_grade', $insert);

        // 🔴 LOG 5 — verifica erro de insert
       // if ($this->db->affected_rows() === 0) {
          //  log_message(
             //   'error',
             //   'ERRO AO INSERIR LINHA | SQL: ' . $this->db->last_query()
       //     );
       // } else {
       //     log_message('error', 'LINHA INSERIDA COM SUCESSO');
       // }
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

   public function updateArte($osId, $novoPath)
{
    $atual = $this->getProducaoByOs($osId);

    // Remove imagem antiga se existir
    if ($atual && !empty($atual->arte_imagem)) {
        $oldPath = FCPATH . $atual->arte_imagem;
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }

    $this->db->where('os_id', $osId);
    return $this->db->update('os_producao', [
        'arte_imagem' => $novoPath
    ]);
}

}
