<?php
session_start();
include "koneksi.php";

$query = mysqli_query($conn,"
SELECT dokter.*, poli.nama_poli
FROM dokter
JOIN poli ON dokter.id_poli=poli.id_poli
ORDER BY dokter.nama_dokter
");
?>
<html>
<head>
    <title>Layanan Pasien</title>

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

.dokter-grid{
    display:grid;
    grid-template-columns: repeat(4, 250px);
    gap:25px;
}

.btn-pilih{
    display:block;
    width:100%;
    padding:10px;
    background:#071b8f;
    color:#fff;
    text-decoration:none;
    text-align:center;
    border-radius:5px;
    font-weight:600;
}

.btn-pilih:hover{
    background:#16aeb9;
}

.card{
    background:#fff;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
    position:relative;
}

.card img{
    width:100%;
    height:240px;
    object-fit:cover;
}

.status{
    position:absolute;
    top:12px;
    right:12px;
    padding:5px 12px;
    border-radius:20px;
    font-size:11px;
    font-weight:600;
}

.available{
    background:#d9f8d9;
    color:#008a00;
}

.available::before{
    content:"";
    width:8px;
    height:8px;
    background:#00b300;
    border-radius:50%;
    display:inline-block;
}

.break{
    background:#ffe3e3;
    color:#d30000;
}

.break::before{
    content:"";
    width:8px;
    height:8px;
    background:#ff0000;
    border-radius:50%;
    display:inline-block;
}

.card-body{
    padding:15px;
}

.card-body h4{
    font-size:15px;
    color:#222;
}

.card-body p{
    color:#666;
    margin-bottom:15px;
    font-size:14px;
}

.card-body button{
    width:100%;
    padding:10px;
    background:#071b8f;
    color:#fff;
    border:none;
    border-radius:5px;
    cursor:pointer;
    font-weight:600;
}

.card-body button:hover{
    background:#16aeb9;
}

.card-body button:disabled{
    background:#dcdcdc;
    cursor:not-allowed;
    color:#777;
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

    <div class="step active">
        <span>1</span>
        <p>Pilih Layanan</p>
    </div>

    <div class="arrow">&#8594;</div>

    <div class="step">
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

    <h2>Pilih Layanan</h2>
    <p class="sub-title">
        Pilih poli/layanan yang Anda butuhkan
    </p>

    <div class="dokter-grid">

<?php while($d = mysqli_fetch_assoc($query)){ ?>

<div class="card">

    <?php
    if($d['foto']==""){
        $foto="uploads/dokter/default.png";
    }else{
        $foto="../admin/uploads/dokter/".$d['foto'];
    }
    ?>

    <img src="<?= $foto ?>">

    <span class="status available">
        Bersedia
    </span>

    <div class="card-body">

        <h4><?= $d['nama_dokter']; ?></h4>

        <p><?= $d['nama_poli']; ?></p>

       <a href="proses_pilih_dokter.php?id_dokter=<?= $d['id_dokter']; ?>" class="btn-pilih">
            Pilih
        </a>

    </div>

</div>

<?php } ?>

</div>

</div>

<div class="chat-button">
    <i class="fas fa-headset"></i>
</div>
    


</div>

</body>
</html>