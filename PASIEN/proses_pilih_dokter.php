<?php
session_start();
include "koneksi.php";

$id_dokter = $_GET['id_dokter'];

//ambil data dokter
$query = mysqli_query($conn,"
SELECT dokter.*, poli.nama_poli
FROM dokter
JOIN poli ON dokter.id_poli=poli.id_poli
WHERE dokter.id_dokter='$id_dokter'
");

$data = mysqli_fetch_assoc($query);

//simpan session
$_SESSION['id_dokter']=$data['id_dokter'];
$_SESSION['id_poli']=$data['id_poli'];
$_SESSION['nama_dokter']=$data['nama_dokter'];
$_SESSION['nama_poli']=$data['nama_poli'];

header("Location: pilih_jadwal.php");