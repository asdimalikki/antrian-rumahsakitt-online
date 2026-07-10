<?php
session_start();
include "koneksi.php";
/* =========================================================
   VALIDASI SESSION
========================================================= */

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['id_pasien'])) {
    header("Location: buat_antrian.php");
    exit;
}

if (
    !isset($_SESSION['id_jadwal']) ||
    !isset($_SESSION['tanggal']) ||
    !isset($_SESSION['jam_mulai']) ||
    !isset($_SESSION['jam_selesai'])
) {
    header("Location: pilih_jadwal.php");
    exit;
}

$id_user      = (int)$_SESSION['id_user'];
$id_pasien    = (int)$_SESSION['id_pasien'];
$id_jadwal    = (int)$_SESSION['id_jadwal'];
$tanggal      = $_SESSION['tanggal'];
$jam_mulai    = $_SESSION['jam_mulai'];
$jam_selesai  = $_SESSION['jam_selesai'];

$tanggalEsc = mysqli_real_escape_string($conn, $tanggal);

/* =========================================================
   PASTIKAN PASIEN MILIK USER YANG LOGIN
========================================================= */

$cekPasien = mysqli_query($conn,"
SELECT *
FROM pasien
WHERE id_pasien='$id_pasien'
AND id_user='$id_user'
LIMIT 1
");

if(mysqli_num_rows($cekPasien)==0){
    die("Pasien tidak ditemukan.");
}

/* =========================================================
   AMBIL DATA JADWAL
========================================================= */

$queryJadwal = mysqli_query($conn,"
SELECT
j.id_jadwal,
j.id_dokter,
d.nama_dokter,
d.id_poli,
p.nama_poli,
p.lokasi
FROM jadwal j
JOIN dokter d
ON j.id_dokter=d.id_dokter
JOIN poli p
ON d.id_poli=p.id_poli
WHERE j.id_jadwal='$id_jadwal'
LIMIT 1
");

if(mysqli_num_rows($queryJadwal)==0){
    die("Jadwal tidak ditemukan.");
}

$data = mysqli_fetch_assoc($queryJadwal);

$id_dokter   = $data['id_dokter'];
$id_poli     = $data['id_poli'];
$nama_dokter = $data['nama_dokter'];
$nama_poli   = $data['nama_poli'];
$lokasi_poli = $data['lokasi'];

/* =========================================================
   CEK DUPLIKAT
========================================================= */

$cek = mysqli_query($conn,"
SELECT *
FROM antrian
WHERE id_pasien='$id_pasien'
AND id_jadwal='$id_jadwal'
AND tanggal_kunjungan='$tanggalEsc'
LIMIT 1
");

if(mysqli_num_rows($cek)>0){

    $a = mysqli_fetch_assoc($cek);

    $_SESSION['kode_antrian']      = $a['kode_antrian'];
    $_SESSION['no_antrian']        = $a['nomor_antrian'];
    $_SESSION['nama_dokter']       = $nama_dokter;
    $_SESSION['nama_poli']         = $nama_poli;
    $_SESSION['lokasi_poli']       = $lokasi_poli;
    $_SESSION['tanggal_kunjungan'] = $tanggal;
    $_SESSION['jam_mulai']         = $jam_mulai;
    $_SESSION['jam_selesai']       = $jam_selesai;

    header("Location: selesai.php");
    exit;
}

/* =========================================================
   NOMOR ANTRIAN
========================================================= */

$nomor = 1;

/* =========================================================
   CEK APAKAH SUDAH ADA YANG SEDANG DILAYANI
========================================================= */

$status = "Sedang Dilayani";

$cekSedang = mysqli_query($conn,"
SELECT id_antrian
FROM antrian
WHERE id_poli='$id_poli'
AND tanggal_kunjungan='$tanggalEsc'
AND status='Sedang Dilayani'
LIMIT 1
");

if(mysqli_num_rows($cekSedang)>0){
    $status = "Menunggu";
}

$q = mysqli_query($conn,"
SELECT MAX(nomor_antrian) maxno
FROM antrian
WHERE id_poli='$id_poli'
AND tanggal_kunjungan='$tanggalEsc'
");

$r = mysqli_fetch_assoc($q);

if($r['maxno']!=NULL){
    $nomor = $r['maxno'] + 1;
}

/* =========================================================
   KODE ANTRIAN
========================================================= */

$kode = "AQ".date("ymd").str_pad($nomor,3,"0",STR_PAD_LEFT);

/* =========================================================
   INSERT
========================================================= */

$simpan = mysqli_query($conn,"
INSERT INTO antrian
(
kode_antrian,
id_pasien,
id_poli,
id_dokter,
id_jadwal,
tanggal_kunjungan,
nomor_antrian,
status,
created_at
)
VALUES
(
'$kode',
'$id_pasien',
'$id_poli',
'$id_dokter',
'$id_jadwal',
'$tanggalEsc',
'$nomor',
'$status',
NOW()
)
");

if(!$simpan){
    die(mysqli_error($conn));
}

/* =========================================================
   SESSION UNTUK SELESAI.PHP
========================================================= */

$_SESSION['kode_antrian']      = $kode;
$_SESSION['no_antrian']        = $nomor;
$_SESSION['nama_dokter']       = $nama_dokter;
$_SESSION['nama_poli']         = $nama_poli;
$_SESSION['lokasi_poli']       = $lokasi_poli;
$_SESSION['tanggal_kunjungan'] = $tanggal;
$_SESSION['jam_mulai']         = $jam_mulai;
$_SESSION['jam_selesai']       = $jam_selesai;

/* =========================================================
   HAPUS SESSION YANG TIDAK DIPERLUKAN
========================================================= */

unset($_SESSION['id_jadwal']);
unset($_SESSION['tanggal']);
unset($_SESSION['jam_mulai']);
unset($_SESSION['jam_selesai']);

/* =========================================================
   SELESAI
========================================================= */

header("Location: selesai.php");
exit;