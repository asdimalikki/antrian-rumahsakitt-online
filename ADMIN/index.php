<?php
include "koneksi.php";

$query_total = mysqli_query($conn,
"SELECT COUNT(*) AS total FROM users");
$data_total = mysqli_fetch_assoc($query_total);
$total_user = $data_total['total'];

$cari = "";

if(isset($_GET['cari']) && $_GET['cari'] != ""){
    $cari = mysqli_real_escape_string($conn, $_GET['cari']);

    $sql = mysqli_query($conn,
    "SELECT *
    FROM users
    WHERE nama LIKE '%$cari%'
    ORDER BY id_user ASC");
}
else{
    $sql = mysqli_query($conn,
    "SELECT *
    FROM users
    ORDER BY id_user ASC");
}
?>

<html>
<head>
<title>Data Users</title>

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

/* LOGOUT (bottom of sidebar) */

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

/* PROFIL */

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
    min-width:800px;
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
table tr:hover{
    background:#f8f9ff;
}

/* AKSI - ICON BUTTON */

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

        <li>
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
        <a href="http://localhost/rumahsakit/" onclick="return confirm('Yakin ingin logout?')">
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
            <div>
                <strong>HiDoyy</strong><br>
                <small>Admin</small>
            </div>
        </div>
    </div>

    <div class="kartu-header">

        <div class="kartu">
            <div class="ikon">
                <i class="fa-solid fa-users"></i>
            </div>

            <div class="titik aktif"></div>

            <h2><?= $total_user; ?></h2>
            <p>Total Pengguna</p>
        </div>

        <div class="kartu">
            <div class="ikon">
                <i class="fa-solid fa-user-shield"></i>
            </div>

            <div class="titik"></div>

            <h2>1</h2>
            <p>Total Admin</p>
        </div>

        <div class="kartu">
            <div class="ikon">
                <i class="fa-solid fa-user-tie"></i>
            </div>

            <div class="titik"></div>

            <h2><?= $total_user; ?></h2>
            <p>Total User Terdaftar</p>
        </div>

    </div>

    <form method="GET" class="filter">

        <input
        type="text"
        name="cari"
        placeholder="Cari nama user..."
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
                    <th>ID User</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php if(mysqli_num_rows($sql)>0){ ?>

                <?php while($data=mysqli_fetch_assoc($sql)){ ?>

                <tr>
                    <td><?= $data['id_user']; ?></td>
                    <td><?= $data['nama']; ?></td>
                    <td><?= $data['email']; ?></td>
                    <td>

                        <div class="aksi">

                            <a href="users_edit.php?id=<?= $data['id_user']; ?>">
                                <button class="btn-icon btn-edit" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </a>

                            <a href="users_hapus.php?id=<?= $data['id_user']; ?>"
                            onclick="return confirm('Yakin ingin menghapus data user ini?')">
                                <button class="btn-icon btn-hapus" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </a>

                        </div>

                    </td>
                </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>
                    <td colspan="4" align="center">
                        Data user tidak ditemukan.
                    </td>
                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>