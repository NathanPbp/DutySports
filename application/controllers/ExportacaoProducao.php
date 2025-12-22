<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportacaoProducao extends CI_Controller
{
     public function pdf($osId)
    {
        if (!is_numeric($osId)) {
            show_error('OS inválida');
        }

        // Models
        $this->load->model('Producao_model');
        $this->load->model('os_model');
        $this->load->model('mapos_model');

        // Dados principais
        $os        = $this->os_model->getById($osId);
        $emitente  = $this->mapos_model->getEmitente();
        $producao  = $this->Producao_model->getProducaoByOs($osId);
        $grade     = $this->Producao_model->getGradeByOs($osId);

        // Dados do cabeçalho
        $logo = null;

if (!empty($emitente->url_logo)) {
    $logo = FCPATH . 'assets/uploads/' . $emitente->url_logo;
}


        $responsavel = $this->session->userdata('nome_admin');
        $emissao     = date('d/m/Y H:i');
        $osNumero    = str_pad($os->idOs, 4, '0', STR_PAD_LEFT);

        // PDF
        $this->load->library('PdfDuty');

        $pdf = new PdfDuty('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(10, 35, 10);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);

        $pdf->setDadosCabecalho(
            $logo,
            $osNumero,
            $responsavel,
            $emissao
        );

        $pdf->AddPage();

        /* ==================================================
         * FAIXA AMARELA – DATA DE ENTREGA
         * ================================================== */
        $pdf->SetFillColor(255, 205, 0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 8, 'DATA DE ENTREGA', 0, 1, 'L', true);

        $pdf->Ln(2);

        /* ==================================================
         * BLOCO DADOS DO PEDIDO
         * ================================================== */
        $pdf->SetFont('helvetica', '', 9);

        $pdf->Cell(95, 6, 'CLIENTE: ' . $os->nomeCliente, 1);
        $pdf->Cell(95, 6, 'TELEFONE: ' . ($os->celular_cliente ?? ''), 1);
        $pdf->Ln();

        $pdf->Cell(95, 6, 'PEDIDO Nº: ' . $osNumero, 1);
        $pdf->Cell(95, 6, 'VENDEDOR: ' . $responsavel, 1);
        $pdf->Ln(10);

        /* ==================================================
         * BLOCO ARTE + INFORMAÇÕES
         * ================================================== */

        // ARTE
        $yInicio = $pdf->GetY();
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(90, 6, 'ARTE', 1, 1);
        $pdf->Rect(10, $pdf->GetY(), 90, 60);

        if (!empty($producao->arte_imagem)) {
            $img = FCPATH . $producao->arte_imagem;
            if (file_exists($img)) {
                $pdf->Image($img, 12, $pdf->GetY() + 2, 86);
            }
        }

        // INFORMAÇÕES
        $pdf->SetXY(105, $yInicio);
        $pdf->Cell(95, 6, 'INFORMAÇÕES', 1, 1);
        $pdf->SetFont('helvetica', '', 9);

        $pdf->Cell(95, 6, 'TECIDO: ' . ($producao->tecido ?? ''), 1, 1);
        $pdf->Cell(95, 6, 'GOLA: ' . ($producao->gola ?? ''), 1, 1);
        $pdf->Cell(95, 6, 'TÉCNICA: ' . ($producao->tecnica ?? ''), 1, 1);
        $pdf->Cell(95, 6, 'SÍMBOLO: ' . ($producao->simbolo ?? ''), 1, 1);

        $pdf->Ln(10);

        /* ==================================================
         * TABELA – CABEÇALHO FIXO
         * ================================================== */
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(230, 230, 230);

        $pdf->Cell(15, 7, 'QTD', 1, 0, 'C', true);
        $pdf->Cell(45, 7, 'NOME', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'SUP', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'INF', 1, 0, 'C', true);
        $pdf->Cell(15, 7, 'Nº', 1, 0, 'C', true);
        $pdf->Cell(40, 7, 'ADICIONAL', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'MODELO', 1, 1, 'C', true);

        /* ==================================================
         * TABELA – CONTEÚDO (QUEBRA AUTOMÁTICA)
         * ================================================== */
        $pdf->SetFont('helvetica', '', 9);

        foreach ($grade as $linha) {

            $pdf->Cell(15, 6, $linha['quantidade'], 1);
            $pdf->Cell(45, 6, $linha['nome'], 1);
            $pdf->Cell(20, 6, $linha['superior'], 1);
            $pdf->Cell(20, 6, $linha['inferior'], 1);
            $pdf->Cell(15, 6, $linha['numero'], 1);
            $pdf->Cell(40, 6, $linha['adicional'], 1);
            $pdf->Cell(20, 6, $linha['modelo'], 1);
            $pdf->Ln();
        }

        /* ==================================================
         * OUTPUT
         * ================================================== */
        $pdf->Output('ficha_producao_os_' . $osNumero . '.pdf', 'I');
    }
    //PDF /\ /\ /\ /\ /\ /\ /\ /\ /\ /\
    // EXCEL===================================
    public function excel($osId)
    {
        error_reporting(0);
        ini_set('display_errors', 0);

        $this->load->model('Producao_model');

        $grade = $this->Producao_model->getGradeByOs($osId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cabeçalho FIXO (ordem da Duty)
        $cabecalho = [
            'QUANTIDADE',
            'NOME',
            'SUPERIOR',
            'INFERIOR',
            'Nº',
            'ADICIONAL',
            'UNISSEX'
        ];

        $coluna = 'A';
        foreach ($cabecalho as $titulo) {
            $sheet->setCellValue($coluna . '1', $titulo);
            $coluna++;
        }

        // Conteúdo
       $linhaExcel = 2;

foreach ($grade as $linha) {

    if (!is_array($linha)) {
        continue;
    }

    $sheet->setCellValue("A{$linhaExcel}", $linha['quantidade'] ?? '');
    $sheet->setCellValue("B{$linhaExcel}", $linha['nome'] ?? '');
    $sheet->setCellValue("C{$linhaExcel}", $linha['superior'] ?? '');
    $sheet->setCellValue("D{$linhaExcel}", $linha['inferior'] ?? '');
    $sheet->setCellValue("E{$linhaExcel}", $linha['numero'] ?? '');
    $sheet->setCellValue("F{$linhaExcel}", $linha['adicional'] ?? '');
    $sheet->setCellValue("G{$linhaExcel}", $linha['modelo'] ?? '');

    $linhaExcel++;
}


        // Download
        $nomeArquivo = 'ficha_producao_os_' . $osId . '.xlsx';
          if (ob_get_length()) {
             ob_end_clean();
                            } 

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
