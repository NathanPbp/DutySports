<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'third_party/tcpdf/tcpdf.php';

class PdfDuty extends TCPDF
{
    protected $logo;
    protected $osNumero;
    protected $responsavel;
    protected $emissao;

   public function setDadosCabecalho($logo, $osNumero, $responsavel, $emissao)
{
    // Normaliza caminho para Windows/Linux e tenta resolver path real
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

public function Header()
{
    // ===============================
    // LOGO (mais para cima)
    // ===============================
    if (!empty($this->logo) && file_exists($this->logo)) {
        // X = 10 | Y = 5 (ANTES estava 8)
        $this->Image(
            $this->logo,
            10,
            3,
            30,   // largura um pouco maior
            0,
            'JPG',
            '',
            '',
            false,
            300
        );
    }

    // ===============================
    // TÍTULO CENTRAL
    // ===============================
    $this->SetFont('helvetica', 'B', 12);
    $this->SetXY(0, 8);
    $this->Cell(0, 6, 'ORDEM DE SERVIÇO', 0, 1, 'C');

    $this->SetFont('helvetica', '', 11);
    $this->Cell(0, 6, '#' . $this->osNumero, 0, 1, 'C');

    // ===============================
    // DADOS À DIREITA
    // ===============================
    $this->SetFont('helvetica', '', 9);
    $this->SetXY(140, 8);
    $this->Cell(60, 5, 'RESPONSÁVEL: ' . $this->responsavel, 0, 2, 'R');
    $this->Cell(60, 5, 'EMISSÃO: ' . $this->emissao, 0, 2, 'R');

    // ===============================
    // LINHA SEPARADORA (MAIS BAIXO)
    // ===============================
    // ANTES: Y = 28
    // AGORA: Y = 32  → não corta o logo
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
}
