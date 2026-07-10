<?php
include "koneksi.php";
 
if(isset($_GET['id']) && $_GET['id'] != ""){
 
    $id = mysqli_real_escape_string($conn, $_GET['id']);
 
    try {
 
        mysqli_query($conn,
        "DELETE FROM users WHERE id_user = '$id'");
 
        header("Location: index.php");
        exit;
 
    } catch (mysqli_sql_exception $e) {
 
        // Tangkap error foreign key (user masih punya data pasien terkait)
        $pesan = urlencode("Gagal menghapus: user yang ingin di hapus masih memiliki data pasien terkait. Hapus dulu data pasiennya.");
        header("Location: index.php?error=$pesan");
        exit;
 
    }
 
}
 
header("Location: index.php");
exit;
?>