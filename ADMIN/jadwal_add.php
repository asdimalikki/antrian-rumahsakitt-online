<?php
include "koneksi.php";

// Ambil daftar dokter untuk dropdown
$query_dokter = mysqli_query($conn, "SELECT d.*, p.nama_poli AS nama_poli
FROM dokter d
LEFT JOIN poli p ON d.id_poli = p.id_poli
ORDER BY d.nama_dokter ASC");

$daftar_dokter = [];
while($row = mysqli_fetch_assoc($query_dokter)){
    $daftar_dokter[] = $row;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $id_dokter   = mysqli_real_escape_string($conn, $_POST['id_dokter']);
    $hari        = mysqli_real_escape_string($conn, $_POST['hari']);
    $tanggal     = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam_mulai   = mysqli_real_escape_string($conn, $_POST['jam_mulai']);
    $jam_selesai = mysqli_real_escape_string($conn, $_POST['jam_selesai']);

    if($id_dokter == ""){
        $pesan_error = "Silakan pilih dokter terlebih dahulu.";
    }
    else if($jam_selesai <= $jam_mulai){
        $pesan_error = "Jam selesai harus lebih besar dari jam mulai.";
    }

    if(!isset($pesan_error)){

        $insert = mysqli_query($conn,
        "INSERT INTO jadwal (id_dokter, hari, tanggal, jam_mulai, jam_selesai)
        VALUES ('$id_dokter', '$hari', '$tanggal', '$jam_mulai', '$jam_selesai')");

        if($insert){
            header("Location: jadwal.php");
            exit;
        } else {
            $pesan_error = "Gagal menyimpan data: " . mysqli_error($conn);
        }
    }
}

$daftar_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu', 'Minggu'];
?>

<html>
<head>
<title>Tambah Jadwal</title>

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
/* JUDUL HALAMAN */
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
    transition:0.2s ease;
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

.form-group input:focus + i,
.form-group .input-icon:has(select:focus) i{
    color:#486fb7;
}

.bantuan{
    font-size:11.5px;
    color:#b0b5c0;
    margin-top:7px;
}

/* CHIP HARI */

.chip-hari{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
}

.chip-hari input[type="radio"]{
    display:none;
}

.chip-hari label{
    margin:0;
    padding:10px 16px;
    border-radius:30px;
    background:#f5f6fa;
    color:#5a5f6b;
    font-size:12.5px;
    font-weight:700;
    text-transform:none;
    letter-spacing:0;
    cursor:pointer;
    border:1.5px solid transparent;
    transition:0.2s ease;
}

.chip-hari input[type="radio"]:checked + label{
    background: linear-gradient(135deg, #486fb7, #5d82cc);
    color:white;
    box-shadow:0 6px 14px rgba(72,111,183,0.3);
}

.chip-hari label:hover{
    border-color:#486fb7;
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
}

.preview-dokter{
    margin-top:18px;
    display:flex;
    align-items:center;
    gap:14px;
    position:relative;
    z-index:2;
}

.preview-avatar{
    width:56px;
    height:56px;
    border-radius:50%;
    background:rgba(255,255,255,0.18);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
    flex-shrink:0;
    border:2px solid rgba(255,255,255,0.3);
}

.preview-nama{
    font-size:17px;
    font-weight:800;
    line-height:1.3;
}

.preview-spesialis{
    font-size:12.5px;
    opacity:0.8;
    margin-top:2px;
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

.preview-kosong{
    font-size:12.5px;
    opacity:0.65;
}

a{
    text-decoration:none;
}

@media(max-width:1000px){
    .layout{
        grid-template-columns:1fr;
    }
    .kartu-preview{
        position:relative;
        top:0;
    }
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

    <div class="judul">
        <h1>Tambah Jadwal Praktik</h1>
        <p>Lengkapi detail di bawah untuk menjadwalkan praktik dokter.</p>
    </div>

    <?php if(isset($pesan_error)){ ?>
        <div class="pesan-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= $pesan_error; ?>
        </div>
    <?php } ?>

    <form method="POST" id="formJadwal">

    <div class="layout">

        <div class="kotak-form">

            <div class="seksi-judul">
                <div class="lingkaran-ikon">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>
                <div>
                    <h3>Informasi Dokter</h3>
                    <p>Pilih dokter yang akan dijadwalkan</p>
                </div>
            </div>

            <div class="form-group full">
                <label>Dokter <span class="wajib">*</span></label>
                <div class="input-icon">
                    <i class="fa-solid fa-user-doctor"></i>
                    <select name="id_dokter" id="id_dokter" required>
                        <option value="">- Pilih Dokter -</option>
                        <?php foreach($daftar_dokter as $d){ ?>
                            <option
                                value="<?= $d['id_dokter']; ?>"
                                data-spesialis="<?= htmlspecialchars($d['spesialis'] ?? '-'); ?>"
                                data-poli="<?= htmlspecialchars($d['nama_poli'] ?? '-'); ?>"
                                <?= (isset($_POST['id_dokter']) && $_POST['id_dokter'] == $d['id_dokter']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($d['nama_dokter']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="bantuan">Tidak menemukan dokter? Tambahkan dulu di menu Data Dokter.</div>
            </div>

            <div class="seksi-judul" style="margin-top:8px;">
                <div class="lingkaran-ikon">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div>
                    <h3>Jadwal Praktik</h3>
                    <p>Tentukan hari, tanggal, dan jam praktik</p>
                </div>
            </div>

            <div class="form-group full">
                <label>Hari <span class="wajib">*</span></label>
                <div class="chip-hari" id="chipHari">
                    <?php foreach($daftar_hari as $i => $h){ ?>
                        <input type="radio" name="hari" id="hari<?= $i; ?>" value="<?= $h; ?>"
                        <?= (isset($_POST['hari']) && $_POST['hari'] == $h) ? 'checked' : ''; ?> required>
                        <label for="hari<?= $i; ?>"><?= $h; ?></label>
                    <?php } ?>
                </div>
            </div>

            <div class="form-grid">

                <div class="form-group full">
                    <label>Tanggal <span class="wajib">*</span></label>
                    <div class="input-icon">
                        <i class="fa-solid fa-calendar"></i>
                        <input type="date" name="tanggal" id="tanggal" value="<?= $_POST['tanggal'] ?? ''; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Jam Mulai <span class="wajib">*</span></label>
                    <div class="input-icon">
                        <i class="fa-solid fa-clock"></i>
                        <input type="time" name="jam_mulai" id="jam_mulai" value="<?= $_POST['jam_mulai'] ?? ''; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Jam Selesai <span class="wajib">*</span></label>
                    <div class="input-icon">
                        <i class="fa-solid fa-clock"></i>
                        <input type="time" name="jam_selesai" id="jam_selesai" value="<?= $_POST['jam_selesai'] ?? ''; ?>" required>
                    </div>
                </div>

            </div>

            <div class="aksi-form">

                <button type="submit" class="btn-simpan">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Jadwal
                </button>

                <a href="jadwal.php" class="btn-batal">
                    Batal
                </a>

            </div>

        </div>

        <div class="kartu-preview">

            <div class="preview-label">Pratinjau Jadwal</div>

            <div class="preview-dokter">
                <div class="preview-avatar">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>
                <div>
                    <div class="preview-nama" id="pvNama">Belum dipilih</div>
                    <div class="preview-spesialis" id="pvSpesialis">-</div>
                </div>
            </div>

            <div class="preview-divider"></div>

            <div class="preview-baris">
                <i class="fa-solid fa-calendar-week"></i>
                <div class="teks">
                    <small>Hari</small>
                    <strong id="pvHari">-</strong>
                </div>
            </div>

            <div class="preview-baris">
                <i class="fa-solid fa-calendar"></i>
                <div class="teks">
                    <small>Tanggal</small>
                    <strong id="pvTanggal">-</strong>
                </div>
            </div>

            <div class="preview-baris">
                <i class="fa-solid fa-clock"></i>
                <div class="teks">
                    <small>Jam Praktik</small>
                    <strong id="pvJam">-</strong>
                </div>
            </div>

            <div class="preview-baris">
                <i class="fa-solid fa-hospital"></i>
                <div class="teks">
                    <small>Poli</small>
                    <strong id="pvPoli">-</strong>
                </div>
            </div>

        </div>

    </div>

    </form>

</div>

<script>
const selectDokter = document.getElementById('id_dokter');
const pvNama = document.getElementById('pvNama');
const pvSpesialis = document.getElementById('pvSpesialis');
const pvPoli = document.getElementById('pvPoli');
const pvHari = document.getElementById('pvHari');
const pvTanggal = document.getElementById('pvTanggal');
const pvJam = document.getElementById('pvJam');

function updateDokter(){
    const opt = selectDokter.options[selectDokter.selectedIndex];
    if(selectDokter.value === ""){
        pvNama.innerText = "Belum dipilih";
        pvSpesialis.innerText = "-";
        pvPoli.innerText = "-";
    } else {
        pvNama.innerText = opt.text;
        pvSpesialis.innerText = opt.getAttribute('data-spesialis') || '-';
        pvPoli.innerText = opt.getAttribute('data-poli') || '-';
    }
}

function updateHari(){
    const dicentang = document.querySelector('input[name="hari"]:checked');
    pvHari.innerText = dicentang ? dicentang.value : '-';
}

function updateTanggal(){
    const nilai = document.getElementById('tanggal').value;
    if(!nilai){
        pvTanggal.innerText = '-';
        return;
    }
    const tgl = new Date(nilai + 'T00:00:00');
    const opsi = { weekday:'long', day:'numeric', month:'long', year:'numeric' };
    pvTanggal.innerText = tgl.toLocaleDateString('id-ID', opsi);
}

function updateJam(){
    const mulai = document.getElementById('jam_mulai').value;
    const selesai = document.getElementById('jam_selesai').value;
    if(!mulai && !selesai){
        pvJam.innerText = '-';
    } else {
        pvJam.innerText = (mulai || '--:--') + '  —  ' + (selesai || '--:--');
    }
}

selectDokter.addEventListener('change', updateDokter);
document.querySelectorAll('input[name="hari"]').forEach(r => r.addEventListener('change', updateHari));
document.getElementById('tanggal').addEventListener('change', updateTanggal);
document.getElementById('jam_mulai').addEventListener('change', updateJam);
document.getElementById('jam_selesai').addEventListener('change', updateJam);

updateDokter();
updateHari();
updateTanggal();
updateJam();
</script>

</body>
</html>