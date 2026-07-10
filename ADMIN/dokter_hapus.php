<?php
include "koneksi.php";

if(isset($_GET['id']) && $_GET['id'] != ""){

    $id = mysqli_real_escape_string($conn, $_GET['id']);

    try {

        mysqli_query($conn,
        "DELETE FROM dokter WHERE id_dokter = '$id'");

        header("Location: dokter.php");
        exit;

    } catch (mysqli_sql_exception $e) {

        // Gagal hapus karena dokter ini masih punya data jadwal atau antrian terkait
        $pesan = urlencode("Gagal menghapus: dokter yang ingin di hapus masih memiliki data jadwal atau antrian terkait. Hapus dulu data tersebut.");
        header("Location: dokter.php?error=$pesan");
        exit;

    }

}

header("Location: dokter.php");
exit;
?>