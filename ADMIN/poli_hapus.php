<?php
include "koneksi.php";

if(isset($_GET['id']) && $_GET['id'] != ""){

    $id = mysqli_real_escape_string($conn, $_GET['id']);

    try {

        mysqli_query($conn,
        "DELETE FROM poli WHERE id_poli = '$id'");

        header("Location: poli.php");
        exit;

    } catch (mysqli_sql_exception $e) {

        // Gagal hapus karena poli ini masih punya data dokter terkait
        $pesan = urlencode("Gagal menghapus: poli yang ingin di hapus masih memiliki data dokter terkait. Hapus dulu data dokternya.");
        header("Location: poli.php?error=$pesan");
        exit;

    }

}

header("Location: poli.php");
exit;
?>