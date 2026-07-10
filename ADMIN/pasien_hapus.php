<?php
include "koneksi.php";
 
if(isset($_GET['id']) && $_GET['id'] != ""){
 
    $id = mysqli_real_escape_string($conn, $_GET['id']);
 
    try {
 
        mysqli_query($conn,
        "DELETE FROM pasien WHERE id_pasien = '$id'");
 
        header("Location: pasien.php");
        exit;
 
    } catch (mysqli_sql_exception $e) {
 
        // Gagal hapus karena pasien ini masih punya data antrian terkait
        $pesan = urlencode("Gagal menghapus: pasien yang ingin di hapus masih memiliki data antrian terkait. Hapus dulu data antriannya.");
        header("Location: pasien.php?error=$pesan");
        exit;
 
    }
 
}
 
header("Location: pasien.php");
exit;
?>