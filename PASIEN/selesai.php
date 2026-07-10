<?php
session_start();
include "koneksi.php";

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit;
}

$id_user=$_SESSION['id_user'];

$query=mysqli_query($conn,"
SELECT
a.*,
d.nama_dokter,
p.nama_poli,
p.lokasi
FROM antrian a

JOIN pasien ps
ON a.id_pasien=ps.id_pasien

JOIN dokter d
ON a.id_dokter=d.id_dokter

JOIN poli p
ON a.id_poli=p.id_poli

WHERE ps.id_user='$id_user'

ORDER BY a.id_antrian DESC

LIMIT 1
");

if(mysqli_num_rows($query)==0){
    die("Antrian tidak ditemukan");
}

$data=mysqli_fetch_assoc($query);

$kode_antrian=$data['kode_antrian'];
$no_antrian=$data['nomor_antrian'];
$nama_dokter=$data['nama_dokter'];
$nama_poli=$data['nama_poli'];
$tanggal=$data['tanggal_kunjungan'];
$status=$data['status'];

$qr_data=urlencode($kode_antrian);

$q=mysqli_query($conn,"
SELECT nomor_antrian
FROM antrian
WHERE id_poli='".$data['id_poli']."'
AND tanggal_kunjungan='".$tanggal."'
AND status='Sedang Dilayani'
ORDER BY nomor_antrian ASC
LIMIT 1
");


if(mysqli_num_rows($q)>0){

    $d=mysqli_fetch_assoc($q);

    $sedang=$d['nomor_antrian'];

}else{

    $sedang=0;

}

?>

<html>
<head>
    <title>Selesai - Tiket Antrian</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins',sans-serif;
        }

        body{
            width:100%;
            height:100vh;
            background:#f5f7fa;
        }

        /* NAVBAR */
        .header-container{
            background:#fff;
            box-shadow:0 4px 6px rgba(0,0,0,0.05);
        }

        .navbar{
            max-width:1300px;
            margin:auto;
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:15px 40px;
        }

        .logo-container{
            display:flex;
            align-items:center;
            gap:12px;
        }

        .logo-img{
            width:50px;
        }

        .logo-text{
            display:flex;
            flex-direction:column;
        }

        .logo-name{
            font-size:1.1rem;
            font-weight:700;
            color:#124c8b;
        }

        .logo-brand{
            color:#124c8b;
        }

        .nav-links{
            list-style:none;
            display:flex;
            gap:35px;
        }

        .nav-links a{
            text-decoration:none;
            color:#3174b5;
            font-weight:500;
        }

        .nav-actions{
            display:flex;
            align-items:center;
            gap:20px;
        }

        .notif-icon i{
            color:#3174b5;
            font-size:20px;
        }

        .login-btn{
            text-decoration:none;
            color:#16aeb9;
            border:2px solid #16aeb9;
            padding:8px 25px;
            border-radius:8px;
            font-weight:600;
        }

        .login-btn:hover{
            background:#16aeb9;
            color:#fff;
        }

        .layanan-container{
        width:100%;
            padding:30px 80px;
        }

        .step-container{
            display:flex;
            align-items:center;
            margin-bottom:25px;
            gap:28px;
            flex-wrap:wrap;
        }

        .step{
            display:flex;
            align-items:center;
            gap:10px;
            color:#888;
            font-size:16px;
            font-weight:500;
        }

        .step span{
            width:28px;
            height:28px;
            border-radius:50%;
            background:#8a8a8a;
            color:#fff;
            display:flex;
            justify-content:center;
            align-items:center;
            font-size:13px;
            font-weight:600;
        }

        .step.active{
            color:#0a1d8f;
            font-weight:600;
        }

        .step.active span{
            background:#0a1d8f;
        }

        .arrow{
            font-size:28px;
            color:#7f7f7f;
            margin:0 10px;
        }

        .line{
            width:40px;
            height:2px;
            background:#ccc;
        }

        /* WRAPPER */
        .selesai-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            max-width: 1100px;
            margin: 0 auto;
        }

        /* ===== TIKET KIRI ===== */
        .tiket-card {
            background: #0a1d8f;
            border-radius: 20px;
            padding: 28px;
            color: #fff;
            position: relative;
            overflow: hidden;
            margin-top: 25px;
        }

        .tiket-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .tiket-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255,255,255,0.65);
            margin-bottom: 6px;
        }

        .tiket-nomor {
            font-size: 52px;
            font-weight: 700;
            line-height: 1;
            letter-spacing: 2px;
        }

        .badge-active {
            background: #23c55e;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .badge-active::before {
            content: '';
            width: 7px;
            height: 7px;
            background: #fff;
            border-radius: 50%;
            display: inline-block;
        }

        .sesi-label {
            font-size: 13px;
            color: rgba(255,255,255,0.7);
            margin-top: 4px;
        }

        .tiket-divider {
            border: none;
            border-top: 1px dashed rgba(255,255,255,0.25);
            margin: 20px 0;
        }

        .tiket-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 22px;
        }

        .tiket-info-item .t-label {
            font-size: 11px;
            color: rgba(255,255,255,0.55);
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 5px;
        }

        .tiket-info-item .t-value {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
        }

        .qr-wrapper {
            background: #fff;
            border-radius: 15px;
            padding: 50px;
            text-align: center;
        }

        .qr-wrapper img {
            width: 130px;
            height: 130px;
        }

        .qr-hint {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
            line-height: 1.5;
        }

        /* ===== PANEL KANAN ===== */
        .right-panel {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 25px;
        }

        .panel-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.07);
        }

        .panel-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 700;
            color: #111;
            margin-bottom: 18px;
        }

        .panel-title i {
            color: #0a1d8f;
        }

        /* Langkah */
        .langkah-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .langkah-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .langkah-num {
            min-width: 28px;
            height: 28px;
            background: #0a1d8f;
            border-radius: 50%;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .langkah-text {
            font-size: 14px;
            color: #444;
            line-height: 1.5;
            padding-top: 3px;
        }

        .link-peta {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #0a1d8f;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            margin-top: 12px;
        }

        /* Status antrian */
        .status-box {
            margin-bottom: 6px;
        }

        .status-now {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .status-nomor {
            font-size: 28px;
            font-weight: 700;
            color: #0a1d8f;
        }

        .status-keterangan {
            font-size: 13px;
            color: #23c55e;
            font-weight: 600;
        }

        .progress-bar {
            background: #e8edf5;
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .progress-fill {
            height: 100%;
            background: #23c55e;
            border-radius: 10px;
            width: 60%;
        }

        .antrian-info-text {
            font-size: 12px;
            color: #888;
        }

        /* Buttons */
        .btn-simpan {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #0a1d8f;
            color: #fff;
            padding: 13px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            width: 100%;
            cursor: pointer;
            border: none;
            transition: 0.3s;
        }

        .btn-simpan:hover { background: #16aeb9; }

        .btn-batal {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #fff;
            color: #e53e3e;
            padding: 13px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            width: 100%;
            cursor: pointer;
            border: 1px solid #fcc;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-batal:hover { background: #fff5f5; }

        .chat-button { position:fixed; right:25px; bottom:25px; width:55px; height:55px; background:#001c8c; border-radius:50%; display:flex; justify-content:center; align-items:center; color:#fff; font-size:22px; cursor:pointer; text-decoration:none; }
        .chat-button:hover { background:#16aeb9; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<header class="header-container">
    <nav class="navbar">
        <div class="logo-container">
            <img src="https://i.imgur.com/0Olben8.png" class="logo-img">
            <div class="logo-text">
                <span class="logo-name">Rumah Sakit</span>
                <span class="logo-brand">Keliling Dunia</span>
            </div>
        </div>
        <ul class="nav-links">
            <li class="nav-item active"><a href="index.php">Home</a></li>
            <li class="nav-item"><a href="#layanan">Layanan</a></li>
            <li class="nav-item"><a href="#informasi">Informasi</a></li>
            <li class="nav-item"><a href="#kontak">Kontak Kami</a></li>
        </ul>
        <div class="nav-actions">
            <a href="http://localhost/rumahsakit/login.php" class="login-btn">Logout</a>
        </div>
    </nav>
</header>

<div class="layanan-container">

    <!-- STEP -->
    <div class="step-container">
        <div class="step"><span>1</span><p>Pilih Layanan</p></div>
        <div class="arrow">&#8594;</div>
        <div class="step"><span>2</span><p>Pilih Jadwal</p></div>
        <div class="arrow">&#8594;</div>
        <div class="step"><span>3</span><p>Konfirmasi</p></div>
        <div class="arrow">&#8594;</div>
        <div class="step active"><span>4</span><p>Selesai</p></div>
    </div>

    <div class="selesai-wrapper">

        <!-- ===== TIKET KIRI ===== -->
        <div class="tiket-card">

            <div class="tiket-header">
                <div>
                    <div class="tiket-label">Nomor Antrian Anda</div>
                    <div class="tiket-nomor">
                        A-<?= str_pad($no_antrian, 2, '0', STR_PAD_LEFT) ?>
                    </div>
                    <div class="sesi-label">Sesi Pagi</div>
                </div>
                <div class="badge-active">
                    <?= $status ?>
                </div>
            </div>

            <hr class="tiket-divider">

            <div class="tiket-info-grid">
                <div class="tiket-info-item">
                    <div class="t-label"><i class="fas fa-user-md"></i> Dokter</div>
                    <div class="t-value"><?= htmlspecialchars($nama_dokter) ?></div>
                </div>
                <div class="tiket-info-item">
                    <div class="t-label"><i class="fas fa-hospital"></i> Departemen</div>
                    <div class="t-value"><?= htmlspecialchars($nama_poli) ?></div>
                </div>
                <div class="tiket-info-item">
                    <div class="t-label"><i class="far fa-calendar"></i> Tanggal</div>
                    <div class="t-value"><?= date('d-m-Y',strtotime($tanggal)); ?></div>
                </div>
            </div>

            <div class="qr-wrapper">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=<?= $qr_data ?>" alt="QR Code">
                <p class="qr-hint">Pindai kode ini pada layar saat tiba di rumah sakit untuk konfirmasi kehadiran.</p>
            </div>

        </div>

        <!-- ===== PANEL KANAN ===== -->
        <div class="right-panel">

            <!-- Langkah Selanjutnya -->
            <div class="panel-card">
                <div class="panel-title">
                    <i class="fas fa-info-circle"></i> Langkah Selanjutnya
                </div>
                <div class="langkah-list">
                    <div class="langkah-item">
                        <div class="langkah-num">1</div>
                        <div class="langkah-text">Tiba di Rumah Sakit paling lambat 15 menit sebelum jadwal check-in.</div>
                    </div>
                    <div class="langkah-item">
                        <div class="langkah-num">2</div>
                        <div class="langkah-text">Siapkan Kartu Identitas atau BPJS Kesehatan Anda.</div>
                    </div>
                    <div class="langkah-item">
                        <div class="langkah-num">3</div>
                        <div class="langkah-text">Tunjukkan kode QR ini kepada petugas di loket pendaftaran.</div>
                    </div>
                </div>
                <a href="#" class="link-peta">
                    <i class="fas fa-map-marker-alt"></i> Lihat Peta Rumah Sakit
                </a>
            </div>

            <!-- Status Antrian -->
            <div class="panel-card">
                <div class="panel-title">
                    <i class="fas fa-list-ol"></i> Status Antrian Saat Ini
                </div>
                <div class="status-box">
                    <div class="status-now">
                        <div class="status-nomor">
                            A-<?= str_pad($sedang,2,'0',STR_PAD_LEFT) ?>
                        </div>
                        <div class="status-keterangan">Sedang Dilayani</div>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                    <div class="antrian-info-text">
                        ~<?= max(0,$no_antrian-$sedang-1) ?> orang sebelum Anda
                    </div>
                </div>
            </div>

            <!-- Tombol -->
            <div class="panel-card" style="padding:20px;">
                <a href="#" class="btn-simpan" onclick="window.print()">
                    <i class="fas fa-download"></i> Simpan Tiket
                </a>
                <a href="pilih_jadwal.php" class="btn-batal">
                    <i class="fas fa-times-circle"></i> Batalkan
                </a>
            </div>

        </div>

    </div>
</div>

<a href="#" class="chat-button">
    <i class="fas fa-headset"></i>
</a>


<script>

setInterval(function(){

    location.reload();

},9000);

</script>
</body>
</html>