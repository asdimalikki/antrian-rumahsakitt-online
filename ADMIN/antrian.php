<?php
include "koneksi.php";

/* Total seluruh antrian */
$query_total = mysqli_query($conn, "SELECT COUNT(*) AS total FROM antrian");
$data_total = mysqli_fetch_assoc($query_total);
$total_antrian = $data_total['total'];

/* Total antrian menunggu */
$query_menunggu = mysqli_query($conn, "SELECT COUNT(*) AS total FROM antrian WHERE status = 'Menunggu'");
$data_menunggu = mysqli_fetch_assoc($query_menunggu);
$total_menunggu = $data_menunggu['total'];

/* Total antrian sedang dilayani */
$query_dilayani = mysqli_query($conn, "SELECT COUNT(*) AS total FROM antrian WHERE status = 'Sedang Dilayani'");
$data_dilayani = mysqli_fetch_assoc($query_dilayani);
$total_dilayani = $data_dilayani['total'];

/* Pencarian */
$cari = "";

if(isset($_GET['cari']) && $_GET['cari'] != ""){
    $cari = mysqli_real_escape_string($conn, $_GET['cari']);
}

/* ===== PAGINATION ===== */
$per_halaman = 10;
$halaman_sekarang = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($halaman_sekarang < 1){ $halaman_sekarang = 1; }
$offset = ($halaman_sekarang - 1) * $per_halaman;

/* Hitung total baris (mengikuti filter pencarian jika ada) */
if($cari != ""){
    $where = "WHERE p.nama_lengkap LIKE '%$cari%' OR a.kode_antrian LIKE '%$cari%'";
}
else{
    $where = "";
}

