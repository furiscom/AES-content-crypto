<?php
session_start();
include('../config.php');
include "AES.php"; //memasukan file AES

$id_file = $_REQUEST['id_file'];
$pwdfile = $_REQUEST['pwdfile'];
$key = mysqli_escape_string($connect, substr(md5($pwdfile), 0, 16));

$query = mysqli_query($connect, "SELECT * FROM file WHERE id_file='$id_file'");
$data = mysqli_fetch_array($query);
$nama_file = $data['file_name_finish'];
$kunci = $data['password'];

$path = "hasil_ekripsi/" . $nama_file;
$path_decrypt = "hasil_dekripsi/" . str_replace(".rda", "", $nama_file);

if (file_exists($path)) {
    $file_source = fopen($path, 'rb');
    $file_output = fopen($path_decrypt, 'wb');
    $mod = $data['file_size'] % 16;
    if ($mod == 0) {
        $banyak = $data['file_size'] / 16;
    } else {
        $banyak = ($data['file_size'] - $mod) / 16;
        $banyak = $banyak + 1;
    }

    if ($pwdfile == $kunci) {
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        $aes = new AES($key);
        for ($bawah = 0; $bawah < $banyak; $bawah++) {
            $data = fread($file_source, 16);
            $cipher = $aes->decrypt($data);
            fwrite($file_output, $cipher);
        }

        fclose($file_source);
        fclose($file_output);

        // Baca data file hasil dekripsi
        $decryptedFileData = file_get_contents($path_decrypt);
        $decryptedFileData = mysqli_real_escape_string($connect, $decryptedFileData);

        // Update query UPDATE dengan decrypted_file_data
        $sql_update = "UPDATE file SET decrypted_file = '$decryptedFileData', status = '2' WHERE id_file = '$id_file'";
        $query_update = mysqli_query($connect, $sql_update);

        if ($query_update) {
            header('location:dekripsi.php?pesan=berhasil');
        } else {
            echo "Gagal mendekripsi file: " . mysqli_error($connect);
        }
    } else {
        echo ("<script language='javascript'>
                window.location.href='dekripsi.php';
                window.alert('Password Salah!');
                </script>");
    }
} else {
    echo ("<script language='javascript'>
            window.location.href='dekripsi.php';
            window.alert('File tidak ada!');
            </script>");
}
?>