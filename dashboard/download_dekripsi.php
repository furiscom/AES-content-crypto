<?php
require_once '../config.php'; // Konfigurasi database

if (isset($_GET['id_file'])) {
    $idFile = $_GET['id_file'];

    // Ambil data file dari database
    $sql = "SELECT decrypted_file, file_name_source FROM `file` WHERE `id_file` = '$idFile'";
    $result = mysqli_query($connect, $sql);
    $file = mysqli_fetch_assoc($result);

    if ($file) {
        $decryptedFileData = $file['decrypted_file'];
        $fileName = $file['file_name_source'];

        // Download file
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        echo $decryptedFileData;
        exit;

    } else {
        echo "File tidak ditemukan.";
    }
} else {
    echo "ID file tidak ditemukan.";
}
?>