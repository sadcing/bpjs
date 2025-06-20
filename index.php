<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ktp = $_POST['no_ktp'];
    $bpjs = $_POST['no_bpjs'];
    $nama = $_POST['nama'];
    $tanggal_raw = $_POST['tanggal'];
    $tanggal = date('m-Y', strtotime($tanggal_raw));

    // Gunakan direktori sementara agar tidak terjadi konflik file lock
    $timestamp = time();
    $docxPath = "/tmp/filled_$timestamp.docx";
    $pdfPath = "/tmp/output_$timestamp.pdf";

    // Salin template ke lokasi kerja
    copy('template.docx', $docxPath);

    // Buka docx dan isi variabel
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
        die('Gagal membuka template Word.');
    }

    // Konversi ke PDF dengan LibreOffice
    $docxSafe = escapeshellarg($docxPath);
    $cmd = "HOME=/tmp libreoffice --headless --convert-to pdf $docxSafe --outdir /tmp 2>&1";
    $output = [];
    exec($cmd, $output);

    // Debug jika gagal
    if (!file_exists($pdfPath)) {
        echo "<h3 style='color:red;'>Gagal generate PDF!</h3>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
        exit;
    }

    // Kirim PDF ke browser
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="output.pdf"');
    readfile($pdfPath);

    // Hapus file sementara
    unlink($docxPath);
    unlink($pdfPath);
    exit;
}
?>