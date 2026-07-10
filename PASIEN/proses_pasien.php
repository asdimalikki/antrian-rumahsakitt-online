<?php
session_start();
include "koneksi.php";

$id_user = intval($_SESSION['id_user']);

// Ambil & bersihkan input
$id_pasien     = isset($_POST['id_pasien']) ? trim($_POST['id_pasien']) : '';
$nama          = mysqli_real_escape_string($conn, $_POST['nama']);
$nik           = mysqli_real_escape_string($conn, $_POST['nik']);
$no_hp         = mysqli_real_escape_string($conn, $_POST['no_hp']);
$alamat        = mysqli_real_escape_string($conn, $_POST['alamat']);
$tanggal_lahir = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
$jk            = mysqli_real_escape_string($conn, $_POST['jk']);
$email         = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';

// ===== Kategori pasien (umum / bpjs) =====
$jenis_pasien = isset($_POST['jenis_pasien']) ? trim($_POST['jenis_pasien']) : 'umum';
if (!in_array($jenis_pasien, ['umum', 'bpjs'])) {
    $jenis_pasien = 'umum';
}

// Kalau kategorinya "umum", no_bpjs dipaksa kosong (NULL) walau ada yang
// mengirim nilai lain lewat devtools/manipulasi request.
if ($jenis_pasien === 'bpjs') {
    $no_bpjs_raw = isset($_POST['no_bpjs']) ? trim($_POST['no_bpjs']) : '';

    if ($no_bpjs_raw === '') {
        die("No. BPJS wajib diisi untuk kategori pasien BPJS.");
    }

    $no_bpjs = "'" . mysqli_real_escape_string($conn, $no_bpjs_raw) . "'";
} else {
    $no_bpjs = "NULL";
}

if ($id_pasien === '') {
    /* =========================================
       MODE TAMBAH PASIEN BARU
       -> satu akun (id_user) boleh punya banyak
          baris di tabel pasien
       ========================================= */
    $query = mysqli_query($conn, "
        INSERT INTO pasien
            (id_user, nama_lengkap, nik, no_hp, no_bpjs, jenis_pasien, alamat, tanggal_lahir, jenis_kelamin, email)
        VALUES
            ('$id_user', '$nama', '$nik', '$no_hp', $no_bpjs, '$jenis_pasien', '$alamat', '$tanggal_lahir', '$jk', '$email')
    ");

    $id_pasien_final = mysqli_insert_id($conn);

} else {
    /* =========================================
       MODE UPDATE PASIEN YANG SUDAH ADA
       -> WHERE id_user disertakan supaya user A
          tidak bisa mengubah data pasien milik
          user B (keamanan)
       ========================================= */
    $id_pasien = intval($id_pasien);

    // Catatan: kolom "email" SENGAJA tidak dimasukkan ke SET.
    // Email pasien yang sudah terdaftar tidak boleh diubah lewat form ini,
    // jadi walau ada yang mengirim nilai email lain (misal lewat devtools),
    // nilai email di database tetap tidak berubah.
    $query = mysqli_query($conn, "
        UPDATE pasien
        SET nama_lengkap='$nama',
            nik='$nik',
            no_hp='$no_hp',
            no_bpjs=$no_bpjs,
            jenis_pasien='$jenis_pasien',
            alamat='$alamat',
            tanggal_lahir='$tanggal_lahir',
            jenis_kelamin='$jk'
        WHERE id_pasien='$id_pasien' AND id_user='$id_user'
    ");

    $id_pasien_final = $id_pasien;
}

if (!$query) {
    die("Terjadi kesalahan menyimpan data pasien: " . mysqli_error($conn));
}

$_SESSION['id_pasien'] = $id_pasien_final;

// lanjut ke step berikutnya (Pilih Jadwal) sambil membawa id_pasien yang aktif
header("Location: pilih_layanan.php?id_pasien=" . $id_pasien_final);
exit;