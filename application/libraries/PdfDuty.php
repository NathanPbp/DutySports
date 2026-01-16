<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'third_party/tcpdf/tcpdf.php';

class PdfDuty extends TCPDF
{
    protected $logo;
    protected $osNumero;
    protected $responsavel;
    protected $emissao;
    protected $prioridade;


   public function setDadosCabecalho($logo, $osNumero, $responsavel, $emissao)
{
    // Normaliza caminho para Windows/Linux e tenta resolver path real1
    if (!empty($logo)) {
        $logo = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $logo);

        // se vier relativo, tenta jogar pra dentro do FCPATH
        if (!is_file($logo)) {
            $tentativa = FCPATH . ltrim(str_replace(['\\','/'], '/', $logo), '/');
            $tentativa = str_replace(['\\','/'], DIRECTORY_SEPARATOR, $tentativa);
            if (is_file($tentativa)) {
                $logo = $tentativa;
            }
        }

        $real = realpath($logo);
        if ($real) {
            $logo = $real;
        }
    }

    $this->logo        = $logo;
    $this->osNumero    = $osNumero;
    $this->responsavel = $responsavel;
    $this->emissao     = $emissao;
}
public function setPrioridade($prioridade)
{
    $this->prioridade = $prioridade;
}


public function Header()
{
    /* ===============================
     * LOGO
     * =============================== */
    if (!empty($this->logo) && file_exists($this->logo)) {
        $this->Image($this->logo, 10, 4, 30);
    }

    /* ===============================
     * VARIÁVEIS BASE DO HEADER
     * =============================== */
    $x = 135;
    $y = 4;      // Y FIXO → não muda nunca
    $w = 60;
    $h = 6;

    /* ===============================
     * LINHA 1 — PÁGINA
     * =============================== */
    $this->SetXY($x, $y);
    $this->SetFont('helvetica', 'B', 9);
    $this->SetFillColor(0, 102, 204);
    $this->SetTextColor(255, 255, 255);

    $this->Cell(    
        $w,
        $h,
        'PÁGINA ' . $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages(),
        1,
        1,
        'C',
        true
    );

    /* ===============================
     * LINHA 2 — RESPONSÁVEL
     * =============================== */
    $this->SetX($x);
    $this->SetFont('helvetica', 'B', 8);
    $this->SetTextColor(0, 0, 0);
    $this->SetFillColor(255, 255, 255);

    $this->SetFont('helvetica', 'B', 7);
$this->Cell(22, $h, 'RESPONSÁVEL:', 1, 0, 'L');

$this->cellFitText(38, $h, $this->responsavel, 1, 'L');
$this->Ln();

    /* ===============================
     * LINHA 3 — EMISSÃO
     * =============================== */
    $this->SetX($x);
    $this->SetFont('helvetica', 'B', 8);
    $this->Cell(22, $h, 'EMISSÃO:', 1, 0);
    $this->SetFont('helvetica', '', 8);
    $this->Cell($w - 22, $h, $this->emissao, 1, 1);
    
    /* ===============================
     * LINHA 4 — PRIORIDADE (SEMPRE RESERVADA)
     * =============================== */
    $this->SetX($x);
    $this->SetFont('helvetica', 'B', 9);

    // cores por tipo
    switch (strtoupper($this->prioridade)) {
        case 'URGENTE':
        case 'EVENTO':
            $this->SetFillColor(220, 53, 69); // vermelho
            break;

        case 'RETRABALHO':
            $this->SetFillColor(25, 135, 84); // verde escuro
            break;

        default:
            $this->SetFillColor(240, 240, 240); // neutro
    }

    $this->SetTextColor(255, 255, 255);

    $this->Cell(
        $w,
        $h,
        $this->prioridade ? strtoupper($this->prioridade) : '',
        1,
        1,
        'C',
        true
    );

    /* ===============================
     * TÍTULO CENTRAL
     * =============================== */
    $this->SetTextColor(0, 0, 0);
    $this->SetFont('helvetica', 'B', 12);
    $this->SetXY(0, 14);
    $this->Cell(0, 6, 'ORDEM DE SERVIÇO', 0, 1, 'C');

    $this->SetFont('helvetica', '', 11);
    $this->Cell(0, 6, '#' . $this->osNumero, 0, 1, 'C');

    /* ===============================
     * LINHA SEPARADORA
     * =============================== */
    $this->Line(10, 32, 200, 32);
}



    // ===============================
    // RODAPÉ (PAGINAÇÃO)
    // ===============================
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', '', 8);
        $this->Cell(
            0,
            10,
            'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(),
            0,
            0,
            'C'
        );
    }
    public function setNumeracaoGuia($atual, $total)
{
    $this->SetFont('helvetica', 'B', 9);
    $this->SetXY(160, 10);
    $this->Cell(
        40,
        6,
        'GUIA ' . $atual . ' / ' . $total,
        0,
        0,
        'R'
    );
}
protected function cellFitText($w, $h, $text, $border = 1, $align = 'L')
{
    $sizes = [8, 7, 6]; // tamanhos possíveis
    foreach ($sizes as $size) {
        $this->SetFont('helvetica', '', $size);
        if ($this->GetStringWidth($text) <= ($w - 2)) {
            break;
        }
    }

    $this->Cell($w, $h, $text, $border, 0, $align);
}




}
