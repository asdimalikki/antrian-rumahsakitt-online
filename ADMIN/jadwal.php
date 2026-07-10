<?php
include "koneksi.php";

$query_total = mysqli_query($conn, "SELECT COUNT(*) AS total FROM jadwal");
$data_total = mysqli_fetch_assoc($query_total);
$total_jadwal = $data_total['total'];

$query_dokter = mysqli_query($conn, "SELECT COUNT(DISTINCT id_dokter) AS total FROM jadwal");
$data_dokter = mysqli_fetch_assoc($query_dokter);
$total_dokter_jadwal = $data_dokter['total'];

$query_hari = mysqli_query($conn, "SELECT COUNT(DISTINCT hari) AS total FROM jadwal");
$data_hari = mysqli_fetch_assoc($query_hari);
$total_hari = $data_hari['total'];

$cari = "";

// ================= PAGINATION =================
$per_halaman = 10;
$halaman = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($halaman < 1){ $halaman = 1; }
$offset = ($halaman - 1) * $per_halaman;

if(isset($_GET['cari']) && $_GET['cari'] != ""){
    $cari = mysqli_real_escape_string($conn, $_GET['cari']);

    $where = "WHERE d.nama_dokter LIKE '%$cari%' OR j.hari LIKE '%$cari%'";

    // Hitung total data hasil pencarian (untuk total halaman)
    $query_count = mysqli_query($conn,
    "SELECT COUNT(*) AS total
    FROM jadwal j
    LEFT JOIN dokter d ON j.id_dokter = d.id_dokter
    $where");
    $total_data = mysqli_fetch_assoc($query_count)['total'];

    $sql = mysqli_query($conn,
    "SELECT j.*, d.nama_dokter AS nama_dokter
    FROM jadwal j
    LEFT JOIN dokter d ON j.id_dokter = d.id_dokter
    $where
    ORDER BY j.id_jadwal ASC
    LIMIT $offset, $per_halaman");
}
else{
    $total_data = $total_jadwal;

    $sql = mysqli_query($conn,
    "SELECT j.*, d.nama_dokter AS nama_dokter
    FROM jadwal j
    LEFT JOIN dokter d ON j.id_dokter = d.id_dokter
    ORDER BY j.id_jadwal ASC
    LIMIT $offset, $per_halaman");
}

$total_halaman = ceil($total_data / $per_halaman);
if($total_halaman < 1){ $total_halaman = 1; }
?>

<html>
<head>
<title>Data Jadwal</title>

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
    margin-left:270px;
    padding:40px;
}

.header{
    display:flex;
    justify-content:flex-end;
    align-items:center;
    gap:20px;
}

.notif{
    position:relative;
}

.notif i{
    font-size:20px;
    color:#555;
}

.bulat{
    width:18px;
    height:18px;
    background:red;
    color:white;
    border-radius:50%;
    position:absolute;
    top:-8px;
    right:-8px;
    font-size:10px;
    display:flex;
    justify-content:center;
    align-items:center;
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

.tombol{
    margin-left:auto;
}

.tombol button{
    background: #486fb7;
    color:white;
    border:none;
    padding:13px 25px;
    border-radius:30px;
    cursor:pointer;
    font-weight:bold;
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
    min-width:850px;
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
table td:nth-child(1){ width:60px; }   /* ID Jadwal */

table th:nth-child(2),
table td:nth-child(2){ width:170px; }  /* Nama Dokter */

table th:nth-child(3),
table td:nth-child(3){ width:110px; }  /* Hari */

table th:nth-child(4),
table td:nth-child(4){ width:130px; }  /* Tanggal */

table th:nth-child(5),
table td:nth-child(5){ width:110px; }  /* Jam Mulai */

table th:nth-child(6),
table td:nth-child(6){ width:110px; }  /* Jam Selesai */

table th:nth-child(7),
table td:nth-child(7){ width:150px; }  /* Aksi */

table tr:hover{
    background:#f8f9ff;
}

.badge{
    display:inline-block;
    padding:4px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:bold;
    white-space:nowrap;
    background:#dbeafe;
    color:#1d4ed8;
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

/* PAGINATION */

.pagination{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:8px;
    margin-top:25px;
    flex-wrap:wrap;
}

.pagination a,
.pagination span{
    display:inline-flex;
    justify-content:center;
    align-items:center;
    min-width:38px;
    height:38px;
    padding:0 8px;
    border-radius:10px;
    font-size:13px;
    font-weight:600;
    text-decoration:none;
    color:#486fb7;
    background:#eaf0ff;
    transition:0.2s ease;
}

.pagination a:hover{
    background:#486fb7;
    color:white;
}

.pagination .aktif{
    background:#486fb7;
    color:white;
}

.pagination .disabled{
    color:#c3c8d4;
    background:#f4f5f9;
    cursor:not-allowed;
}

.pagination .info-halaman{
    background:none;
    color:#888;
    font-weight:400;
    margin-right:auto;
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

    <?php if(isset($_GET['error'])){ ?>
                <div style="background:#ffe3e6;color:#ff3d5a;padding:14px 20px;border-radius:10px;margin-bottom:20px;">
                    <?= urldecode($_GET['error']); ?>
                </div>
        <?php } ?>

    <div class="header">
        <div class="profil">
            <img src="https://i.pinimg.com/736x/d3/84/d5/d384d5d973fa729d9db608e537e77623.jpg">
            <div><strong>HiDoyy</strong><br><small>Admin</small></div>
        </div>
    </div>

    <div class="kartu-header">

        <div class="kartu">
            <div class="ikon">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div class="titik aktif"></div>
            <h2><?= $total_jadwal; ?></h2>
            <p>Total Jadwal</p>
        </div>

        <div class="kartu">
            <div class="ikon">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <div class="titik"></div>
            <h2><?= $total_dokter_jadwal; ?></h2>
            <p>Dokter Terjadwal</p>
        </div>

        <div class="kartu">
            <div class="ikon">
                <i class="fa-solid fa-calendar-week"></i>
            </div>
            <div class="titik"></div>
            <h2><?= $total_hari; ?></h2>
            <p>Hari Aktif</p>
        </div>

    </div>

    <form method="GET" class="filter">

        <input
        type="text"
        name="cari"
        placeholder="Cari nama dokter / hari..."
        value="<?= $cari; ?>">

        <button class="btn-cari">
            <i class="fa-solid fa-magnifying-glass"></i>
            Cari
        </button>

        <div class="tombol">
            <a href="jadwal_add.php">
                <button type="button">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Jadwal
                </button>
            </a>
        </div>

    </form>

    <div class="kotak-tabel">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Dokter</th>
                    <th>Hari</th>
                    <th>Tanggal</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php if(mysqli_num_rows($sql) > 0){ ?>

                <?php while($data = mysqli_fetch_assoc($sql)){ ?>

                <tr>
                    <td><?= $data['id_jadwal']; ?></td>
                    <td><?= $data['nama_dokter'] ?? '-'; ?></td>
                    <td>
                        <span class="badge">
                            <?= $data['hari'] ?? '-'; ?>
                        </span>
                    </td>
                    <td><?= date('d-m-Y', strtotime($data['tanggal'])); ?></td>
                    <td><?= substr($data['jam_mulai'], 0, 5); ?></td>
                    <td><?= substr($data['jam_selesai'], 0, 5); ?></td>
                    <td>
                        <a href="jadwal_edit.php?id=<?= $data['id_jadwal']; ?>">
                                <button class="btn-icon btn-edit" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                        </a>

                        <a href="jadwal_hapus.php?id=<?= $data['id_jadwal']; ?>"
                        onclick="return confirm('Yakin ingin menghapus data jadwal ini?')">
                            <button class="btn-icon btn-hapus" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                        </a>
                    </td>
                </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>
                    <td colspan="7" align="center">
                        Data jadwal tidak ditemukan.
                    </td>
                </tr>

            <?php } ?>

            </tbody>

        </table>

        <?php if($total_halaman > 1){ ?>

        <div class="pagination">

            <span class="info-halaman">
                Halaman <?= $halaman; ?> dari <?= $total_halaman; ?>
            </span>

            <?php
                $param_cari = $cari !== "" ? "&cari=".urlencode($cari) : "";
            ?>

            <?php if($halaman <= 1){ ?>
                <span class="disabled"><i class="fa-solid fa-angle-left"></i></span>
            <?php } else { ?>
                <a href="?page=<?= $halaman - 1; ?><?= $param_cari; ?>"><i class="fa-solid fa-angle-left"></i></a>
            <?php } ?>

            <?php
                // Tampilkan maksimal 5 nomor halaman di sekitar halaman aktif
                $mulai = max(1, $halaman - 2);
                $akhir = min($total_halaman, $mulai + 4);
                $mulai = max(1, $akhir - 4);

                if($mulai > 1){
                    echo '<a href="?page=1'.$param_cari.'">1</a>';
                    if($mulai > 2){ echo '<span class="disabled">...</span>'; }
                }

                for($i = $mulai; $i <= $akhir; $i++){
                    if($i == $halaman){
                        echo '<span class="aktif">'.$i.'</span>';
                    } else {
                        echo '<a href="?page='.$i.$param_cari.'">'.$i.'</a>';
                    }
                }

                if($akhir < $total_halaman){
                    if($akhir < $total_halaman - 1){ echo '<span class="disabled">...</span>'; }
                    echo '<a href="?page='.$total_halaman.$param_cari.'">'.$total_halaman.'</a>';
                }
            ?>

            <?php if($halaman >= $total_halaman){ ?>
                <span class="disabled"><i class="fa-solid fa-angle-right"></i></span>
            <?php } else { ?>
                <a href="?page=<?= $halaman + 1; ?><?= $param_cari; ?>"><i class="fa-solid fa-angle-right"></i></a>
            <?php } ?>

        </div>

        <?php } ?>

    </div>

</div>

</body>
</html>