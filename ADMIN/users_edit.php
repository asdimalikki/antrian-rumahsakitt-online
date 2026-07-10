<?php
include "koneksi.php";

if(!isset($_GET['id']) || $_GET['id'] == ""){
    header("Location: index.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Proses update saat form disubmit
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $update = mysqli_query($conn,
    "UPDATE users SET
        nama = '$nama',
        email = '$email'
    WHERE id_user = '$id'");

    if($update){

    mysqli_query($conn,
    "UPDATE pasien SET
        nama_lengkap = '$nama',
        email = '$email'
    WHERE id_user = '$id'");

        header("Location: index.php");
        exit;
    } else {
        $pesan_error = "Gagal mengupdate data: " . mysqli_error($conn);
    }
}

// Ambil data lama untuk ditampilkan di form
$query = mysqli_query($conn,
"SELECT * FROM users WHERE id_user = '$id'");

if(mysqli_num_rows($query) == 0){
    header("Location: index.php");
    exit;
}

$data = mysqli_fetch_assoc($query);
?>

<html>
<head>
<title>Edit User</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}

body{
    background:#f4f5f9;
}

/* SIDEBAR */

.sidebar{
    width:240px;
    height:100vh;
    background: #486fb7 45%;
    position:fixed;
    left:0;
    top:0;
    color:white;
    padding:35px 25px;
    border-top-right-radius:100px;
    border-bottom-right-radius:100px;
    overflow:hidden;
    display:flex;
    flex-direction:column;
}

.sidebar::before{
    content:"";
    width:300px;
    height:300px;
    background:rgba(255,255,255,0.08);
    border-radius:50%;
    position:absolute;
    top:-120px;
    left:-80px;
}

.sidebar::after{
    content:"";
    width:250px;
    height:250px;
    background:rgba(255,255,255,0.06);
    border-radius:50%;
    position:absolute;
    bottom:100px;
    left:-120px;
}

.logo{
    font-size:30px;
    font-weight:bold;
    position:relative;
    z-index:2;
}

.menu{
    list-style:none;
    margin-top:50px;
    position:relative;
    z-index:2;
}

.menu li{
    padding:14px 18px;
    margin-bottom:15px;
    border-radius:10px;
    width:170px;
    display:flex;
    gap:12px;
    cursor:pointer;
}

.menu li:hover,
.menu .aktif{
    background: rgba(255,255,255,0.15);
}

.menu li a{
    color:white;
    text-decoration:none;
    display:flex;
    gap:12px;
    align-items:center;
    width:100%;
}

/* LOGOUT (bawah sidebar) */

.logout-wrap{
    margin-top:auto;
    position:relative;
    z-index:2;
}

.logout-wrap a{
    display:flex;
    align-items:center;
    gap:12px;
    color:white;
    text-decoration:none;
    padding:14px 18px;
    border-radius:10px;
    width:170px;
    background:rgba(255,255,255,0.1);
    transition: all 0.2s ease;
}

.logout-wrap a:hover{
    background:#ff3d5a;
}

/* KONTEN */

.konten{
    margin-left:240px;
    padding:40px 40px 60px 40px;
    min-height:100vh;
}

.header{
    display:flex;
    justify-content:flex-end;
    align-items:center;
    gap:20px;
}

.profil{
    display:flex;
    align-items:center;
}

.profil img{
    width:45px;
    height:45px;
    border-radius:50%;
    margin-right:10px;
}

.profil div{
    line-height:1.3;
}

/* AREA TENGAH */

.wrapper-form{
    display:flex;
    flex-direction:column;
    align-items:center;
    margin-top:20px;
}

/* JUDUL HALAMAN */

.judul{
    margin-top:-20px;
    margin-bottom:10px;
    text-align:center;
}

.judul h1{
    font-size:28px;
    color:#2b2f3a;
    font-weight:800;
    letter-spacing:0.2px;
}

.judul p{
    color:#8a8f9c;
    margin-top:6px;
    font-size:15px;
}

/* KOTAK FORM */

.kotak-form{
    background:white;
    padding:40px 45px;
    border-radius:24px;
    margin-top:25px;
    box-shadow:0 10px 35px rgba(0,0,0,0.07);
    width:100%;
    max-width:560px;
    border:1px solid #f0f1f5;
}

.form-group{
    margin-bottom:22px;
}

.form-group label{
    display:block;
    margin-bottom:8px;
    color:#4a4f5c;
    font-size:13px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:0.4px;
}

.form-group .input-icon{
    position:relative;
}

.form-group .input-icon i{
    position:absolute;
    left:18px;
    top:50%;
    transform:translateY(-50%);
    color:#a7acb8;
    font-size:14px;
}

.form-group input{
    width:100%;
    padding:14px 18px 14px 44px;
    border:1.5px solid #e6e8ee;
    border-radius:12px;
    background:#fafbfc;
    font-size:14px;
    color:#2b2f3a;
    transition:0.2s ease;
}

.form-group input:focus{
    outline:none;
    border-color:#486fb7;
    background:white;
    box-shadow:0 0 0 4px rgba(72,111,183,0.12);
}

.form-group input:disabled{
    color:#9aa0ab;
    cursor:not-allowed;
}

.aksi-form{
    display:flex;
    justify-content:center;
    gap:12px;
    margin-top:34px;
}

.btn-simpan{
    background: linear-gradient(135deg, #486fb7, #3a5a99);
    color:white;
    border:none;
    padding:14px 32px;
    border-radius:30px;
    cursor:pointer;
    font-weight:bold;
    font-size:14px;
    box-shadow:0 8px 18px rgba(72,111,183,0.3);
    transition:0.2s ease;
}

.btn-simpan:hover{
    transform:translateY(-1px);
    box-shadow:0 10px 22px rgba(72,111,183,0.4);
}

.btn-batal{
    background:#f1f2f6;
    color:#5a5f6b;
    border:none;
    padding:14px 32px;
    border-radius:30px;
    cursor:pointer;
    font-weight:bold;
    font-size:14px;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    transition:0.2s ease;
}

.btn-batal:hover{
    background:#e6e8ee;
}

.pesan-error{
    background:#ffe3e6;
    color:#ff3d5a;
    padding:14px 20px;
    border-radius:10px;
    margin-bottom:20px;
    font-size:14px;
    display:flex;
    align-items:center;
    gap:10px;
}

a{
    text-decoration:none;
}
</style>
</head>

<body>

<div class="sidebar">

    <div class="logo">
        Sequentra<br>
        Health
    </div>

    <ul class="menu">
        <li class="aktif">
            <a href="index.php">
                <i class="fa-solid fa-users"></i>
                Data Users
            </a>
        </li>

        <li>
            <a href="pasien.php">
                <i class="fa-solid fa-user"></i>
                Data Pasien
            </a>
        </li>

        <li>
            <a href="antrian.php">
                <i class="fa-solid fa-user-doctor"></i>
                Data Antrian
            </a>
        </li>

        <li>
            <a href="dokter.php">
                <i class="fa-solid fa-user-doctor"></i>
                Data Dokter
            </a>
        </li>

        <li>
            <a href="jadwal.php">
                <i class="fa-solid fa-calendar-days"></i>
                Data Jadwal
            </a>
        </li>

        <li>
            <a href="poli.php">
                <i class="fa-solid fa-hospital"></i>
                Data Poli
            </a>
        </li>
    </ul>

    <div class="logout-wrap">
        <a href="logout.php" onclick="return confirm('Yakin ingin logout?')">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
        </a>
    </div>

</div>

<div class="konten">

    <div class="header">

        <div class="profil">
            <img src="https://i.pinimg.com/736x/d3/84/d5/d384d5d973fa729d9db608e537e77623.jpg">
            <div>
                <strong>HiDoyy</strong><br>
                <small>Admin</small>
            </div>
        </div>

    </div>

    <div class="wrapper-form">

        <div class="judul">
            <h1>Edit Data User</h1>
            <p>Ubah informasi user di bawah ini lalu simpan perubahan.</p>
        </div>

        <div class="kotak-form">

            <?php if(isset($pesan_error)){ ?>
                <div class="pesan-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= $pesan_error; ?>
                </div>
            <?php } ?>

            <form method="POST">

                <div class="form-group">
                    <label>ID User</label>
                    <div class="input-icon">
                        <i class="fa-solid fa-hashtag"></i>
                        <input type="text" value="<?= $data['id_user']; ?>" disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <div class="input-icon">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <div class="input-icon">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" name="email" value="<?= htmlspecialchars($data['email']); ?>" required>
                    </div>
                </div>

                <div class="aksi-form">
                    <button type="submit" class="btn-simpan">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>

                    <a href="index.php" class="btn-batal">
                        Batal
                    </a>
                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>