$query_jumlah = mysqli_query($conn,
"SELECT COUNT(*) AS total
FROM antrian a
LEFT JOIN pasien p ON a.id_pasien = p.id_pasien
$where");

$data_jumlah = mysqli_fetch_assoc($query_jumlah);
$total_baris = $data_jumlah['total'];
$total_halaman = ceil($total_baris / $per_halaman);
if($total_halaman < 1){ $total_halaman = 1; }

/* Query data sesuai halaman aktif */
$sql = mysqli_query($conn,
"SELECT a.*, p.nama_lengkap AS nama_pasien, pl.nama_poli AS nama_poli, d.nama_dokter AS nama_dokter
FROM antrian a
LEFT JOIN pasien p ON a.id_pasien = p.id_pasien
LEFT JOIN poli pl ON a.id_poli = pl.id_poli
LEFT JOIN dokter d ON a.id_dokter = d.id_dokter
$where
ORDER BY a.id_antrian ASC
LIMIT $offset, $per_halaman");
?>

<html>
<head>
<title>Data Antrian</title>

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

/* KONTEN */

.konten{
    margin-left:270px;
    padding:40px;
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
}
.logout-wrap a:hover{
    background:#ff3d5a;
}

/* KARTU */

.kartu-header{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
    margin-top:30px;
}

.kartu{
    background:white;
    padding:20px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
    position:relative;
}

.ikon{
    width:45px;
    height:45px;
    background: #486fb7 45%;
    color:white;
    border-radius:12px;
    display:flex;
    justify-content:center;
    align-items:center;
}

.titik{
    width:14px;
    height:14px;
    border-radius:50%;
    border:3px solid #d8d8d8;
    position:absolute;
    right:20px;
    top:20px;
}

.titik.aktif{
    background:#3158ff;
    border:none;
}

.kartu h2{
    margin-top:20px;
    font-size:38px;
}

.kartu p{
    color:#888;
    margin-top:5px;
}

/* FILTER */

.filter{
    display:flex;
    align-items:center;
    gap:15px;
    margin-top:30px;
}

.filter input{
    width:280px;
    padding:13px 20px;
    border:none;
    border-radius:30px;
    background:white;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
}

.btn-cari{
    background: #486fb7;
    color:white;
    border:none;
    padding:13px 20px;
    border-radius:30px;
    cursor:pointer;
}

/* TABEL */
.kotak-tabel{
    background:white;
    padding:30px;
    border-radius:20px;
    margin-top:30px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:1100px;
    table-layout:fixed;
}

table th{
    text-align:left;
    padding:14px 12px;
    border-bottom:2px solid #ddd;
    color:#666;
    font-size:13px;
    white-space:nowrap;
    background:#fafafa;
    overflow:hidden;
    text-overflow:ellipsis;
}

table td{
    padding:14px 12px;
    border-bottom:1px solid #eee;
    font-size:13px;
    vertical-align:middle;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}

/* Lebar kolom */
table th:nth-child(1),
table td:nth-child(1){ width:50px; }   /* ID */

table th:nth-child(2),
table td:nth-child(2){ width:160px; }  /* Kode Antrian */

table th:nth-child(3),
table td:nth-child(3){ width:140px; }  /* Pasien */

table th:nth-child(4),
table td:nth-child(4){ width:130px; }  /* Poli */

table th:nth-child(5),
table td:nth-child(5){ width:160px; }  /* Dokter */

table th:nth-child(6),
table td:nth-child(6){ width:140px; }  /* Tanggal Kunjungan */

table th:nth-child(7),
table td:nth-child(7){ width:100px; }   /* No. Antrian */

table th:nth-child(8),
table td:nth-child(8){ width:100px; }  /* Status */

table th:nth-child(9),
table td:nth-child(9){ width:150px; }  /* Aksi */

table tr:hover{
    background:#f8f9ff;
}

.btn-edit{
    background:#3158ff;
    color:white;
    border:none;
    padding:7px 14px;
    border-radius:6px;
    cursor:pointer;
    font-size:12px;
}

.btn-hapus{
    background:#ff3d5a;
    color:white;
    border:none;
    padding:7px 14px;
    border-radius:6px;
    cursor:pointer;
    margin-left:4px;
    font-size:12px;
}

.badge{
    display:inline-block;
    padding:4px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:bold;
    white-space:nowrap;
}

.badge-menunggu{
    background:#fef3c7;
    color:#b45309;
}

.badge-dipanggil{
    background:#dbeafe;
    color:#1d4ed8;
}

.badge-dilayani{
    background:#dcfce7;
    color:#15803d;
}

.badge-selesai{
    background:#e5e7eb;
    color:#374151;
}

.badge-batal{
    background:#fee2e2;
    color:#b91c1c;
}

/* PAGINASI */

.paginasi-bawah{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:25px;
    flex-wrap:wrap;
    gap:15px;
}

.info-paginasi{
    color:#888;
    font-size:13px;
}

.info-paginasi strong{
    color:#444;
}

.paginasi{
    display:flex;
    align-items:center;
    gap:6px;
    list-style:none;
    flex-wrap:wrap;
}

.paginasi a,
.paginasi span{
    display:flex;
    align-items:center;
    justify-content:center;
    min-width:36px;
    height:36px;
    padding:0 10px;
    border-radius:10px;
    background:#f4f5f9;
    color:#486fb7;
    font-size:13px;
    font-weight:bold;
    text-decoration:none;
    transition:0.2s;
}

.paginasi a:hover{
    background:#dbe6fb;
}

.paginasi .aktif{
    background:#486fb7;
    color:white;
    box-shadow:0 4px 10px rgba(72,111,183,0.35);
}

.paginasi .nonaktif{
    background:#f4f5f9;
    color:#bbb;
    cursor:not-allowed;
}

.paginasi .titik-titik{
    background:transparent;
    color:#999;
    cursor:default;
}

a{
    text-decoration:none;
}
.aksi{
    display:flex;
    gap:8px;
    align-items:center;
}

.btn-icon{
    width:34px;
    height:34px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    font-size:14px;
    transition: all 0.2s ease;
}

.btn-edit{
    background:#eaf0ff;
    color:#3158ff;
}

.btn-edit:hover{
    background:#3158ff;
    color:white;
    transform:translateY(-2px);
    box-shadow:0 4px 10px rgba(49,88,255,0.35);
}

.btn-hapus{
    background:#ffe6ea;
    color:#ff3d5a;
}

.btn-hapus:hover{
    background:#ff3d5a;
    color:white;
    transform:translateY(-2px);
    box-shadow:0 4px 10px rgba(255,61,90,0.35);
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
            <div><strong>HiDoyy</strong><br><small>Admin</small></div>
        </div>
    </div>

    <div class="kartu-header">

        <div class="kartu">
            <div class="ikon">
                <i class="fa-solid fa-list-ol"></i>
            </div>
            <div class="titik aktif"></div>
            <h2><?= $total_antrian; ?></h2>
            <p>Total Antrian</p>
        </div>

        <div class="kartu">
            <div class="ikon">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div class="titik"></div>
            <h2><?= $total_menunggu; ?></h2>
            <p>Menunggu</p>
        </div>

        <div class="kartu">
            <div class="ikon">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <div class="titik"></div>
            <h2><?= $total_dilayani; ?></h2>
            <p>Sedang Dilayani</p>
        </div>

    </div>

    <form method="GET" class="filter">

        <input
        type="text"
        name="cari"
        placeholder="Cari nama pasien / kode antrian..."
        value="<?= $cari; ?>">

        <button class="btn-cari">
            <i class="fa-solid fa-magnifying-glass"></i>
            Cari
        </button>

    </form>

    <div class="kotak-tabel">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode Antrian</th>
                    <th>Pasien</th>
                    <th>Poli</th>
                    <th>Dokter</th>
                    <th>Tgl Kunjungan</th>
                    <th>No. Antrian</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php if(mysqli_num_rows($sql) > 0){ ?>

                <?php while($data = mysqli_fetch_assoc($sql)){ ?>

                <tr>
                    <td><?= $data['id_antrian']; ?></td>
                    <td><?= $data['kode_antrian'] ?? '-'; ?></td>
                    <td><?= $data['nama_pasien'] ?? '-'; ?></td>
                    <td><?= $data['nama_poli'] ?? '-'; ?></td>
                    <td><?= $data['nama_dokter'] ?? '-'; ?></td>
                    <td><?= $data['tanggal_kunjungan'] ?? '-'; ?></td>
                    <td><?= $data['nomor_antrian'] ?? '-'; ?></td>
                    <td>
                        <?php
                        $status = $data['status'];
                        $kelas = "badge-menunggu";

                        if($status == 'Dipanggil'){ $kelas = "badge-dipanggil"; }
                        elseif($status == 'Sedang Dilayani'){ $kelas = "badge-dilayani"; }
                        elseif($status == 'Selesai'){ $kelas = "badge-selesai"; }
                        elseif($status == 'Batal'){ $kelas = "badge-batal"; }
                        ?>
                        <span class="badge <?= $kelas; ?>">
                            <?= $status ?? '-'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="antrian_edit.php?id=<?= $data['id_antrian']; ?>">
                                <button class="btn-icon btn-edit" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                        </a>

                        <a href="antrian_hapus.php?id=<?= $data['id_antrian']; ?>"
                        onclick="return confirm('Yakin ingin menghapus data antrian ini?')">
                                <button class="btn-icon btn-hapus" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                        </a>
                    </td>
                </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>
                    <td colspan="9" align="center">
                        Data antrian tidak ditemukan.
                    </td>
                </tr>

            <?php } ?>

            </tbody>

        </table>

        <?php if($total_halaman > 1){ ?>

        <div class="paginasi-bawah">

            <div class="info-paginasi">
                Menampilkan <strong><?= ($offset + 1); ?>–<?= min($offset + $per_halaman, $total_baris); ?></strong>
                dari <strong><?= $total_baris; ?></strong> data
            </div>

            <ul class="paginasi">

                <?php
                $param_cari = $cari != "" ? "&cari=" . urlencode($cari) : "";

                /* Tombol Sebelumnya */
                if($halaman_sekarang > 1){
                    echo "<li><a href='?page=" . ($halaman_sekarang - 1) . $param_cari . "'><i class='fa-solid fa-chevron-left'></i></a></li>";
                }
                else{
                    echo "<li><span class='nonaktif'><i class='fa-solid fa-chevron-left'></i></span></li>";
                }

                /* Halaman 1 selalu tampil */
                if($halaman_sekarang > 3){
                    echo "<li><a href='?page=1" . $param_cari . "'>1</a></li>";
                    echo "<li><span class='titik-titik'>...</span></li>";
                }

                /* Range nomor halaman di sekitar halaman aktif */
                for($i = max(1, $halaman_sekarang - 2); $i <= min($total_halaman, $halaman_sekarang + 2); $i++){
                    if($i == $halaman_sekarang){
                        echo "<li><span class='aktif'>$i</span></li>";
                    }
                    else{
                        echo "<li><a href='?page=$i" . $param_cari . "'>$i</a></li>";
                    }
                }

                /* Halaman terakhir selalu tampil */
                if($halaman_sekarang < $total_halaman - 2){
                    echo "<li><span class='titik-titik'>...</span></li>";
                    echo "<li><a href='?page=$total_halaman" . $param_cari . "'>$total_halaman</a></li>";
                }

                /* Tombol Selanjutnya */
                if($halaman_sekarang < $total_halaman){
                    echo "<li><a href='?page=" . ($halaman_sekarang + 1) . $param_cari . "'><i class='fa-solid fa-chevron-right'></i></a></li>";
                }
                else{
                    echo "<li><span class='nonaktif'><i class='fa-solid fa-chevron-right'></i></span></li>";
                }
                ?>

            </ul>

        </div>

        <?php } ?>

    </div>

</div>

</body>
</html>
