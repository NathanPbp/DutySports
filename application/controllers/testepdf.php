<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'third_party/tcpdf/tcpdf.php';

class TestePdf extends CI_Controller
{
    public function index()
    {
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 14);
        $pdf->Cell(0, 10, 'TCPDF OK', 0, 1);
        $pdf->Output('teste.pdf', 'I');
    }
}
