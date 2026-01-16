    <?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    class Producao_model extends CI_Model
    {
        public function __construct()
        {
            parent::__construct();
        }

        /* ===============================
        *  PRODUÇÃO (GUIAS)
        * =============================== */

        public function getProducoesByOs($osId)
        {
            return $this->db
                ->where('os_id', $osId)
                ->order_by('numero_guia', 'ASC')
                ->get('os_producao')
                ->result();
        }

        public function getProducaoById($producaoId)
        {
            return $this->db
                ->where('id', $producaoId)
                ->get('os_producao')
                ->row();
        }

        public function getNextNumeroGuia($osId)
        {
            $this->db->select_max('numero_guia');
            $this->db->where('os_id', $osId);
            $row = $this->db->get('os_producao')->row();

            return ($row && $row->numero_guia) ? $row->numero_guia + 1 : 1;
        }

        public function saveProducao($osId, $data, $producaoId = null)
        {
            $payload = [
                'modelo'     => $data['modelo'] ?? null,
                'tecido'     => $data['tecido'] ?? null,
                'gola'       => $data['gola'] ?? null,
                'tecnica'    => $data['tecnica'] ?? null,
                'simbolo'    => $data['simbolo'] ?? null,
                'observacao' => $data['observacao'] ?? null,
                'prioridade' => $data['prioridade'] ?? null,

            ];

            // UPDATE1
            if ($producaoId) {
                $this->db->where('id', $producaoId);
                $this->db->update('os_producao', $payload);
                return $producaoId;
            }

            // INSERT (nova guia)
            $payload['os_id'] = $osId;
            $payload['numero_guia'] = $this->getNextNumeroGuia($osId);

            $this->db->insert('os_producao', $payload);
            return $this->db->insert_id();
        }

        /* ===============================
        *  GRADE DE PRODUÇÃO
        * =============================== */

        public function getGradeByProducao($producaoId)
        {
            return $this->db
                ->where('producao_id', $producaoId)
                ->order_by('id', 'ASC')
                ->get('os_producao_grade')
                ->result_array();
        }

       public function saveGrade($producaoId, $osId, $grade)
{
    if (!$producaoId || !$osId) {
        return false;
    }

    // Remove grade antiga
    $this->db->where('producao_id', $producaoId);
    $this->db->delete('os_producao_grade');

    if (empty($grade)) {
        return true;
    }

    foreach ($grade as $linha) {

        if (empty($linha['quantidade']) && empty($linha['nome'])) {
            continue;
        }

        $data = [
            'producao_id' => $producaoId,
            'os_id'       => $osId, // ✅ AGORA NÃO É MAIS NULL
            'quantidade'  => $linha['quantidade'] ?? 0,
            'nome'        => $linha['nome'] ?? null,
            'superior'    => $linha['superior'] ?? null,
            'inferior'    => $linha['inferior'] ?? null,
            'numero'      => $linha['numero'] ?? null,
            'adicional'   => $linha['adicional'] ?? null,
            'modelo'      => $linha['modelo'] ?? null,
        ];

        $this->db->insert('os_producao_grade', $data);
    }

    return true;
}




        /* ===============================
        *  TÉCNICAS
        * =============================== */

        public function getTecnicas()
        {
            return $this->db
                ->where('ativo', 1)
                ->order_by('tipo, nome')
                ->get('producao_tecnicas')
                ->result();
        }

        public function getTecnicasByProducao($producaoId)
        {
            return $this->db
                ->select('t.*')
                ->from('os_producao_tecnicas opt')
                ->join('producao_tecnicas t', 't.id = opt.tecnica_id')
                ->where('opt.producao_id', $producaoId)
                ->get()
                ->result();
        }

        public function saveTecnicas($producaoId, $tecnicas)
        {
            $this->db->where('producao_id', $producaoId)
                    ->delete('os_producao_tecnicas');

            if (!is_array($tecnicas)) {
                return;
            }

            foreach ($tecnicas as $tecnicaId) {
                $this->db->insert('os_producao_tecnicas', [
                    'producao_id' => $producaoId,
                    'tecnica_id'  => $tecnicaId
                ]);
            }
        }

        /* ===============================
        *  ARTE DA PRODUÇÃO
        * =============================== */

        public function updateArte($producaoId, $novoPath)
{
    $atual = $this->getProducaoById($producaoId);

    // só apaga a antiga SE for diferente da nova
    if (
        $atual &&
        !empty($atual->arte_imagem) &&
        $atual->arte_imagem !== $novoPath
    ) {
        $oldPath = FCPATH . $atual->arte_imagem;
        if (is_file($oldPath)) {
            unlink($oldPath);
        }
    }

    $this->db->where('id', $producaoId);
    $this->db->update('os_producao', [
        'arte_imagem' => $novoPath
    ]);
}

    }
