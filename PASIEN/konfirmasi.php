<?php
session_start();
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['id_jadwal'])) {
        header("Location: pilih_jadwal.php");
        exit;
    }

    $_SESSION['id_jadwal']   = (int)$_POST['id_jadwal'];
    $_SESSION['tanggal']     = $_POST['tanggal'];
    $_SESSION['jam_mulai']   = $_POST['jam_mulai'];
    $_SESSION['jam_selesai'] = $_POST['jam_selesai'];

    // Redirect supaya halaman berubah menjadi GET
    header("Location: konfirmasi.php");
    exit;
}

if (!isset($_SESSION['id_jadwal'])) {

    header("Location: pilih_jadwal.php");
    exit;
}

if (!isset($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit;
}

$id_jadwal = (int)$_SESSION['id_jadwal'];
$sql = "SELECT j.id_jadwal, j.hari, j.tanggal, j.jam_mulai, j.jam_selesai,
               d.nama_dokter, d.spesialis, d.id_poli,
               p.nama_poli, p.lokasi
        FROM jadwal j
        JOIN dokter d ON j.id_dokter = d.id_dokter
        JOIN poli   p ON d.id_poli   = p.id_poli
        WHERE j.id_jadwal = $id_jadwal
        LIMIT 1";

$result = mysqli_query($conn, $sql);
$data   = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: pilih_jadwal.php");
    exit;
}

$data['tanggal']     = $_SESSION['tanggal'];
$data['jam_mulai']   = $_SESSION['jam_mulai'];
$data['jam_selesai'] = $_SESSION['jam_selesai'];

// Ambil data pasien (jenis_pasien & no_bpjs) berdasarkan pasien yang sedang login
$id_user_login = (int)$_SESSION['id_user'];
$sqlPasien = mysqli_query($conn,
    "SELECT jenis_pasien, no_bpjs FROM pasien WHERE id_user = $id_user_login LIMIT 1");
$dataPasien = $sqlPasien ? mysqli_fetch_assoc($sqlPasien) : null;

$jenis_pasien = ($dataPasien && !empty($dataPasien['jenis_pasien'])) ? $dataPasien['jenis_pasien'] : 'umum';
$is_bpjs      = ($jenis_pasien == 'bpjs');

// Nomor antrian estimasi (opsional, hanya tampilan — nomor final tetap dibuat saat insert ke tabel antrian)
$nama_pasien = isset($_SESSION['nama_pasien']) ? $_SESSION['nama_pasien'] : null;

// Estimasi nomor antrian: ambil nomor_antrian TERBESAR yang sudah dipakai
// untuk poli & tanggal kunjungan yang sama, lalu +1. Nomor final tetap
// ditentukan saat insert di buat_antrian_proses.php (idealnya pakai logika
// yang sama supaya konsisten dengan yang ditampilkan di sini).
$id_poli_terpilih = (int)$data['id_poli'];
$tanggal_kunjungan = $data['tanggal'];

