<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['id_jadwal']) || !isset($_SESSION['id_user'])) {
    header("Location: pilih_jadwal.php");
    exit;
}

$id_jadwal   = (int)$_SESSION['id_jadwal'];
$id_user     = (int)$_SESSION['id_user'];
$tanggal     = $_SESSION['tanggal'];
$jam_mulai   = $_SESSION['jam_mulai'];
$jam_selesai = $_SESSION['jam_selesai'];
$metode      = isset($_POST['metode']) ? $_POST['metode'] : 'Transfer';
$total_biaya = 160000;

// ── 1. Ambil id_pasien 
$q = mysqli_query($conn, "SELECT id_pasien FROM pasien 
                           WHERE id_user = $id_user LIMIT 1");
$pasien = mysqli_fetch_assoc($q);
if (!$pasien) die("Data pasien tidak ditemukan.");
$id_pasien = (int)$pasien['id_pasien'];

// ── 2. Ambil id_dokter & id_poli dari jadwal 
$q2 = mysqli_query($conn, "SELECT j.id_dokter, d.id_poli 
                            FROM jadwal j
                            JOIN dokter d ON j.id_dokter = d.id_dokter
                            WHERE j.id_jadwal = $id_jadwal LIMIT 1");
$jadwal = mysqli_fetch_assoc($q2);
if (!$jadwal) die("Data jadwal tidak ditemukan.");
$id_dokter = (int)$jadwal['id_dokter'];
$id_poli   = (int)$jadwal['id_poli'];

// ── 3. Ambil nama dokter & poli 
$q3 = mysqli_query($conn, "SELECT d.nama_dokter, p.nama_poli, p.lokasi
                            FROM dokter d 
                            JOIN poli p ON d.id_poli = p.id_poli
                            WHERE d.id_dokter = $id_dokter LIMIT 1");
$nama        = mysqli_fetch_assoc($q3);
$nama_dokter = $nama['nama_dokter'] ?? '-';
$nama_poli   = $nama['nama_poli']   ?? '-';
$lokasi_poli = $nama['lokasi']      ?? '-';

// ── 4. Generate kode & nomor antrian 
$q4 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM antrian 
                            WHERE id_jadwal = $id_jadwal 
                            AND tanggal_kunjungan = '$tanggal'");
$row4       = mysqli_fetch_assoc($q4);
$no_antrian = (int)$row4['total'] + 1;
$kode       = 'ANT-' . date('Ymd') . '-' . str_pad($no_antrian, 3, '0', STR_PAD_LEFT);

// ── 5. Insert ke tabel antrian 
$sql_antrian = "INSERT INTO antrian 
                (kode_antrian, id_pasien, id_poli, id_dokter, id_jadwal, 
                 tanggal_kunjungan, nomor_antrian, status)
                VALUES 
                ('$kode', $id_pasien, $id_poli, $id_dokter, $id_jadwal,
                 '$tanggal', $no_antrian, 'Menunggu')";

$hasil_antrian = mysqli_query($conn, $sql_antrian);
if (!$hasil_antrian) die("Gagal insert antrian: " . mysqli_error($conn));

$id_antrian = mysqli_insert_id($conn);
if ($id_antrian == 0) die("id_antrian tidak valid setelah insert.");

// ── 6. Insert ke tabel pembayaran 
$waktu_bayar = date('Y-m-d H:i:s');
$sql_bayar   = "INSERT INTO pembayaran 
                (id_antrian, metode, total_biaya, status_bayar, waktu_bayar)
                VALUES 
                ($id_antrian, '$metode', $total_biaya, 'Lunas', '$waktu_bayar')";

$hasil_bayar = mysqli_query($conn, $sql_bayar);
if (!$hasil_bayar) die("Gagal insert pembayaran: " . mysqli_error($conn));

// ── 7. Simpan semua ke session 
$_SESSION['kode_antrian'] = $kode;
$_SESSION['no_antrian']   = $no_antrian;
$_SESSION['metode_bayar'] = $metode;
$_SESSION['waktu_bayar']  = $waktu_bayar;
$_SESSION['nama_dokter']  = $nama_dokter;
$_SESSION['nama_poli']    = $nama_poli;
$_SESSION['lokasi_poli']  = $lokasi_poli;

// ── 8. Hapus session jadwal 
unset($_SESSION['id_jadwal'], $_SESSION['tanggal'],
      $_SESSION['jam_mulai'], $_SESSION['jam_selesai']);

// ── 9. Redirect ke halaman selesai 
header("Location: selesai.php");
exit;
?>