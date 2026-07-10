<?php
include "koneksi.php";

/* Ambil ID dari URL */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id < 1){
    header("Location: antrian.php");
    exit;
}

/* ===== PROSES SIMPAN PERUBAHAN ===== */
if(isset($_POST['simpan'])){

    $id_pasien   = (int)$_POST['id_pasien'];
    $id_poli     = (int)$_POST['id_poli'];
    $id_dokter   = (int)$_POST['id_dokter'];
    $kode_antrian = mysqli_real_escape_string($conn, $_POST['kode_antrian']);
    $tanggal_kunjungan = mysqli_real_escape_string($conn, $_POST['tanggal_kunjungan']);
    $nomor_antrian = (int)$_POST['nomor_antrian'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $update = mysqli_query($conn,
    "UPDATE antrian SET
        id_pasien = '$id_pasien',
        id_poli = '$id_poli',
        id_dokter = '$id_dokter',
        kode_antrian = '$kode_antrian',
        tanggal_kunjungan = '$tanggal_kunjungan',
        nomor_antrian = '$nomor_antrian',
        status = '$status'
    WHERE id_antrian = '$id'");

    if($update){

    // Jika antrian selesai, otomatis panggil nomor berikutnya
if($status=="Selesai"){

    // Cari nomor menunggu paling kecil
    $cari = mysqli_query($conn,"
        SELECT id_antrian
        FROM antrian
        WHERE id_poli='$id_poli'
        AND tanggal_kunjungan='$tanggal_kunjungan'
        AND status='Menunggu'
        ORDER BY nomor_antrian ASC
        LIMIT 1
    ");

    if(mysqli_num_rows($cari)>0){

        $next = mysqli_fetch_assoc($cari);

        mysqli_query($conn,"
            UPDATE antrian
            SET status='Sedang Dilayani'
            WHERE id_antrian='".$next['id_antrian']."'
        ");

    }

}

    header("Location: antrian.php?sukses=edit");
    exit;
}
}

/* ===== AMBIL DATA ANTRIAN BERDASARKAN ID ===== */
$query = mysqli_query($conn, "SELECT * FROM antrian WHERE id_antrian = '$id'");

if(mysqli_num_rows($query) < 1){
    header("Location: antrian.php");
    exit;
}

$data = mysqli_fetch_assoc($query);

/* ===== AMBIL DATA UNTUK DROPDOWN ===== */
$list_pasien = mysqli_query($conn, "SELECT id_pasien, nama_lengkap FROM pasien ORDER BY nama_lengkap ASC");
$list_poli   = mysqli_query($conn, "SELECT id_poli, nama_poli FROM poli ORDER BY nama_poli ASC");
$list_dokter = mysqli_query($conn, "SELECT id_dokter, nama_dokter FROM dokter ORDER BY nama_dokter ASC");
?>

<html>
<head>
<title>Edit Antrian</title>

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

/* KARTU FORM */

.kartu-form{
    background:white;
    max-width:760px;
    margin:35px auto 0 auto;
    padding:45px;
    border-radius:24px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.grup{
    margin-bottom:24px;
}

.grup label{
    display:block;
    font-size:13px;
    font-weight:bold;
    color:#444;
    margin-bottom:10px;
    letter-spacing:0.3px;
}

.kotak-input{
    display:flex;
    align-items:center;
    background:#f4f5f9;
    border-radius:14px;
    padding:0 18px;
    border:1px solid transparent;
    transition:0.2s;
}

.kotak-input:focus-within{
    border:1px solid #486fb7;
    background:white;
}

.kotak-input i{
    color:#999;
    font-size:15px;
    margin-right:12px;
}

.kotak-input input,
.kotak-input select{
    flex:1;
    border:none;
    background:transparent;
    padding:15px 0;
    font-size:14px;
    outline:none;
    color:#333;
    appearance:none;
}

.kotak-input input:disabled{
    color:#aaa;
}

.baris-dua{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

/* TOMBOL */

.aksi-form{
    display:flex;
    justify-content:center;
    gap:12px;
    margin-top:35px;
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
    background:#e9eaf0;
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

        <li class="aktif">
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
        <h1>Edit Data Antrian</h1>
        <p>Ubah informasi antrian di bawah ini lalu simpan perubahan.</p>
    </div>

    <div class="kartu-form">

        <form method="POST">

            <div class="grup">
                <label>ID ANTRIAN</label>
                <div class="kotak-input">
                    <i class="fa-solid fa-hashtag"></i>
                    <input type="text" value="<?= $data['id_antrian']; ?>" disabled>
                </div>
            </div>

            <div class="grup">
                <label>KODE ANTRIAN</label>
                <div class="kotak-input">
                    <i class="fa-solid fa-barcode"></i>
                    <input type="text" name="kode_antrian" value="<?= htmlspecialchars($data['kode_antrian']); ?>" required>
                </div>
            </div>

            <div class="grup">
                <label>PASIEN</label>
                <div class="kotak-input">
                    <i class="fa-solid fa-user"></i>
                    <select name="id_pasien" required>
                        <option value="">-- Pilih Pasien --</option>
                        <?php while($p = mysqli_fetch_assoc($list_pasien)){ ?>
                            <option value="<?= $p['id_pasien']; ?>" <?= ($p['id_pasien'] == $data['id_pasien']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($p['nama_lengkap']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="baris-dua">

                <div class="grup">
                    <label>POLI</label>
                    <div class="kotak-input">
                        <i class="fa-solid fa-building"></i>
                        <select name="id_poli" required>
                            <option value="">-- Pilih Poli --</option>
                            <?php while($pl = mysqli_fetch_assoc($list_poli)){ ?>
                                <option value="<?= $pl['id_poli']; ?>" <?= ($pl['id_poli'] == $data['id_poli']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($pl['nama_poli']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="grup">
                    <label>DOKTER</label>
                    <div class="kotak-input">
                        <i class="fa-solid fa-user-doctor"></i>
                        <select name="id_dokter" required>
                            <option value="">-- Pilih Dokter --</option>
                            <?php while($d = mysqli_fetch_assoc($list_dokter)){ ?>
                                <option value="<?= $d['id_dokter']; ?>" <?= ($d['id_dokter'] == $data['id_dokter']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($d['nama_dokter']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

            </div>

            <div class="baris-dua">

                <div class="grup">
                    <label>TANGGAL KUNJUNGAN</label>
                    <div class="kotak-input">
                        <i class="fa-solid fa-calendar-days"></i>
                        <input type="date" name="tanggal_kunjungan" value="<?= $data['tanggal_kunjungan']; ?>" required>
                    </div>
                </div>

                <div class="grup">
                    <label>NO. ANTRIAN</label>
                    <div class="kotak-input">
                        <i class="fa-solid fa-list-ol"></i>
                        <input type="number" name="nomor_antrian" value="<?= $data['nomor_antrian']; ?>" required>
                    </div>
                </div>

            </div>

            <div class="grup">
                <label>STATUS</label>
                <div class="kotak-input">
                    <i class="fa-solid fa-circle-info"></i>
                    <select name="status" required>
                        <?php
                        $opsi_status = ["Menunggu", "Dipanggil", "Sedang Dilayani", "Selesai", "Batal"];
                        foreach($opsi_status as $opsi){
                            $selected = ($opsi == $data['status']) ? 'selected' : '';
                            echo "<option value='$opsi' $selected>$opsi</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="aksi-form">
                <button type="submit" name="simpan" class="btn-simpan">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Perubahan
                </button>
                <a href="antrian.php">
                    <button type="button" class="btn-batal">Batal</button>
                </a>
            </div>

        </form>

    </div>

</div>

</body>
</html>