$nomor_antrian = 1;
$sqlAntrian = mysqli_query($conn, "
    SELECT MAX(nomor_antrian) AS max_no
    FROM antrian
    WHERE id_poli = $id_poli_terpilih
    AND tanggal_kunjungan = '$tanggal_kunjungan'
");
if ($sqlAntrian) {
    $rowAntrian = mysqli_fetch_assoc($sqlAntrian);
    $nomor_antrian = ($rowAntrian['max_no'] !== null) ? (int)$rowAntrian['max_no'] + 1 : 1;
}

// Biaya administrasi awal: gratis untuk pasien BPJS
$biaya_admin = $is_bpjs ? 0 : 10000;
?>

<html>
<head>
    <title>Konfirmasi</title>

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
            min-height:100vh;
            background:#f5f7fa;
        }

        .container{
            width:100%;
            min-height:100vh;
            display:flex;
            flex-direction:column;
        }

        /* ================= NAVBAR ================= */

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
            max-width:1300px;
            margin:0 auto;
            padding:30px 80px 60px;
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

        .sub-title{
            color:#555;
            margin-bottom:30px;
        }

        /* ================= LAYOUT KONFIRMASI ================= */

        .konfirmasi-wrapper{
            display:grid;
            grid-template-columns: 1.4fr 1fr;
            gap:25px;
            align-items:start;
        }

        .card{
            background:#fff;
            border-radius:18px;
            padding:30px;
            box-shadow:0 8px 25px rgba(0,0,0,.06);
            width:100%;
        }

        .card-title{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:25px;
            gap:12px;
        }

        .card-title-left{
            display:flex;
            align-items:center;
            gap:12px;
        }

        .card-title .icon-circle{
            width:38px;
            height:38px;
            border-radius:10px;
            background:#eef3ff;
            display:flex;
            justify-content:center;
            align-items:center;
            color:#0a1d8f;
            font-size:16px;
            flex-shrink:0;
        }

        .card-title h3{
            font-size:17px;
            font-weight:700;
            color:#1d3557;
        }

        .badge-antrian{
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            background:#0a1d8f;
            color:#fff;
            padding:8px 18px;
            border-radius:12px;
            flex-shrink:0;
        }

        .badge-antrian small{
            font-size:11px;
            color:#c7d4ff;
            font-weight:500;
            letter-spacing:.3px;
        }

        .badge-antrian strong{
            font-size:20px;
            font-weight:700;
            line-height:1.2;
        }

        .detail-list{
            display:flex;
            flex-direction:column;
        }

        .detail-item{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:16px 0;
            border-bottom:1px solid #ececec;
        }

        .detail-item:last-child{
            border-bottom:none;
        }

        .detail-item span{
            color:#777;
            font-size:14px;
        }

        .detail-item strong{
            color:#222;
            font-size:15px;
            font-weight:600;
            text-align:right;
        }

        .badge-poli{
            display:inline-block;
            background:#eef3ff;
            color:#0a1d8f;
            font-size:12px;
            font-weight:600;
            padding:5px 14px;
            border-radius:20px;
            margin-top:10px;
        }

        .badge-jenis-pasien{
            display:inline-block;
            padding:4px 12px;
            border-radius:20px;
            font-size:12px;
            font-weight:700;
        }

        .badge-jenis-umum{
            background:#f1f5f9;
            color:#475569;
        }

        .badge-jenis-bpjs{
            background:#dcfce7;
            color:#15803d;
        }

        .text-gratis{
            color:#15803d;
        }

        /* ===== Kartu info alur pembayaran ===== */

        .flow-card{
            background:linear-gradient(135deg,#0a1d8f,#123fb0);
            border-radius:18px;
            padding:28px;
            color:#fff;
        }

        .flow-card h3{
            font-size:16px;
            margin-bottom:6px;
        }

        .flow-card p{
            font-size:13px;
            color:#dbe4ff;
            margin-bottom:20px;
            line-height:20px;
        }

        .flow-steps{
            display:flex;
            flex-direction:column;
            gap:14px;
        }

        .flow-step{
            display:flex;
            align-items:flex-start;
            gap:12px;
        }

        .flow-step .num{
            width:26px;
            height:26px;
            border-radius:50%;
            background:rgba(255,255,255,.15);
            border:1px solid rgba(255,255,255,.35);
            display:flex;
            justify-content:center;
            align-items:center;
            font-size:12px;
            font-weight:700;
            flex-shrink:0;
        }

        .flow-step.current .num{
            background:#16aeb9;
            border-color:#16aeb9;
        }

        .flow-step p{
            margin:0;
            font-size:13px;
            color:#fff;
            line-height:19px;
        }

        .flow-step small{
            display:block;
            color:#c7d4ff;
            font-size:12px;
            margin-top:2px;
        }

        /* ===== Info box pembayaran ditunda ===== */

        .info-box{
            margin-top:20px;
            background:#fff9e8;
            border-left:5px solid #f3b400;
            padding:18px 20px;
            border-radius:12px;
            display:flex;
            gap:14px;
        }

        .info-box i{
            color:#c98600;
            font-size:20px;
            margin-top:2px;
        }

        .info-box p{
            color:#6b5400;
            font-size:13px;
            line-height:19px;
        }

        .info-box strong{
            display:block;
            color:#4a3c00;
            font-size:14px;
            margin-bottom:4px;
        }

        .rincian-note{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding-top:16px;
            margin-top:6px;
            border-top:1px dashed #ddd;
        }

        .rincian-note span{
            color:#777;
            font-size:13px;
        }

        .rincian-note strong{
            color:#0a1d8f;
            font-size:14px;
        }

        /* ================= BUTTONS ================= */

        .button-group{
            margin-top:30px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .btn-kembali{
            display:flex;
            align-items:center;
            gap:8px;
            background:#fff;
            border:1px solid #ddd;
            padding:12px 28px;
            border-radius:8px;
            text-decoration:none;
            color:#444;
            font-weight:600;
            transition:0.3s;
        }

        .btn-kembali:hover{
            background:#f1f1f1;
            border-color:#aaa;
        }

        .btn-lanjut{
            display:flex;
            align-items:center;
            gap:8px;
            background:#0a1d8f;
            color:#fff;
            padding:12px 40px;
            border:none;
            border-radius:8px;
            text-decoration:none;
            font-weight:600;
            font-size:15px;
            cursor:pointer;
            transition:0.3s;
        }

        .btn-lanjut:hover{
            background:#16aeb9;
        }

        .chat-button{
            position:fixed;
            right:25px;
            bottom:25px;
            width:55px;
            height:55px;
            background:#001c8c;
            border-radius:50%;
            display:flex;
            justify-content:center;
            align-items:center;
            color:#fff;
            font-size:22px;
            cursor:pointer;
        }

        .chat-button:hover{
            background:#16aeb9;
        }

        @media (max-width: 960px){
            .konfirmasi-wrapper{
                grid-template-columns:1fr;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Navbar -->
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
                <a href="#" class="notif-icon">
                    <i class="fas fa-bell"></i>
                </a>

                <a href="http://localhost/rumahsakit/login.php" class="login-btn">
                  Logout
                </a>
            </div>

        </nav>
    </header>

  <div class="layanan-container">

    <!-- Step -->
    <div class="step-container">
        <div class="step">
            <span>1</span>
            <p>Pilih Layanan</p>
        </div>

        <div class="arrow">&#8594;</div>

        <div class="step">
            <span>2</span>
            <p>Pilih Jadwal</p>
        </div>

        <div class="arrow">&#8594;</div>

        <div class="step active">
            <span>3</span>
            <p>Konfirmasi</p>
        </div>

        <div class="arrow">&#8594;</div>

        <div class="step">
            <span>4</span>
            <p>Selesai</p>
        </div>
    </div>

    <h2>Konfirmasi Antrian</h2>
    <p class="sub-title">
        Periksa kembali data di bawah ini sebelum mengambil nomor antrean.
    </p>

    <div class="konfirmasi-wrapper">

        <!-- kiri: ringkasan antrian -->
        <div class="left-column">

            <div class="card">

                <div class="card-title">
                    <div class="card-title-left">
                        <div class="icon-circle"><i class="fas fa-clipboard-list"></i></div>
                        <h3>Ringkasan Antrian</h3>
                    </div>

                    <div class="badge-antrian">
                        <small>No. Antrian</small>
                        <strong><?= htmlspecialchars($nomor_antrian); ?></strong>
                    </div>
                </div>

                <div class="detail-list">

                    <div class="detail-item">
                        <span>Poli</span>
                        <strong><?= htmlspecialchars($data['nama_poli']); ?></strong>
                    </div>

                    <div class="detail-item">
                        <span>Dokter</span>
                        <strong><?= htmlspecialchars($data['nama_dokter']); ?></strong>
                    </div>

                    <div class="detail-item">
                        <span>Tanggal</span>
                        <strong><?= date('d F Y', strtotime($data['tanggal'])); ?></strong>
                    </div>

                    <div class="detail-item">
                        <span>Jam</span>
                        <strong>
                            <?= substr($data['jam_mulai'],0,5); ?>
                            &ndash;
                            <?= substr($data['jam_selesai'],0,5); ?> WIB
                        </strong>
                    </div>

                    <div class="detail-item">
                        <span>Lokasi</span>
                        <strong><?= htmlspecialchars($data['lokasi']); ?></strong>
                    </div>

                    <div class="detail-item">
                        <span>Jenis Pasien</span>
                        <strong>
                            <?php if($is_bpjs){ ?>
                                <span class="badge-jenis-pasien badge-jenis-bpjs">
                                    <i class="fa-solid fa-id-card"></i> Pasien BPJS
                                </span>
                            <?php } else { ?>
                                <span class="badge-jenis-pasien badge-jenis-umum">Pasien Umum</span>
                            <?php } ?>
                        </strong>
                    </div>

                </div>

                <span class="badge-poli"><i class="fas fa-circle-check"></i> Jadwal tersedia &amp; siap dikonfirmasi</span>

                <div class="info-box">
                    <i class="fas fa-circle-info"></i>
                    <p>
                        <strong>Belum ada pembayaran di tahap ini</strong>
                        Anda hanya perlu mengambil nomor antrean sekarang. Biaya konsultasi dan obat akan
                        ditagihkan setelah pemeriksaan dokter selesai dan resep diterbitkan.
                    </p>
                </div>

            </div>

        </div>

        <!-- kanan: alur & info biaya -->
        <div class="right-column">

            <div class="flow-card">
                <h3><i class="fas fa-route"></i>&nbsp; Alur Kunjungan Anda</h3>
                <p>Pembayaran dilakukan di akhir, setelah pemeriksaan dan resep dokter terbit.</p>

                <div class="flow-steps">

                    <div class="flow-step current">
                        <div class="num">1</div>
                        <p>Ambil nomor antrean
                            <small>Tanpa biaya di muka</small>
                        </p>
                    </div>

                    <div class="flow-step">
                        <div class="num">2</div>
                        <p>Menunggu &amp; diperiksa dokter
                            <small>Sesuai nomor urut antrean</small>
                        </p>
                    </div>

                    <div class="flow-step">
                        <div class="num">3</div>
                        <p>Dokter menerbitkan resep
                            <small>Jika diperlukan tindakan/obat</small>
                        </p>
                    </div>

                    <div class="flow-step">
                        <div class="num">4</div>
                        <p>Pembayaran &amp; ambil obat di kasir/apotek
                            <small>Total biaya dihitung setelah pemeriksaan</small>
                        </p>
                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-title">
                    <div class="card-title-left">
                        <div class="icon-circle"><i class="fas fa-receipt"></i></div>
                        <h3>Estimasi Biaya Awal</h3>
                    </div>
                </div>

                <div class="detail-list">
                    <div class="detail-item">
                        <span>Biaya Administrasi</span>
                        <?php if($is_bpjs){ ?>
                            <strong class="text-gratis">Gratis (BPJS)</strong>
                        <?php } else { ?>
                            <strong>Rp<?= number_format($biaya_admin, 0, ',', '.'); ?></strong>
                        <?php } ?>
                    </div>
                </div>

                <div class="rincian-note">
                    <span>Biaya konsultasi &amp; obat</span>
                    <strong><?= $is_bpjs ? 'Ditanggung BPJS' : 'Ditentukan setelah periksa'; ?></strong>
                </div>
            </div>

        </div>

    </div>

    <!-- button -->
    <div class="button-group">
        <a href="pilih_jadwal.php" class="btn-kembali">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>

        <form action="proses_antrian.php" method="POST">
            <button type="submit" class="btn-lanjut">
                Buat Antrian <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>

  </div>

</div>

<div class="chat-button">
    <i class="fas fa-headset"></i>
</div>

</body>
</html>