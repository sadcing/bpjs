<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$ktp = $_POST['no_ktp'];
$bpjs = $_POST['no_bpjs'];
$nama = $_POST['nama'];
$tanggal_raw = $_POST['tanggal'];
$tanggal = date('m-Y', strtotime($tanggal_raw));

    $docxPath = 'filled_' . time() . '.docx';
    $pdfPath = 'output_' . time() . '.pdf';

    // Copy template
    copy('template.docx', $docxPath);

    // Open the docx as zip
    $zip = new ZipArchive;
    if ($zip->open($docxPath) === TRUE) {
        $content = $zip->getFromName('word/document.xml');
        $content = str_replace('{{NO_KTP}}', $ktp, $content);
        $content = str_replace('{{NO_BPJS}}', $bpjs, $content);
        $content = str_replace('{{NAMA}}', $nama, $content);
        $content = str_replace('{{TANGGAL}}', $tanggal, $content);
        $zip->addFromString('word/document.xml', $content);
        $zip->close();
    } else {
        die('Failed to open template.');
    }

    // Convert to PDF using LibreOffice
    $cmd = "libreoffice --headless --convert-to pdf $docxPath --outdir .";
    exec($cmd);

    // Output PDF to user
    header('Content-Type: application/pdf');
    header("Content-Disposition: attachment; filename=\"$pdfPath\"");
    readfile($pdfPath);

    // Cleanup
    unlink($docxPath);
    unlink($pdfPath);
    exit;
}
?>