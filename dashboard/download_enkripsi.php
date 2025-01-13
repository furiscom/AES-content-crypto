<?php
require_once '../config.php'; // Konfigurasi database

if (isset($_GET['id_file'])) {
    $idFile = $_GET['id_file'];

    // Ambil nama file dari database
    $sql = "SELECT file_name_finish FROM `file` WHERE `id_file` = '$idFile'";
    $result = mysqli_query($connect, $sql);
    $file = mysqli_fetch_assoc($result);

    if ($file) {
        $fileName = $file['file_name_finish'];
        $filePath = 'uploads/' . $fileName; // Path ke file di folder uploads

        if (file_exists($filePath)) {
            // Download file
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            echo "File tidak ditemukan.";
        }
    } else {
        echo "Data file tidak ditemukan di database.";
    }
} else {
    echo "ID file tidak ditemukan.";
}
?>