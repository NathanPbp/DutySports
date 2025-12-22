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
        $this->logo        = $logo;
        $this->osNumero    = $osNumero;
        $this->responsavel = $responsavel;
        $this->emissao     = $emissao;
    }

    // ==================================================
    // CABEÇALHO (REPITE EM TODAS AS PÁGINAS)
    // ==================================================
    public function Header()
    {
        // Logo (esquerda)
        if ($this->logo && file_exists($this->logo)) {
            $this->Image($this->logo, 10, 8, 30);
        }

        // Título central
        $this->SetFont('helvetica', 'B', 12);
        $this->SetXY(0, 10);
        $this->Cell(0, 6, 'ORDEM DE SERVIÇO', 0, 1, 'C');

        $this->SetFont('helvetica', '', 11);
        $this->Cell(0, 6, '#' . $this->osNumero, 0, 1, 'C');

        // Dados direita
        $this->SetFont('helvetica', '', 9);
        $this->SetXY(140, 10);
        $this->Cell(60, 5, 'RESPONSÁVEL: ' . $this->responsavel, 0, 2, 'R');
        $this->Cell(60, 5, 'EMISSÃO: ' . $this->emissao, 0, 2, 'R');

        // Linha separadora
        $this->Line(10, 28, 200, 28);
    }

    // ==================================================
    // RODAPÉ (PAGINAÇÃO)
    // ==================================================
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
