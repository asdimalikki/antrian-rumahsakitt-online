<?php
include "koneksi.php";

if(isset($_GET['id']) && $_GET['id'] != ""){

    $id = mysqli_real_escape_string($conn, $_GET['id']);

    try {

        mysqli_query($conn,
        "DELETE FROM jadwal WHERE id_jadwal = '$id'");

        header("Location: jadwal.php");
        exit;

    } catch (mysqli_sql_exception $e) {

        // Gagal hapus karena jadwal ini masih punya data antrian terkait
        $pesan = urlencode("Gagal menghapus: jadwal yang ingin di hapus masih memiliki data antrian terkait. Hapus dulu data antriannya.");
        header("Location: jadwal.php?error=$pesan");
        exit;

    }

}

header("Location: jadwal.php");
exit;
?>