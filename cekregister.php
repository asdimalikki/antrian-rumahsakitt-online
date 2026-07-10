<?php
session_start();
include "koneksi.php";

// Ambil data dari form
$nama       = mysqli_real_escape_string($conn, $_POST['nama']);
$email      = mysqli_real_escape_string($conn, $_POST['email']);
$no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);
$password   = $_POST['password'];

// Username otomatis dari email
$username = explode("@", $email)[0];

// Cek apakah email sudah digunakan
$cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

if(mysqli_num_rows($cek) > 0){

    echo "<script>
            alert('Email sudah terdaftar!');
            window.location='register.php';
          </script>";
    exit;
}

// Cek apakah nomor telepon sudah digunakan
$cekHp = mysqli_query($conn, "SELECT * FROM users WHERE no_hp='$no_telepon'");

if(mysqli_num_rows($cekHp) > 0){

    echo "<script>
            alert('Nomor telepon sudah terdaftar!');
            window.location='register.php';
          </script>";
    exit;
}

// Jika username sudah dipakai maka tambahkan angka random
$cekUsername = mysqli_query($conn,"SELECT * FROM users WHERE username='$username'");

if(mysqli_num_rows($cekUsername) > 0){
    $username = $username.rand(100,999);
}

// Enkripsi password
$passwordHash = $password;

// Simpan ke tabel users
$queryUser = mysqli_query($conn,"INSERT INTO users
(
nama,
email,
no_hp,
username,
password,
role,
status
)

VALUES
(
'$nama',
'$email',
'$no_telepon',
'$username',
'$passwordHash',
'pasien',
'aktif'
)");

if($queryUser){

    // Ambil id_user yang baru dibuat
    $id_user = mysqli_insert_id($conn);

    mysqli_query($conn,"INSERT INTO pasien
    (
        id_user,
        nama_lengkap,
        no_hp,
        email
    )

    VALUES
    (
        '$id_user',
        '$nama',
        '$no_telepon',
        '$email'
    )");

    echo "<script>
            alert('Registrasi berhasil, silakan login');
            window.location='index.php';
          </script>";

}else{

    echo "<script>
            alert('Registrasi gagal!');
            window.location='register.php';
          </script>";
}
?>