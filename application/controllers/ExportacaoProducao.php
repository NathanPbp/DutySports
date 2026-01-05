<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExportacaoProducao extends CI_Controller
{
    /* =========================================================
     * PDF (já estava funcionando – mantido igual)
     * ========================================================= */
    public function pdf($osId)
    {
        $this->load->model('os_model');
        $this->load->model('mapos_model');
        $this->load->model('Producao_model');

        $os       = $this->os_model->getById($osId);
        $emitente = $this->mapos_model->getEmitente();
        $producao = $this->Producao_model->getProducaoByOs($osId);
        $grade    = $this->Producao_model->getGradeByOs($osId);

        if (!$os) {
            show_error('OS não encontrada');
        }

        $osNumero    = str_pad($os->idOs, 4, '0', STR_PAD_LEFT);
        $responsavel = $this->session->userdata('nome_admin') ?? '';
        $emissao     = date('d/m/Y H:i');

        $logo = null;
        if (!empty($emitente->url_logo)) {
            $logoTemp = FCPATH . 'assets/uploads/' . $emitente->url_logo;
            if (file_exists($logoTemp)) {
                $logo = $logoTemp;
            }
        }

        $this->load->library('PdfDuty');

        $pdf = new PdfDuty('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(10, 35, 10);
        $pdf->SetAutoPageBreak(true, 20);

        $pdf->setDadosCabecalho($logo, $osNumero, $responsavel, $emissao);
        $pdf->AddPage();

        $pdf->SetFillColor(255, 211, 0);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, 'DATA DE ENTREGA', 0, 1, 'L', true);

        $telefone = $os->telefone_cliente ?? $os->celular_cliente ?? '';

        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(95, 8, 'CLIENTE: ' . $os->nomeCliente, 1);
        $pdf->Cell(95, 8, 'TELEFONE: ' . $telefone, 1, 1);

        $pdf->Cell(95, 8, 'PEDIDO Nº: ' . $osNumero, 1);
        $pdf->Cell(95, 8, 'VENDEDOR: ' . $responsavel, 1, 1);

        $pdf->Ln(4);

        $yInicio = $pdf->GetY();

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetXY(10, $yInicio);
        $pdf->Cell(90, 8, 'ARTE', 1, 1);
        $pdf->Rect(10, $yInicio + 8, 90, 90);

        if ($producao && !empty($producao->arte_imagem)) {
            $artePath = FCPATH . $producao->arte_imagem;
            if (file_exists($artePath)) {
                $pdf->Image($artePath, 10, $yInicio + 8, 90, 0);
            }
        }

        $pdf->SetXY(105, $yInicio);
        $pdf->Cell(95, 8, 'INFORMAÇÕES', 1, 1);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetX(105);
        $pdf->MultiCell(
            95,
            8,
            "TECIDO: " . ($producao->tecido ?? '') . "\n" .
            "GOLA: " . ($producao->gola ?? '') . "\n" .
            "TÉCNICA: " . ($producao->tecnica ?? '') . "\n" .
            "SÍMBOLO: " . ($producao->simbolo ?? '') . "\n\n" .
            "OBS:\n" . ($producao->observacao ?? ''),
            1
        );

        $pdf->Ln(4);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(230,230,230);

        $pdf->Cell(12, 8, 'QTD', 1, 0, 'C', true);
        $pdf->Cell(45, 8, 'NOME', 1, 0, 'C', true);
        $pdf->Cell(15, 8, 'SUP', 1, 0, 'C', true);
        $pdf->Cell(15, 8, 'INF', 1, 0, 'C', true);
        $pdf->Cell(15, 8, 'Nº', 1, 0, 'C', true);
        $pdf->Cell(45, 8, 'ADICIONAL', 1, 0, 'C', true);
        $pdf->Cell(23, 8, 'MODELO', 1, 1, 'C', true);

        $pdf->SetFont('helvetica', '', 9);

        foreach ($grade as $linha) {
            if ($pdf->GetY() > 260) {
                $pdf->AddPage();
            }

            $pdf->Cell(12, 8, $linha['quantidade'] ?? '', 1);
            $pdf->Cell(45, 8, $linha['nome'] ?? '', 1);
            $pdf->Cell(15, 8, $linha['superior'] ?? '', 1);
            $pdf->Cell(15, 8, $linha['inferior'] ?? '', 1);
            $pdf->Cell(15, 8, $linha['numero'] ?? '', 1);
            $pdf->Cell(45, 8, $linha['adicional'] ?? '', 1);
            $pdf->Cell(23, 8, $linha['modelo'] ?? '', 1, 1);
        }

        $pdf->Output('OS_' . $osNumero . '.pdf', 'I');
    }

    /* =========================================================
     * EXCEL – GRADE DE PRODUÇÃO (CORRIGIDO)
     * ========================================================= */
   public function excel($osId)
{
    if (!$osId) {
        show_error('OS inválida');
    }

    // Model
    $this->load->model('Producao_model');

    // Busca apenas a grade
    $grade = $this->Producao_model->getGradeByOs($osId);

    if (empty($grade)) {
        show_error('Nenhuma grade encontrada para esta OS');
    }

    // Composer autoload
    require_once FCPATH . 'vendor/autoload.php';

    // PhpSpreadsheet
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Grade de Produção');

    // ===============================
    // CABEÇALHO
    // ===============================
    $headers = [
        'QTD',
        'NOME',
        'SUPERIOR',
        'INFERIOR',
        'Nº',
        'ADICIONAL',
        'MODELO'
    ];

    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '1', $header);
        $sheet->getStyle($col . '1')->getFont()->setBold(true);
        $col++;
    }

    // ===============================
    // DADOS
    // ===============================
    $row = 2;
    foreach ($grade as $item) {
        $sheet->setCellValue('A' . $row, $item['quantidade']);
        $sheet->setCellValue('B' . $row, $item['nome']);
        $sheet->setCellValue('C' . $row, $item['superior']);
        $sheet->setCellValue('D' . $row, $item['inferior']);
        $sheet->setCellValue('E' . $row, $item['numero']);
        $sheet->setCellValue('F' . $row, $item['adicional']);
        $sheet->setCellValue('G' . $row, $item['modelo']);
        $row++;
    }

    // Ajusta largura das colunas
    foreach (range('A', 'G') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // ===============================
    // DOWNLOAD
    // ===============================
    $filename = 'GRADE_PRODUCAO_OS_' . str_pad($osId, 4, '0', STR_PAD_LEFT) . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

}
