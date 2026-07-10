<?php
include "koneksi.php";

if(isset($_GET['id']) && $_GET['id'] != ""){

    $id = mysqli_real_escape_string($conn, $_GET['id']);

    mysqli_query($conn,
    "DELETE FROM antrian WHERE id_antrian = '$id'");

    header("Location: antrian.php");
    exit;

}

header("Location: antrian.php");
exit;
?>