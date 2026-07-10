<?php
include "koneksi.php";

if(!isset($_GET['id']) || $_GET['id'] == ""){
    header("Location: jadwal.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil daftar dokter untuk dropdown
$query_dokter = mysqli_query($conn, "SELECT * FROM dokter ORDER BY nama_dokter ASC");

// Proses update saat form disubmit
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $id_dokter   = mysqli_real_escape_string($conn, $_POST['id_dokter']);
    $hari        = mysqli_real_escape_string($conn, $_POST['hari']);
    $tanggal     = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam_mulai   = mysqli_real_escape_string($conn, $_POST['jam_mulai']);
    $jam_selesai = mysqli_real_escape_string($conn, $_POST['jam_selesai']);

    if($jam_selesai <= $jam_mulai){
        $pesan_error = "Jam selesai harus lebih besar dari jam mulai.";
    }

    if(!isset($pesan_error)){

        $update = mysqli_query($conn,
        "UPDATE jadwal SET
            id_dokter = '$id_dokter',
            hari = '$hari',
            tanggal = '$tanggal',
            jam_mulai = '$jam_mulai',
            jam_selesai = '$jam_selesai'
        WHERE id_jadwal = '$id'");

        if($update){
            header("Location: jadwal.php");
            exit;
        } else {
            $pesan_error = "Gagal mengupdate data: " . mysqli_error($conn);
        }
    }
}

// Ambil data lama untuk ditampilkan di form
$query = mysqli_query($conn,
"SELECT * FROM jadwal WHERE id_jadwal = '$id'");

if(mysqli_num_rows($query) == 0){
    header("Location: jadwal.php");
    exit;
}

$data = mysqli_fetch_assoc($query);

$daftar_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu', 'Minggu'];
?>

<html>
<head>
<title>Edit Jadwal</title>

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
/* AREA TENGAH */

.wrapper-form{
    display:flex;
    flex-direction:column;
    align-items:center;
    margin-top:20px;
}

/* JUDUL HALAMAN */

.judul{
    margin-top:-25px;
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
    font-size:14px;
}

/* KOTAK FORM */

.kotak-form{
    background:white;
    padding:40px 45px;
    border-radius:24px;
    margin-top:25px;
    box-shadow:0 10px 35px rgba(0,0,0,0.07);
    width:100%;
    max-width:640px;
    border:1px solid #f0f1f5;
}

/* IKON JADWAL DI ATAS FORM */

.icon-area{
    display:flex;
    flex-direction:column;
    align-items:center;
    margin-bottom:28px;
}

.icon-preview{
    width:90px;
    height:90px;
    border-radius:50%;
    background:#e5e8f5;
    color:#486fb7;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:34px;
    border:3px solid #f0f1f5;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:0 20px;
}

.form-grid .full{
    grid-column:1 / -1;
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
    pointer-events:none;
}

.form-group input,
.form-group select{
    width:100%;
    padding:14px 18px 14px 44px;
    border:1.5px solid #e6e8ee;
    border-radius:12px;
    background:#fafbfc;
    font-size:14px;
    color:#2b2f3a;
    transition:0.2s ease;
    appearance:none;
    -webkit-appearance:none;
}

.form-group select{
    background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23a7acb8' stroke-width='2'><polyline points='6 9 12 15 18 9'/></svg>");
    background-repeat:no-repeat;
    background-position:right 16px center;
}

.form-group input:focus,
.form-group select:focus{
    outline:none;
    border-color:#486fb7;
    background-color:white;
    box-shadow:0 0 0 4px rgba(72,111,183,0.12);
}

.aksi-form{
    display:flex;
    justify-content:center;
    gap:12px;
    margin-top:12px;
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

        <li class="aktif">
            <a href="jadwal.php" style="color:white;text-decoration:none;display:flex;gap:12px;align-items:center;">
                <i class="fa-solid fa-calendar-days"></i>
                Data Jadwal
            </a>
        </li>

        <li>
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

    <div class="wrapper-form">

        <div class="judul">
            <h1>Edit Data Jadwal</h1>
            <p>Ubah informasi jadwal di bawah ini lalu simpan perubahan.</p>
        </div>

        <div class="kotak-form">

            <?php if(isset($pesan_error)){ ?>
                <div class="pesan-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= $pesan_error; ?>
                </div>
            <?php } ?>

            <form method="POST">

                <div class="form-grid">

                    <div class="form-group full">
                        <label>Dokter</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-user-doctor"></i>
                            <select name="id_dokter" required>
                                <option value="">- Pilih Dokter -</option>
                                <?php while($d = mysqli_fetch_assoc($query_dokter)){ ?>
                                    <option value="<?= $d['id_dokter']; ?>" <?= ($data['id_dokter'] == $d['id_dokter']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($d['nama_dokter']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Hari</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-calendar-week"></i>
                            <select name="hari" required>
                                <option value="">- Pilih Hari -</option>
                                <?php foreach($daftar_hari as $h){ ?>
                                    <option value="<?= $h; ?>" <?= ($data['hari'] == $h) ? 'selected' : ''; ?>>
                                        <?= $h; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tanggal</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-calendar"></i>
                            <input type="date" name="tanggal" value="<?= $data['tanggal']; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Jam Mulai</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-clock"></i>
                            <input type="time" name="jam_mulai" value="<?= substr($data['jam_mulai'], 0, 5); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Jam Selesai</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-clock"></i>
                            <input type="time" name="jam_selesai" value="<?= substr($data['jam_selesai'], 0, 5); ?>" required>
                        </div>
                    </div>

                </div>

                <div class="aksi-form">
                    <button type="submit" class="btn-simpan">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>

                    <a href="jadwal.php" class="btn-batal">
                        Batal
                    </a>
                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>