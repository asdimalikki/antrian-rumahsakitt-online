<?php
session_start();
include "koneksi.php";

if(!isset($_SESSION['id_dokter'])){
    header("Location: pilih_layanan.php");
    exit;
}

$id_dokter = $_SESSION['id_dokter'];

// Query dokter
$sqlDokter = mysqli_query($conn,"
SELECT d.*, p.nama_poli
FROM dokter d
JOIN poli p ON d.id_poli = p.id_poli
WHERE d.id_dokter='$id_dokter'
");

$dokter = mysqli_fetch_assoc($sqlDokter);

// Query jadwal
$sqlJadwal = mysqli_query($conn,"
SELECT *
FROM jadwal
WHERE id_dokter='$id_dokter'
ORDER BY tanggal
");

$sqlJadwal = mysqli_query($conn,"
SELECT DISTINCT tanggal
FROM jadwal
WHERE id_dokter='$id_dokter'
ORDER BY tanggal
");
$tanggal_jadwal = [];

while($jadwal = mysqli_fetch_assoc($sqlJadwal)){
    $tanggal_jadwal[] = $jadwal['tanggal'];
}
$bulan_awal = date('n') - 1;
$tahun_awal = date('Y');

if(!empty($tanggal_jadwal)){
    $bulan_awal = date('n', strtotime($tanggal_jadwal[0])) - 1;
    $tahun_awal = date('Y', strtotime($tanggal_jadwal[0]));
}
?>
<html>
<head>
    <title>Pilih Jadwal</title>

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

        .container{
            width:100%;
            height:100vh;
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

.sub-title{
    color:#555;
    margin-bottom:20px;
}
.jadwal-container{
    display:grid;
    grid-template-columns:500px 1fr;
    gap:30px;
    margin-top:30px;
}

.kalender-box,
.jam-box{
    background:#fff;
    padding:20px;
    border-radius:10px;
    box-shadow:0 3px 10px rgba(0,0,0,0.08);
}

.bulan{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin:25px 0;
}

.bulan i{
    cursor:pointer;
}

.hari,
.tanggal{
    display:grid;
    grid-template-columns:repeat(7,1fr);
    gap:12px;
    text-align:center;
}

.hari{
    margin-bottom:20px;
    font-weight:600;
    color:#666;
}

.tanggal span{
    padding:8px;
    border-radius:50%;
    cursor:pointer;
}

.tanggal span:hover{
    background: #e7e7e7;
}

.nonaktif{
    color:#bbb;
    background:#f3f3f3;
    pointer-events:none;
}

.tersedia-tanggal{
    cursor:pointer;
}
.tidak-tersedia{
    background:#e5e5e5 !important;
    color:#999;
    border:none !important;
    cursor:not-allowed;
}

.tersedia:hover{
    background: #e7e7e7 !important;
    color:white;
}

.pilih-tanggal{
    background:#2dc653;
    color:white;
}

.aktif{
    background:#2dc653;
    color:#fff;
}

.legend{
    display:flex;
    gap:25px;
    margin:20px 0;
}

.legend div{
    display:flex;
    align-items:center;
    gap:8px;
    font-size:14px;
}

.dot{
    width:12px;
    height:12px;
    border-radius:50%;
}

.hijau{
    background:#1fc748;
}

.kuning{
    background:#f3b400;
}

.abu{
    background:#999;
}

.jam-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:15px;
    margin:20px 0;
}

.jam-grid button{
    padding:10px;
    border:1px solid #bfbfbf;
    border-radius:5px;
    background:#fff;
    cursor:pointer;
}

.tersedia{
    background:#b9f1a5 !important;
    border:none !important;
}

.aktif-jam{
    background:#b9f1a5 !important;
    color:#1b5e20;
    border:none !important;
}

.ringkasan{
    margin-top:30px;
    background:#dfe4ff;
    padding:20px;
    border-radius:10px;
}

.ringkasan h3{
    margin-bottom:20px;
}

.ringkasan-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;
}

.ringkasan-grid small{
    color:#666;
}

.btn-lanjut{
    display:block;
    width:100%;
    margin-top:20px;
    padding:14px;
    background:#081b8f;
    color:#fff;
    text-align:center;
    text-decoration:none;
    border-radius:5px;
    font-weight:600;
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

    <div class="step active">
        <span>2</span>
        <p>Pilih Jadwal</p>
    </div>

    <div class="arrow">&#8594;</div>

    <div class="step">
        <span>3</span>
        <p>Konfirmasi</p>
    </div>

    <div class="arrow">&#8594;</div>

    <div class="step">
        <span>4</span>
        <p>Selesai</p>
    </div>

</div>

    <h2>Pilih Tanggal & Jam</h2>
    <p class="sub-title">
        Pilih Jadwal Yang Tersedia
    </p>


    <div class="jadwal-container">

    <!-- Kalender -->
    <div class="kalender-box">
        <h4>Pilih Tanggal</h4>

        <div class="bulan">
            <i class="fas fa-chevron-left"
            onclick="bulanSebelumnya()"></i>

            <span id="namaBulan"></span>

            <i class="fas fa-chevron-right"
            onclick="bulanBerikutnya()"></i>
        </div>

        <div class="hari">
            <span>Sen</span>
            <span>Sel</span>
            <span>Rab</span>
            <span>Kam</span>
            <span>Jum</span>
            <span>Sab</span>
            <span>Min</span>
        </div>

        <div class="tanggal" id="kalender"></div>
    </div>

    <!-- Pilih Jam -->
    <div class="jam-box">

        <h4>Pilih Jam</h4>

        <div class="legend">
            <div><span class="dot hijau"></span>Tersedia</div>
            <div><span class="dot abu"></span>Tidak Tersedia</div>
        </div>

        <div class="jam-grid" id="daftarJam">
        </div>

    </div>

</div>

<div class="ringkasan">

    <h3>Ringkasan Pilihan</h3>

    <div class="ringkasan-grid">
        <div>
            <small>Poli</small>
            <p><?= $dokter['nama_poli']; ?></p>
        </div>

        <div>
            <small>Dokter</small>
            <p><?= $dokter['nama_dokter']; ?></p>
        </div>

        <div>
            <small>Tanggal</small>
            <p id="ringkasanTanggal">Belum dipilih</p>
        </div>

        <div>
            <small>Jam</small>
            <p id="ringkasanJam">Belum dipilih</p>
        </div>
    </div>

</div>

<form action="konfirmasi.php" method="POST">

    <input type="hidden" name="id_jadwal" id="inputIdJadwal">
    <input type="hidden" name="tanggal" id="inputTanggal">
    <input type="hidden" name="jam_mulai" id="inputJamMulai">
    <input type="hidden" name="jam_selesai" id="inputJamSelesai">       

    <button type="submit"
            class="btn-lanjut">
        Buat Antrian Sekarang
    </button>

</form>


</div>

</div>

<div class="chat-button">
    <i class="fas fa-headset"></i>
</div>
    


</div>
<script>
let tanggalJadwal = <?= json_encode($tanggal_jadwal); ?>;
let tahun = <?= $tahun_awal ?>;
let bulan = <?= $bulan_awal ?>;

let tanggalDipilih = '';
let jamDipilih = '';

const namaBulan = [
    'Januari','Februari','Maret',
    'April','Mei','Juni',
    'Juli','Agustus','September',
    'Oktober','November','Desember'
];

updateNamaBulan();
buatKalender();

function updateNamaBulan(){
    document.getElementById('namaBulan').innerHTML =
        namaBulan[bulan] + ' ' + tahun;
}

function buatKalender(){

    let kalender = document.getElementById('kalender');

    let firstDay = new Date(tahun, bulan, 1).getDay();
    let lastDate = new Date(tahun, bulan + 1, 0).getDate();

    kalender.innerHTML = "";

    if(firstDay == 0){
        firstDay = 7;
    }

    for(let i=1;i<firstDay;i++){
        kalender.innerHTML += "<span></span>";
    }

    for(let i=1;i<=lastDate;i++){

        let fullDate =
            tahun + "-" +
            String(bulan + 1).padStart(2,'0') + "-" +
            String(i).padStart(2,'0');

        if(tanggalJadwal.includes(fullDate))
        {
            kalender.innerHTML += `
                <span class="tersedia-tanggal"
                      onclick="ambilJam('${fullDate}',this)">
                    ${i}
                </span>
            `;
        }
        else
        {
            kalender.innerHTML += `
                <span class="nonaktif">${i}</span>
            `;
        }
    }
}

function ambilJam(tanggal,element){

    document
        .querySelectorAll('.tersedia-tanggal')
        .forEach(x => x.classList.remove('pilih-tanggal'));

    element.classList.add('pilih-tanggal');

    tanggalDipilih = tanggal;

    document.getElementById('ringkasanTanggal').innerHTML =
        tanggal;

    fetch(
        'get_jam.php?id_dokter=<?=$id_dokter;?>&tanggal=' + tanggal
    )
    .then(res => res.text())
    .then(data => {
        document.getElementById('daftarJam').innerHTML = data;
    });
}
function pilihJam(idJadwal, jamMulai, jamSelesai){

    document.getElementById('ringkasanJam').innerHTML =
        jamMulai + ' - ' + jamSelesai;

    document.getElementById('inputIdJadwal').value =
        idJadwal;

    document.getElementById('inputTanggal').value =
        tanggalDipilih;

    document.getElementById('inputJamMulai').value =
        jamMulai;

    document.getElementById('inputJamSelesai').value =
        jamSelesai;
}

/* ==========================
   CEK BULAN ADA JADWAL ATAU TIDAK
   ========================== */
function adaJadwalDiBulan(bulanCek, tahunCek){

    let bulanString =
        tahunCek + "-" +
        String(bulanCek + 1).padStart(2,'0');

    return tanggalJadwal.some(tanggal =>
        tanggal.startsWith(bulanString)
    );
}

/* ==========================
   BULAN BERIKUTNYA
   ========================== */
function bulanBerikutnya(){

    let nextBulan = bulan + 1;
    let nextTahun = tahun;

    if(nextBulan > 11){
        nextBulan = 0;
        nextTahun++;
    }

    if(adaJadwalDiBulan(nextBulan, nextTahun)){
        bulan = nextBulan;
        tahun = nextTahun;

        updateNamaBulan();
        buatKalender();
    }
}

/* ==========================
   BULAN SEBELUMNYA
   ========================== */
function bulanSebelumnya(){

    let prevBulan = bulan - 1;
    let prevTahun = tahun;

    if(prevBulan < 0){
        prevBulan = 11;
        prevTahun--;
    }

    if(adaJadwalDiBulan(prevBulan, prevTahun)){
        bulan = prevBulan;
        tahun = prevTahun;

        updateNamaBulan();
        buatKalender();
    }
}

/* ==========================
   EVENT TOMBOL PANAH
   ========================== */
document.querySelector('.fa-chevron-right')
    .onclick = bulanBerikutnya;

document.querySelector('.fa-chevron-left')
    .onclick = bulanSebelumnya;
</script>
</body>
</html>