<?php 

/*
    Autor: benhur alencar azevedo
    utilidade: gera o pdf da lista telefonica
 */

namespace lib\services;

use Fpdf\Fpdf;

class PDFService extends Fpdf 
{
    function __construct()
    {
        parent::__construct('P', 'mm', 'A4');
    }
    public static function geraListaContatosPDF(array $data): PDFService
    {
        $pdf = new self();
        $pdf->AliasNbPages();
        $pdf->SetMargins(20,20);
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);

        #cabecalho do documento
        $pdf->MultiCell(0, 5, mb_convert_encoding('Lista de Contatos de ' . $data['nome'], 'UTF-8', mb_list_encodings()));
        $pdf->Ln(5);

        $pdf->SetFont('Arial', '', 12);

        foreach($data['contatos'] as $contato)
        {
            $pdf->MultiCell(0, 5, mb_convert_encoding('Nome: ' . $contato['nome'] . ', telefone: ' . $contato['telefone'], 'UTF-8', mb_list_encodings()));
            $pdf->Ln(5);
        }
        return $pdf;
    }
}