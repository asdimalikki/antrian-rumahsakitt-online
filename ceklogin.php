<?php
session_start();
include "koneksi.php";

$email = $_POST['email'];
$password = $_POST['password'];

$query = mysqli_query($conn,
"SELECT * FROM users WHERE email='$email'");

if(mysqli_num_rows($query) > 0){

    $data = mysqli_fetch_assoc($query);

    if($password == $data['password']){

        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['email'] = $data['email'];
        $_SESSION['role'] = $data['role'];

        // Jika Admin
        if($data['role']=="admin"){

            echo "
            <script>
            alert('Selamat datang Admin');
            window.location='ADMIN/index.php';
            </script>";

        }else{

            echo "
            <script>
            alert('Login berhasil');
            window.location='pasien/index.php';
            </script>";

        }

    }else{

        echo "
        <script>
        alert('Password salah!');
        window.location='index.php';
        </script>";

    }

}else{

    echo "
    <script>
    alert('Email tidak ditemukan!');
    window.location='index.php';
    </script>";

}
?>
