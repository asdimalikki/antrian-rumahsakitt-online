<?php
include "koneksi.php";

// Ambil daftar poli untuk dropdown
$query_poli = mysqli_query($conn, "SELECT * FROM poli ORDER BY nama_poli ASC");

$daftar_poli = [];
while($row = mysqli_fetch_assoc($query_poli)){
    $daftar_poli[] = $row;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $nama_dokter = mysqli_real_escape_string($conn, $_POST['nama_dokter']);
    $spesialis   = mysqli_real_escape_string($conn, $_POST['spesialis']);
    $id_poli     = mysqli_real_escape_string($conn, $_POST['id_poli']);
    $no_hp       = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $foto        = "";

    if($nama_dokter == ""){
        $pesan_error = "Nama dokter wajib diisi.";
    }
    else if($id_poli == ""){
        $pesan_error = "Silakan pilih poli terlebih dahulu.";
    }

    // Cek apakah ada file foto yang diupload
    if(!isset($pesan_error) && isset($_FILES['foto']) && $_FILES['foto']['error'] == 0){

        $folder_upload = "uploads/dokter/";

        if(!is_dir($folder_upload)){
            mkdir($folder_upload, 0777, true);
        }

        $ekstensi_diizinkan = ['jpg', 'jpeg', 'png'];
        $nama_file = $_FILES['foto']['name'];
        $ekstensi  = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        if(in_array($ekstensi, $ekstensi_diizinkan)){

            $nama_file_baru = "dokter_" . time() . "_" . rand(100,999) . "." . $ekstensi;
            $tujuan_upload  = $folder_upload . $nama_file_baru;

            if(move_uploaded_file($_FILES['foto']['tmp_name'], $tujuan_upload)){
                $foto = $nama_file_baru;
            } else {
                $pesan_error = "Gagal mengupload foto. Coba lagi.";
            }

        } else {
            $pesan_error = "Format foto tidak didukung. Gunakan JPG, JPEG, atau PNG.";
        }
    }

    if(!isset($pesan_error)){

        $insert = mysqli_query($conn,
        "INSERT INTO dokter (nama_dokter, spesialis, id_poli, no_hp, foto)
        VALUES ('$nama_dokter', '$spesialis', '$id_poli', '$no_hp', '$foto')");

        if($insert){
            header("Location: dokter.php");
            exit;
        } else {
            $pesan_error = "Gagal menyimpan data: " . mysqli_error($conn);
        }
    }
}
?>

<html>
<head>
<title>Tambah Dokter</title>

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

.judul{
    margin-top:10px;
    margin-bottom:30px;
}

.judul h1{
    font-size:30px;
    color:#1f2430;
    font-weight:800;
    letter-spacing:0.2px;
}

.judul p{
    color:#8a8f9c;
    margin-top:6px;
    font-size:14.5px;
}

/* LAYOUT 2 KOLOM */

.layout{
    display:grid;
    grid-template-columns:1.5fr 1fr;
    gap:28px;
    align-items:start;
}

/* KOTAK FORM */

.kotak-form{
    background:white;
    padding:38px 42px;
    border-radius:22px;
    box-shadow:0 10px 35px rgba(31,41,55,0.06);
    border:1px solid #f0f1f5;
}

.seksi-judul{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:26px;
    padding-bottom:18px;
    border-bottom:1.5px dashed #ebedf3;
}

.seksi-judul .lingkaran-ikon{
    width:40px;
    height:40px;
    border-radius:12px;
    background:linear-gradient(135deg, #486fb7, #5d82cc);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:16px;
    flex-shrink:0;
}

.seksi-judul h3{
    font-size:16px;
    color:#1f2430;
    font-weight:800;
}

.seksi-judul p{
    font-size:12.5px;
    color:#9aa0ab;
    margin-top:2px;
}

/* UPLOAD FOTO */

.foto-area{
    display:flex;
    align-items:center;
    gap:20px;
    margin-bottom:28px;
}

.foto-preview{
    width:88px;
    height:88px;
    border-radius:50%;
    object-fit:cover;
    background:#e5e8f5;
    color:#486fb7;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:32px;
    border:3px solid #f0f1f5;
    flex-shrink:0;
}

img.foto-preview{
    font-size:0;
}

.label-upload{
    background:#f1f2f6;
    color:#4a4f5c;
    padding:10px 20px;
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
    gap:0 18px;
}

.form-grid .full{
    grid-column:1 / -1;
}

.form-group{
    margin-bottom:22px;
}

.form-group label{
    display:flex;
    align-items:center;
    gap:6px;
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
}

.form-group input,
.form-group select{
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

.form-group select{
    background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23a7acb8' stroke-width='2'><polyline points='6 9 12 15 18 9'/></svg>");
    background-repeat:no-repeat;
    background-position:right 16px center;
    cursor:pointer;
}

.form-group input:focus,
.form-group select:focus{
    outline:none;
    border-color:#486fb7;
    background-color:white;
    box-shadow:0 0 0 4px rgba(72,111,183,0.12);
}

.bantuan{
    font-size:11.5px;
    color:#b0b5c0;
    margin-top:7px;
}

.aksi-form{
    display:flex;
    justify-content:center;
    gap:12px;
    margin-top:8px;
    padding-top:22px;
    border-top:1.5px dashed #ebedf3;
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
}

/* KARTU PREVIEW */

.kartu-preview{
    background: linear-gradient(155deg, #486fb7 0%, #34508a 100%);
    border-radius:22px;
    padding:34px 30px;
    color:white;
    position:relative;
    overflow:hidden;
    box-shadow:0 15px 35px rgba(72,111,183,0.28);
    position:sticky;
    top:30px;
    text-align:center;
}

.kartu-preview::before{
    content:"";
    width:220px;
    height:220px;
    background:rgba(255,255,255,0.07);
    border-radius:50%;
    position:absolute;
    top:-100px;
    right:-80px;
}

.kartu-preview::after{
    content:"";
    width:160px;
    height:160px;
    background:rgba(255,255,255,0.06);
    border-radius:50%;
    position:absolute;
    bottom:-80px;
    left:-60px;
}

.preview-label{
    font-size:11.5px;
    text-transform:uppercase;
    letter-spacing:1px;
    opacity:0.75;
    font-weight:700;
    position:relative;
    z-index:2;
    text-align:left;
}

.preview-avatar-wrap{
    margin-top:22px;
    position:relative;
    z-index:2;
}

.preview-avatar{
    width:96px;
    height:96px;
    border-radius:50%;
    object-fit:cover;
    background:rgba(255,255,255,0.18);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:36px;
    margin:0 auto;
    border:3px solid rgba(255,255,255,0.3);
}

img.preview-avatar{
    font-size:0;
}

.preview-nama{
    margin-top:16px;
    font-size:18px;
    font-weight:800;
    position:relative;
    z-index:2;
}

.preview-spesialis-badge{
    display:inline-block;
    margin-top:8px;
    padding:5px 14px;
    background:rgba(255,255,255,0.18);
    border-radius:20px;
    font-size:11.5px;
    font-weight:700;
    position:relative;
    z-index:2;
}

.preview-divider{
    height:1px;
    background:rgba(255,255,255,0.18);
    margin:24px 0;
    position:relative;
    z-index:2;
}

.preview-baris{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:18px;
    position:relative;
    z-index:2;
    text-align:left;
}

.preview-baris:last-child{
    margin-bottom:0;
}

.preview-baris i{
    width:34px;
    height:34px;
    background:rgba(255,255,255,0.15);
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:14px;
    flex-shrink:0;
}

.preview-baris .teks small{
    display:block;
    font-size:11px;
    opacity:0.7;
    text-transform:uppercase;
    letter-spacing:0.5px;
    margin-bottom:2px;
}

.preview-baris .teks strong{
    font-size:14px;
    font-weight:700;
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

    <div class="judul">
        <h1>Tambah Data Dokter</h1>
        <p>Lengkapi detail di bawah untuk mendaftarkan dokter baru.</p>
    </div>

    <?php if(isset($pesan_error)){ ?>
        <div class="pesan-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= $pesan_error; ?>
        </div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data" id="formDokter">

    <div class="layout">

        <div class="kotak-form">

            <div class="seksi-judul">
                <div class="lingkaran-ikon">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div>
                    <h3>Foto & Identitas</h3>
                    <p>Foto profil dan data utama dokter</p>
                </div>
            </div>

            <div class="foto-area">
                <div class="foto-preview" id="previewFoto">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>

                <div>
                    <label class="label-upload">
                        <i class="fa-solid fa-camera"></i>
                        Unggah Foto
                        <input type="file" name="foto" id="inputFoto" accept=".jpg,.jpeg,.png" onchange="tampilkanPreviewFoto(event)">
                    </label>
                    <div class="nama-file" id="namaFile">Belum ada foto dipilih</div>
                </div>
            </div>

            <div class="form-group full">
                <label>Nama Dokter <span class="wajib">*</span></label>
                <div class="input-icon">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="nama_dokter" id="nama_dokter" value="<?= $_POST['nama_dokter'] ?? ''; ?>" required>
                </div>
            </div>

            <div class="seksi-judul" style="margin-top:8px;">
                <div class="lingkaran-ikon">
                    <i class="fa-solid fa-stethoscope"></i>
                </div>
                <div>
                    <h3>Spesialisasi & Penugasan</h3>
                    <p>Tentukan bidang keahlian dan poli tugas</p>
                </div>
            </div>

            <div class="form-grid">

                <div class="form-group">
                    <label>Spesialis</label>
                    <div class="input-icon">
                        <i class="fa-solid fa-stethoscope"></i>
                        <input type="text" name="spesialis" id="spesialis" value="<?= $_POST['spesialis'] ?? ''; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Poli <span class="wajib">*</span></label>
                    <div class="input-icon">
                        <i class="fa-solid fa-hospital"></i>
                        <select name="id_poli" id="id_poli" required>
                            <option value="">- Pilih Poli -</option>
                            <?php foreach($daftar_poli as $p){ ?>
                                <option value="<?= $p['id_poli']; ?>" <?= (isset($_POST['id_poli']) && $_POST['id_poli'] == $p['id_poli']) ? 'selected' : ''; ?>>
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
                        <input type="text" name="no_hp" id="no_hp" value="<?= $_POST['no_hp'] ?? ''; ?>">
                    </div>
                    <div class="bantuan">Nomor ini akan ditampilkan di kartu profil dokter.</div>
                </div>

            </div>

            <div class="aksi-form">

                <button type="submit" class="btn-simpan">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Dokter
                </button>

                <a href="dokter.php" class="btn-batal">
                    Batal
                </a>
            </div>

        </div>

        <div class="kartu-preview">

            <div class="preview-label">Pratinjau Profil</div>

            <div class="preview-avatar-wrap">
                <div class="preview-avatar" id="pvAvatar">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>
            </div>

            <div class="preview-nama" id="pvNama">Nama Dokter</div>
            <div class="preview-spesialis-badge" id="pvSpesialis">Spesialis</div>

            <div class="preview-divider"></div>

            <div class="preview-baris">
                <i class="fa-solid fa-hospital"></i>
                <div class="teks">
                    <small>Poli</small>
                    <strong id="pvPoli">-</strong>
                </div>
            </div>

            <div class="preview-baris">
                <i class="fa-solid fa-phone"></i>
                <div class="teks">
                    <small>No. HP</small>
                    <strong id="pvHp">-</strong>
                </div>
            </div>

        </div>

    </div>

    </form>

</div>

<script>
const inputNama = document.getElementById('nama_dokter');
const inputSpesialis = document.getElementById('spesialis');
const selectPoli = document.getElementById('id_poli');
const inputHp = document.getElementById('no_hp');

const pvNama = document.getElementById('pvNama');
const pvSpesialis = document.getElementById('pvSpesialis');
const pvPoli = document.getElementById('pvPoli');
const pvHp = document.getElementById('pvHp');

function updateNama(){
    pvNama.innerText = inputNama.value.trim() !== '' ? inputNama.value : 'Nama Dokter';
}

function updateSpesialis(){
    pvSpesialis.innerText = inputSpesialis.value.trim() !== '' ? inputSpesialis.value : 'Spesialis';
}

function updatePoli(){
    const opt = selectPoli.options[selectPoli.selectedIndex];
    pvPoli.innerText = selectPoli.value !== '' ? opt.text : '-';
}

function updateHp(){
    pvHp.innerText = inputHp.value.trim() !== '' ? inputHp.value : '-';
}

function tampilkanPreviewFoto(event){
    const file = event.target.files[0];
    if(!file) return;

    document.getElementById('namaFile').innerText = file.name;

    const reader = new FileReader();
    reader.onload = function(e){
        // Foto kecil di atas form
        const previewLama = document.getElementById('previewFoto');
        const previewBaru = document.createElement('img');
        previewBaru.className = 'foto-preview';
        previewBaru.id = 'previewFoto';
        previewBaru.src = e.target.result;
        previewLama.replaceWith(previewBaru);

        // Foto besar di kartu pratinjau
        const avatarLama = document.getElementById('pvAvatar');
        const avatarBaru = document.createElement('img');
        avatarBaru.className = 'preview-avatar';
        avatarBaru.id = 'pvAvatar';
        avatarBaru.src = e.target.result;
        avatarLama.replaceWith(avatarBaru);
    }
    reader.readAsDataURL(file);
}

inputNama.addEventListener('input', updateNama);
inputSpesialis.addEventListener('input', updateSpesialis);
selectPoli.addEventListener('change', updatePoli);
inputHp.addEventListener('input', updateHp);

updateNama();
updateSpesialis();
updatePoli();
updateHp();
</script>

</body>
</html>