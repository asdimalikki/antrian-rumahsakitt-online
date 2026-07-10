<?php
include "koneksi.php";

// Ambil id poli dari URL
$id_poli = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

if($id_poli == ""){
    header("Location: poli.php");
    exit;
}

// Ambil data poli yang akan diedit
$query_poli = mysqli_query($conn, "SELECT * FROM poli WHERE id_poli = '$id_poli'");
$poli = mysqli_fetch_assoc($query_poli);

if(!$poli){
    header("Location: poli.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $nama_poli  = mysqli_real_escape_string($conn, $_POST['nama_poli']);
    $deskripsi  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $lokasi     = mysqli_real_escape_string($conn, $_POST['lokasi']);

    if($nama_poli == ""){
        $pesan_error = "Nama poli wajib diisi.";
    }
    else if($lokasi == ""){
        $pesan_error = "Lokasi wajib diisi.";
    }

    if(!isset($pesan_error)){

        $update = mysqli_query($conn,
        "UPDATE poli SET
            nama_poli = '$nama_poli',
            deskripsi = '$deskripsi',
            lokasi = '$lokasi'
        WHERE id_poli = '$id_poli'");

        if($update){
            header("Location: poli.php");
            exit;
        } else {
            $pesan_error = "Gagal menyimpan perubahan: " . mysqli_error($conn);
        }
    }

    // supaya form tetap menampilkan input terakhir jika gagal validasi
    $poli['nama_poli'] = $_POST['nama_poli'];
    $poli['lokasi']    = $_POST['lokasi'];
    $poli['deskripsi'] = $_POST['deskripsi'];
}
?>

<html>
<head>
<title>Edit Poli</title>

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

/* JUDUL HALAMAN (di tengah, seperti gambar) */
.judul{
    text-align:center;
    margin-top:-20px;
    margin-bottom:35px;
}

.judul h1{
    font-size:30px;
    color:#1f2430;
    font-weight:800;
    letter-spacing:0.2px;
}

.judul p{
    color:#8a8f9c;
    margin-top:8px;
    font-size:14.5px;
}

/* KARTU FORM TUNGGAL DI TENGAH */

.wrapper-form{
    max-width:640px;
    margin:0 auto;
}

.kotak-form{
    background:white;
    padding:40px 44px;
    border-radius:22px;
    box-shadow:0 10px 35px rgba(31,41,55,0.06);
    border:1px solid #f0f1f5;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:0 18px;
}

.form-grid .full{
    grid-column:1 / -1;
}

.form-group{
    margin-bottom:24px;
}

.form-group label{
    display:block;
    margin-bottom:9px;
    color:#4a4f5c;
    font-size:12.5px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:0.5px;
}

.form-group label .wajib{
    color:#ff3d5a;
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
    pointer-events:none;
    transition:0.2s ease;
}

.form-group input,
.form-group textarea{
    width:100%;
    padding:14px 18px 14px 44px;
    border:1.5px solid #e6e8ee;
    border-radius:12px;
    background:#fafbfc;
    font-size:14px;
    color:#1f2430;
    transition:0.2s ease;
    appearance:none;
    -webkit-appearance:none;
}

.form-group textarea{
    resize:vertical;
    min-height:100px;
    line-height:1.5;
}

.form-group input:focus,
.form-group textarea:focus{
    outline:none;
    border-color:#486fb7;
    background-color:white;
    box-shadow:0 0 0 4px rgba(72,111,183,0.12);
}

.form-group input:focus + i,
.form-group textarea:focus + i{
    color:#486fb7;
}

/* AKSI DI TENGAH (sesuai gambar) */

.aksi-form{
    display:flex;
    justify-content:center;
    gap:14px;
    margin-top:10px;
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
    display:inline-flex;
    align-items:center;
    gap:8px;
}

.btn-simpan:hover{
    transform:translateY(-1px);
    box-shadow:0 10px 22px rgba(72,111,183,0.4);
}

.btn-batal{
    background:#f1f2f6;
    color:#5a5f6b;
    border:none;
    padding:14px 28px;
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
    margin-bottom:24px;
    font-size:14px;
    display:flex;
    align-items:center;
    gap:10px;
    max-width:640px;
    margin-left:auto;
    margin-right:auto;
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
        <li>
            <a href="index.php" style="color:white;text-decoration:none;display:flex;gap:12px;align-items:center;">
                <i class="fa-solid fa-users"></i>
                Data Users
            </a>
        </li>

        <li>
            <a href="pasien.php" style="color:white;text-decoration:none;display:flex;gap:12px;align-items:center;">
                <i class="fa-solid fa-user"></i>
                Data Pasien
            </a>
        </li>

        <li>
            <a href="antrian.php" style="color:white;text-decoration:none;display:flex;gap:12px;align-items:center;">
                <i class="fa-solid fa-user-doctor"></i>
               Data Antrian
            </a>
        </li>

        <li>
            <a href="dokter.php" style="color:white;text-decoration:none;display:flex;gap:12px;align-items:center;">
                <i class="fa-solid fa-user-doctor"></i>
               Data Dokter
            </a>
        </li>

        <li>
            <a href="jadwal.php" style="color:white;text-decoration:none;display:flex;gap:12px;align-items:center;">
                <i class="fa-solid fa-calendar-days"></i>
                Data Jadwal
            </a>
        </li>

        <li class="aktif">
            <a href="poli.php" style="color:white;text-decoration:none;display:flex;gap:12px;align-items:center;">
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

    <div class="judul">
        <h1>Edit Data Poli</h1>
        <p>Ubah informasi poli di bawah ini lalu simpan perubahan.</p>
    </div>

    <?php if(isset($pesan_error)){ ?>
        <div class="pesan-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= $pesan_error; ?>
        </div>
    <?php } ?>

    <div class="wrapper-form">

        <form method="POST" id="formEditPoli">

        <div class="kotak-form">

            <div class="form-grid">

                <div class="form-group full">
                    <label>Nama Poli <span class="wajib">*</span></label>
                    <div class="input-icon">
                        <i class="fa-solid fa-hospital"></i>
                        <input type="text" name="nama_poli" id="nama_poli"
                        value="<?= htmlspecialchars($poli['nama_poli']); ?>" required>
                    </div>
                </div>

                <div class="form-group full">
                    <label>Lokasi <span class="wajib">*</span></label>
                    <div class="input-icon">
                        <i class="fa-solid fa-location-dot"></i>
                        <input type="text" name="lokasi" id="lokasi"
                        value="<?= htmlspecialchars($poli['lokasi']); ?>" required>
                    </div>
                </div>

                <div class="form-group full">
                    <label>Deskripsi</label>
                    <div class="input-icon">
                        <i class="fa-solid fa-pen" style="top:22px; transform:none;"></i>
                        <textarea name="deskripsi" id="deskripsi"><?= htmlspecialchars($poli['deskripsi']); ?></textarea>
                    </div>
                </div>

            </div>

            <div class="aksi-form">
                <button type="submit" class="btn-simpan">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>

                <a href="poli.php" class="btn-batal">
                    Batal
                </a>
            </div>

        </div>

        </form>

    </div>

</div>

</body>
</html>