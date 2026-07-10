<?php
include "koneksi.php";

if(!isset($_GET['id']) || $_GET['id'] == ""){
    header("Location: pasien.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nik            = mysqli_real_escape_string($conn, $_POST['nik']);
    $nama_lengkap   = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $jenis_kelamin  = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $tanggal_lahir  = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
    $no_hp          = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $email          = mysqli_real_escape_string($conn, $_POST['email']);
    $id_user        = mysqli_real_escape_string($conn, $_POST['id_user']);
    $jenis_pasien   = mysqli_real_escape_string($conn, $_POST['jenis_pasien']);
    $no_bpjs        = ($jenis_pasien == 'bpjs') ? mysqli_real_escape_string($conn, $_POST['no_bpjs']) : '';

    $update = mysqli_query($conn,
    "UPDATE pasien SET
        nik = '$nik',
        nama_lengkap = '$nama_lengkap',
        jenis_kelamin = '$jenis_kelamin',
        tanggal_lahir = '$tanggal_lahir',
        no_hp = '$no_hp',
        email = '$email',
        jenis_pasien = '$jenis_pasien',
        no_bpjs = '$no_bpjs'
    WHERE id_pasien = '$id'");

    if($update){
        $update_users = mysqli_query($conn,
        "UPDATE users SET
            nama = '$nama_lengkap',
            email = '$email'
        WHERE id_user = '$id_user'");

        header("Location: pasien.php");
        exit;
    } else {
        $pesan_error = "Gagal mengupdate data: " . mysqli_error($conn);
    }
}

// Ambil data lama untuk ditampilkan di form
$query = mysqli_query($conn,
"SELECT * FROM pasien WHERE id_pasien = '$id'");

if(mysqli_num_rows($query) == 0){
    header("Location: pasien.php");
    exit;
}

$data = mysqli_fetch_assoc($query);
$jenis_pasien_lama = $data['jenis_pasien'] ?? 'umum';
$no_bpjs_lama      = $data['no_bpjs'] ?? '';
?>

<html>
<head>
<title>Edit Pasien</title>

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

.wrapper-form{
    display:flex;
    flex-direction:column;
    align-items:center;
    margin-top:20px;
}

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

.form-group input:disabled{
    color:#9aa0ab;
    cursor:not-allowed;
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
            <a href="index.php">
                <i class="fa-solid fa-users"></i>
                Data Users
            </a>
        </li>

        <li class="aktif">
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
            <h1>Edit Data Pasien</h1>
            <p>Ubah informasi pasien di bawah ini lalu simpan perubahan.</p>
        </div>

        <div class="kotak-form">

            <?php if(isset($pesan_error)){ ?>
                <div class="pesan-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= $pesan_error; ?>
                </div>
            <?php } ?>

            <form method="POST">

                <input type="hidden" name="id_user" value="<?= $data['id_user']; ?>">

                <div class="form-grid">

                    <div class="form-group full">
                        <label>ID Pasien</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-hashtag"></i>
                            <input type="text" value="<?= $data['id_pasien']; ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group full">
                        <label>Nama Lengkap</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($data['nama_lengkap']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group full">
                        <label>NIK</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-id-card"></i>
                            <input type="text" name="nik" value="<?= htmlspecialchars($data['nik'] ?? ''); ?>" maxlength="16">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-venus-mars"></i>
                            <select name="jenis_kelamin">
                                <option value="Laki-laki" <?= ($data['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="Perempuan" <?= ($data['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-cake-candles"></i>
                            <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($data['tanggal_lahir'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>No. HP</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-phone"></i>
                            <input type="text" name="no_hp" value="<?= htmlspecialchars($data['no_hp'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" name="email" value="<?= htmlspecialchars($data['email']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Kategori Pasien</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-id-card-clip"></i>
                            <select name="jenis_pasien" id="jenis_pasien" onchange="toggleBpjs()">
                                <option value="umum" <?= ($jenis_pasien_lama == 'umum') ? 'selected' : ''; ?>>Umum</option>
                                <option value="bpjs" <?= ($jenis_pasien_lama == 'bpjs') ? 'selected' : ''; ?>>BPJS</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" id="wrap_no_bpjs">
                        <label>No. BPJS</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-notes-medical"></i>
                            <input type="text" name="no_bpjs" id="no_bpjs" value="<?= htmlspecialchars($no_bpjs_lama); ?>" placeholder="Nomor BPJS">
                        </div>
                    </div>

                </div>

                <div class="aksi-form">
                    <button type="submit" class="btn-simpan">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>

                    <a href="pasien.php" class="btn-batal">
                        Batal
                    </a>
                </div>

            </form>

        </div>

    </div>

</div>

<script>
function toggleBpjs(){
    var jenis = document.getElementById('jenis_pasien').value;
    var wrap  = document.getElementById('wrap_no_bpjs');
    var input = document.getElementById('no_bpjs');

    if(jenis === 'bpjs'){
        wrap.style.display = 'block';
        input.disabled = false;
    } else {
        wrap.style.display = 'none';
        input.disabled = true;
        input.value = '';
    }
}

// jalankan sekali saat halaman dimuat, sesuai data yang tersimpan
toggleBpjs();
</script>

</body>
</html>