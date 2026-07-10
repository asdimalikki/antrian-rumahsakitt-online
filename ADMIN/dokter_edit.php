<?php
include "koneksi.php";

if(!isset($_GET['id']) || $_GET['id'] == ""){
    header("Location: dokter.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil daftar poli untuk dropdown
$query_poli = mysqli_query($conn, "SELECT * FROM poli ORDER BY nama_poli ASC");

// Proses update saat form disubmit
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $nama_dokter = mysqli_real_escape_string($conn, $_POST['nama_dokter']);
    $spesialis   = mysqli_real_escape_string($conn, $_POST['spesialis']);
    $id_poli     = mysqli_real_escape_string($conn, $_POST['id_poli']);
    $no_hp       = mysqli_real_escape_string($conn, $_POST['no_hp']);

    // Ambil nama foto lama (sebagai default kalau tidak upload foto baru)
    $query_lama = mysqli_query($conn, "SELECT foto FROM dokter WHERE id_dokter = '$id'");
    $data_lama  = mysqli_fetch_assoc($query_lama);
    $foto       = $data_lama['foto'];

    // Cek apakah ada file foto baru yang diupload
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0){

        $folder_upload = "uploads/dokter/";

        // Buat folder kalau belum ada
        if(!is_dir($folder_upload)){
            mkdir($folder_upload, 0777, true);
        }

        $ekstensi_diizinkan = ['jpg', 'jpeg', 'png'];
        $nama_file   = $_FILES['foto']['name'];
        $ekstensi    = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        if(in_array($ekstensi, $ekstensi_diizinkan)){

            // Buat nama file unik supaya tidak bentrok
            $nama_file_baru = "dokter_" . $id . "_" . time() . "." . $ekstensi;
            $tujuan_upload  = $folder_upload . $nama_file_baru;

            if(move_uploaded_file($_FILES['foto']['tmp_name'], $tujuan_upload)){

                // Hapus foto lama dari folder (kalau ada) supaya tidak menumpuk
                if(!empty($foto) && file_exists($folder_upload . $foto)){
                    unlink($folder_upload . $foto);
                }

                $foto = $nama_file_baru;

            } else {
                $pesan_error = "Gagal mengupload foto. Coba lagi.";
            }

        } else {
            $pesan_error = "Format foto tidak didukung. Gunakan JPG, JPEG, atau PNG.";
        }
    }

    if(!isset($pesan_error)){

        $update = mysqli_query($conn,
        "UPDATE dokter SET
            nama_dokter = '$nama_dokter',
            spesialis = '$spesialis',
            id_poli = '$id_poli',
            no_hp = '$no_hp',
            foto = '$foto'
        WHERE id_dokter = '$id'");

        if($update){
            header("Location: dokter.php");
            exit;
        } else {
            $pesan_error = "Gagal mengupdate data: " . mysqli_error($conn);
        }
    }
}

// Ambil data lama untuk ditampilkan di form
$query = mysqli_query($conn,
"SELECT * FROM dokter WHERE id_dokter = '$id'");

if(mysqli_num_rows($query) == 0){
    header("Location: dokter.php");
    exit;
}

$data = mysqli_fetch_assoc($query);
?>

<html>
<head>
<title>Edit Dokter</title>

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

/* FOTO PROFIL DOKTER */

.foto-area{
    display:flex;
    flex-direction:column;
    align-items:center;
    margin-bottom:28px;
}

.foto-preview{
    width:110px;
    height:110px;
    border-radius:50%;
    object-fit:cover;
    background:#e5e8f5;
    color:#486fb7;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:42px;
    border:3px solid #f0f1f5;
}

img.foto-preview{
    font-size:0;
}

.label-upload{
    margin-top:14px;
    background:#f1f2f6;
    color:#4a4f5c;
    padding:9px 20px;
    border-radius:30px;
    font-size:13px;
    font-weight:700;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    gap:8px;
    transition:0.2s ease;
}

.label-upload:hover{
    background:#e6e8ee;
}

.label-upload input[type="file"]{
    display:none;
}

.nama-file{
    margin-top:8px;
    font-size:12px;
    color:#8a8f9c;
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

        <li class="aktif">
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
            <h1>Edit Data Dokter</h1>
            <p>Ubah informasi dokter di bawah ini lalu simpan perubahan.</p>
        </div>

        <div class="kotak-form">

            <?php if(isset($pesan_error)){ ?>
                <div class="pesan-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= $pesan_error; ?>
                </div>
            <?php } ?>

            <form method="POST" enctype="multipart/form-data">

                <div class="foto-area">

                    <?php if(!empty($data['foto'])){ ?>
                        <img class="foto-preview" id="previewFoto" src="uploads/dokter/<?= $data['foto']; ?>" alt="foto dokter">
                    <?php } else { ?>
                        <div class="foto-preview" id="previewFoto">
                            <i class="fa-solid fa-user-doctor"></i>
                        </div>
                    <?php } ?>

                    <label class="label-upload">
                        <i class="fa-solid fa-camera"></i>
                        Ganti Foto
                        <input type="file" name="foto" id="inputFoto" accept=".jpg,.jpeg,.png" onchange="tampilkanPreview(event)">
                    </label>

                    <div class="nama-file" id="namaFile">
                        <?= !empty($data['foto']) ? 'Foto saat ini: ' . htmlspecialchars($data['foto']) : 'Belum ada foto'; ?>
                    </div>

                </div>

                <div class="form-grid">

                    <div class="form-group full">
                        <label>Nama Dokter</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="nama_dokter" value="<?= htmlspecialchars($data['nama_dokter']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Spesialis</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-stethoscope"></i>
                            <input type="text" name="spesialis" value="<?= htmlspecialchars($data['spesialis'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Poli</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-hospital"></i>
                            <select name="id_poli">
                                <option value="">- Pilih Poli -</option>
                                <?php while($p = mysqli_fetch_assoc($query_poli)){ ?>
                                    <option value="<?= $p['id_poli']; ?>" <?= ($data['id_poli'] == $p['id_poli']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($p['nama_poli']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group full">
                        <label>No. HP</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-phone"></i>
                            <input type="text" name="no_hp" value="<?= htmlspecialchars($data['no_hp'] ?? ''); ?>">
                        </div>
                    </div>

                </div>

                <div class="aksi-form">
                    <button type="submit" class="btn-simpan">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>

                    <a href="dokter.php" class="btn-batal">
                        Batal
                    </a>
                </div>

            </form>

        </div>

    </div>

</div>

<script>
function tampilkanPreview(event){
    const file = event.target.files[0];
    if(!file) return;

    const reader = new FileReader();
    reader.onload = function(e){
        const previewLama = document.getElementById('previewFoto');
        const previewBaru = document.createElement('img');
        previewBaru.className = 'foto-preview';
        previewBaru.id = 'previewFoto';
        previewBaru.src = e.target.result;
        previewLama.replaceWith(previewBaru);

        document.getElementById('namaFile').innerText = 'Foto baru: ' + file.name;
    }
    reader.readAsDataURL(file);
}
</script>

</body>
</html>