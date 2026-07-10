<html>
<head>
    <title>Dashboard Antrian Online Rumah Sakit</title>

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
    overflow-x:hidden;
    overflow-y:auto;
    background:#f8fafc;
    }
    
    .container{
    width:100%;
    min-height:100vh;
    display:flex;
    flex-direction:column;
    }

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

    .nav-item a{
        text-decoration:none;
        color:#3174b5;
        font-weight:500;
        position:relative;
    }

    .nav-item.active a::after{
        content:'';
        position:absolute;
        left:0;
        bottom:-8px;
        width:100%;
        height:2px;
        background:#3174b5;
    }

    .nav-item a:hover{
        color:#16aeb9;
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

    .notif-icon:hover i {
            color: #16335c;
            transform: scale(1.1); 
    }

    .login-btn{
        text-decoration:none;
        color:#16aeb9;
        border:2px solid #16335c;
        padding:8px 25px;
        border-radius:8px;
        font-weight:600;
    }

    .login-btn:hover{
        background: #16aeb9;
        color:#fff;
    }
    .hero{
    height:380px;
    display:flex;
    background:linear-gradient(
        90deg,
        #16335c 0%,
        #16335c 45%,
        #16335c 100%
    );
}

    .hero-image{
        width:45%;
        height: 380px;
        position:relative;
        overflow:hidden;
    }

    .hero-image img{
        position:absolute;
        left:-270px;
        bottom:0;
        height:140%;
    }

    .hero-image::after{
        content:'';
        position:absolute;
        top:0;
        right:0;
        width:190px;
        height:200%;
        background:linear-gradient(
            to right,
            rgba(255,255,255,0),
            rgba(72,111,183,.2),
            #16335c
            #16335c
            );
    }

        /* Efek putih lembut */
    .hero-image::before{
        content: "";
        position: absolute;
        inset: 0;
         background: linear-gradient(
            to right,
            rgba(255,255,255,0.35),
            rgba(255,255,255,0.15),
            transparent
            );
    }

    .hero-content{
        width:55%;
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
        text-align:center;
        color:white;
        padding:30px;
        
        
    }

    .hero-content h1{
        font-size:45px;
        letter-spacing: 2px;
        margin-bottom:30px;
        font-family:"Times New Roman", serif;
        text-shadow:
        0 0 10px rgba(255,255,255,0.3),
        0 0 25px rgba(255,255,255,0.2);
    }

    .hero-content p{
        font-size: 28px;
        line-height:1.5;
        max-width:500px;
        
    }

    .btn-antrian{
        display: inline-block;
        margin-top:35px;
        padding: 16px 40px;
        background : #5584da; 
        color: #ffffff;
        text-decoration:none;
        border-radius:14px;
        font-size: 18px;
        font-weight:600;
        transition:.3s;
        box-shadow:
        0 8px 20px rgba(0,0,0,0.15);
    }

    .btn-antrian:hover{
        background: #183699;
        transform: translateY(-3px);
    }   
    
    .fitur{
        background:#e9e9e9;
        display:grid;
        grid-template-columns:repeat(4,1fr);
        gap:20px;
        padding:20px 85px;
    }

    .fitur-item{
        text-align:center;
    }

    .icon-fitur{
        width:55px;
        height:55px;
        border-radius:50%;
        background:#dbe2ff;
        margin:auto;
        margin-bottom:10px;
        display:flex;
        justify-content:center;
        align-items:center;
        font-size:24px;
    }

    .fitur-item h3{
        font-size:18px;
        margin-bottom:5px;
    }

    .fitur-item p{
        color:#666;
        font-size:13px;
    }

    .layanan {
        padding: 55px 10px;
        background-color: #f7f9fc; 
        text-align: center;
        font-family: 'Poppins', Arial, sans-serif;
    }

    .layanan h2 {
        font-size: 32px;
        font-weight: 700;
        color: #16335c; 
        margin-bottom: 45px;
    }

    .layanan-container {
        display: grid;
        grid-template-columns: repeat(3, 2fr);
        gap: 25px;
        max-width: 1100px;
        margin: 0 auto;
    }

    .layanan-item {
        background-color: #ffffff;
        border-radius: 14px;
        padding: 30px 15px;
        box-shadow: 0 2px 10px rgba(20, 50, 90, 0.06);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .layanan-item:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 20px rgba(20, 50, 90, 0.12);
    }

    .icon-layanan {
        width: 70px;
        height: 70px;
        margin: 0 auto 16px auto;
        background-color: #e8f1fb; 
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-layanan img {
        width: 34px;
        height: 34px;
        object-fit: contain;
    }

    .layanan-item p {
        font-size: 14px;
        font-weight: 600;
        color: #16335c;
        margin: 0;
        margin-bottom :10px;
    }
    .layanan-item span{
        display:block;
        font-size:13px;
        color:#4a5568;
        line-height:1.5;
    }
    .informasi{
        padding:80px 60px;
        background:#ffffff;
        scroll-margin-top:40px;
        text-align:center;
        margin-top : 10px;
    }

    .informasi h2{
        font-size:32px;
        font-weight:700;
        color:#16335c;
        margin-bottom:60px;
    }

    .informasi-top{
        display:flex;
        align-items:center;
        gap:60px;
        max-width:1200px;
        margin:0 auto 70px auto;
        text-align:left;
    }

    .informasi-stats{
        flex:1;
        position:relative;
        overflow:hidden;
        background:linear-gradient(135deg,#486fb7,#2d57a3);
        border-radius:18px;
        padding:40px 35px;
        box-shadow:0 15px 35px rgba(45,87,163,0.3);
    }

    .informasi-stats::before{
        content:'';
        position:absolute;
        top:-60px;
        right:-60px;
        width:180px;
        height:180px;
        border-radius:50%;
        background:rgba(255,255,255,0.06);
    }

    .informasi-stats::after{
        content:'';
        position:absolute;
        bottom:-50px;
        left:-50px;
        width:140px;
        height:140px;
        border-radius:50%;
        background:rgba(255,255,255,0.05);
    }

    .stats-header{
        display:flex;
        align-items:center;
        gap:10px;
        margin-bottom:28px;
        position:relative;
        z-index:1;
    }

    .stats-header-icon{
        width:34px;
        height:34px;
        border-radius:50%;
        background:rgba(255,255,255,0.15);
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff;
        font-size:14px;
    }

    .stats-header span{
        color:#dbe6f7;
        font-size:13px;
        font-weight:600;
        letter-spacing:0.3px;
    }

    .stats-grid{
        display:grid;
        grid-template-columns:repeat(2,1fr);
        position:relative;
        z-index:1;
    }

    .stat-item{
        padding:18px 10px;
        border-right:1px solid rgba(255,255,255,0.15);
        border-bottom:1px solid rgba(255,255,255,0.15);
    }

    .stat-item:nth-child(2){
        border-right:none;
    }

    .stat-item:nth-child(3),
    .stat-item:nth-child(4){
        border-bottom:none;
    }

    .stat-item:nth-child(4){
        border-right:none;
    }

    .stat-icon{
        width:34px;
        height:34px;
        border-radius:50%;
        background:rgba(255,255,255,0.15);
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff;
        font-size:14px;
        margin-bottom:12px;
    }

    .stat-item h3{
        color:#fff;
        font-size:30px;
        font-weight:700;
        margin-bottom:4px;
    }

    .stat-item p{
        color:#dbe6f7;
        font-size:13px;
        font-weight:500;
    }

    .informasi-text{
        flex:1;
    }

    .informasi-label{
        display:inline-block;
        background:#e6f1fb;
        color:#185fa5;
        font-size:13px;
        font-weight:600;
        padding:6px 16px;
        border-radius:20px;
        margin-bottom:18px;
    }

    .informasi-text h3{
        font-size:28px;
        color:#16335c;
        font-weight:700;
        margin-bottom:16px;
        line-height:1.3;
    }

    .informasi-text p{
        color:#5b6b85;
        font-size:15px;
        line-height:1.7;
        margin-bottom:26px;
    }

    .informasi-checklist{
        list-style:none;
    }

    .informasi-checklist li{
        display:flex;
        align-items:flex-start;
        gap:12px;
        margin-bottom:16px;
        font-size:14px;
        color:#16335c;
        font-weight:500;
    }

    .informasi-checklist i{
        background:#16aeb9;
        color:#fff;
        width:20px;
        height:20px;
        border-radius:50%;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:10px;
        margin-top:2px;
        flex-shrink:0;
    }

    .informasi-strip{
        display:grid;
        grid-template-columns:repeat(3,1fr);
        gap:25px;
        max-width:1100px;
        margin:0 auto;
    }

    .informasi-card{
        background:#f7f9fc;
        border-radius:14px;
        padding:32px 22px;
        text-align:center;
        border:1px solid #e3e8f0;
        transition:transform 0.25s ease, box-shadow 0.25s ease;
    }

    .informasi-card:hover{
        transform:translateY(-6px);
        box-shadow:0 8px 20px rgba(20,50,90,0.1);
    }

    .informasi-icon{
        width:55px;
        height:55px;
        border-radius:50%;
        background:#16335c;
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        margin:0 auto 16px auto;
        font-size:20px;
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
        box-shadow:0 5px 15px rgba(0,0,0,0.3);
        cursor:pointer;
        z-index:1000;
    }

    .chat-button:hover{
        background:#16aeb9;
    }
    footer {
    background: #16335c;
    color: #cdd8ea;
    padding: 100px 60px 0 100px;
}

.footer-grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr 1fr 1.6fr;
    gap: 40px;
    padding-bottom: 40px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.footer-col h4 {
    color: #ffffff;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 22px;
    padding-bottom: 10px;
    display: inline-block;
    position: relative;
}

.footer-col h4::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 32px;
    height: 2px;
    background: #16aeb9;
    border-radius: 2px;
}

.footer-info-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 14px;
    font-size: 13.5px;
    line-height: 1.5;
}

.footer-info-item i {
    color: #16aeb9;
    font-size: 16px;
    margin-top: 2px;
    flex-shrink: 0;
}

.footer-socials {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.footer-social-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #cdd8ea;
    font-size: 15px;
    text-decoration: none;
    transition: background 0.2s, border-color 0.2s;
}

.footer-social-btn:hover {
    background: #16aeb9;
    border-color: #16aeb9;
    color: #fff;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 12px;
}

.footer-links li a {
    color: #cdd8ea;
    text-decoration: none;
    font-size: 13.5px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: color 0.2s, padding-left 0.2s;
}

.footer-links li a:hover {
    color: #16aeb9;
    padding-left: 4px;
}

.footer-links li a i {
    color: #16aeb9;
    font-size: 11px;
}

.footer-map {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.12);
}

.footer-map iframe {
    width: 100%;
    height: 200px;
    display: block;
    border: none;
}

.footer-map-label {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-top: 10px;
    font-size: 12.5px;
    color: #9ab0c8;
}

.footer-map-label i {
    color: #16aeb9;
    font-size: 14px;
}
     </style>
</head>
<body>

<div class="container">

    <header class="header-container">
        <nav class="navbar">

            <div class="logo-container">
                <img src="https://i.imgur.com/0Olben8.png" class="logo-img">
                <div class="logo-text">
                    <span class="logo-name">Sequentra</span>
                    <span class="logo-brand">Health</span>
                </div>
            </div>

            <ul class="nav-links">
                <li class="nav-item active"><a href="dashboard.php">Home</a></li>
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

    <div class="hero">

        <div class="hero-image">
            <img src="https://i.imgur.com/iZjMwnl.png">
        </div>

        <div class="hero-content">
            <h1>SEQUENTRA HEALTH</h1>

            <p>
                Solusi digital untuk mengatur antrian pasien
                dengan lebih mudah, cepat, dan efisien.
            </p>

            <a href="buat_antrian.php" class="btn-antrian">
                Buat Antrian Sekarang
            </a>
        </div>

    </div>

    <div class="fitur">

        <div class="fitur-item">
            <div class="icon-fitur">⏰</div>
            <h3>Hemat Waktu</h3>
            <p>Daftar dari mana saja dan kapan saja.</p>
        </div>

        <div class="fitur-item">
            <div class="icon-fitur">👥</div>
            <h3>Tanpa Antri Panjang</h3>
            <p>Datang sesuai jadwal antrian Anda.</p>
        </div>

        <div class="fitur-item">
            <div class="icon-fitur">📅</div>
            <h3>Jadwal Fleksibel</h3>
            <p>Pilih waktu kunjungan yang sesuai kebutuhan.</p>
        </div>

        <div class="fitur-item">
            <div class="icon-fitur">🔒</div>
            <h3>Aman & Terpercaya</h3>
            <p>Data pasien terlindungi dengan baik.</p>
        </div>

    </div>

    <div class="layanan" id="layanan">

    <h2>Layanan Unggulan</h2>

    <div class="layanan-container">

        <div class="layanan-item">
            <div class="icon-layanan">
                <img src="https://cdn-icons-png.flaticon.com/512/2966/2966488.png">
            </div>
            <p>Poli Umum</p>
            <span>Ngobrol langsung sama dokter buat tanya soal kesehatan kamu, gampang dan nyaman.</span>
        </div>

        <div class="layanan-item">
            <div class="icon-layanan">
                <img src="https://cdn-icons-png.flaticon.com/512/387/387561.png">
            </div>
            <p>Poli Spesialis</p>
            <span>Dokter  khusus di rumah sakit yang menangani keluhan kesehatan secara lebih mendalam.</span>
        </div>

        <div class="layanan-item">
            <div class="icon-layanan">
                <img src="https://cdn-icons-png.flaticon.com/512/2966/2966327.png">
            </div>
            <p>Poli Gigi</p>
            <span>kita gak cuma benerin gigi yang sakit, tapi juga siap bikin senyum kamu makin glowing.</span>
        </div>

        <div class="layanan-item">
            <div class="icon-layanan">
                <img src="https://cdn-icons-png.flaticon.com/512/4341/4341139.png">
            </div>
            <p>Kesehatan Anak</p>
           <span>kita gak cuma benerin gigi yang sakit, tapi juga siap bikin senyum kamu makin glowing.</span>
        </div>

        <div class="layanan-item">
            <div class="icon-layanan">
                <img src="https://cdn-icons-png.flaticon.com/512/2785/2785482.png">
            </div>
            <p>Vaksinasi</p>
            <span>khusus di rumah sakit yang menangani keluhan kesehatan secara lebih mendalam sesuai bidang organ tubuh.</span>
        </div>

        <div class="layanan-item">
            <div class="icon-layanan">
                <img src="https://cdn-icons-png.flaticon.com/512/2966/2966481.png">
            </div>
            <p>Medical Check Up</p>
            <span>Pengecekan kesehatan rutin biar kondisi badan kamu selalu terpantau dengan baik.</span>
        </div>

        </div>
</div>

    <div class="informasi" id="informasi">

        <h2>Informasi Penting</h2>

        <div class="informasi-top">

            <div class="informasi-stats">

                <div class="stats-header">
                    <div class="stats-header-icon"></div>
                    <span>SEQUENTRA HEALTH INFORMATIONS</span>
                </div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-user-injured"></i></div>
                        <h3>10.000+</h3>
                        <p>Pasien terlayani</p>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-user-doctor"></i></div>
                        <h3>50+</h3>
                        <p>Dokter spesialis</p>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-truck-medical"></i></div>
                        <h3>24/7</h3>
                        <p>Layanan darurat</p>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                        <h3>15+</h3>
                        <p>Tahun beroperasi</p>
                    </div>
                </div>

            </div>

            <div class="informasi-text">
                <h3>Apa yang perlu Anda siapkan?</h3>
                <p>Supaya proses antrian berjalan lancar, pastikan dokumen berikut sudah Anda siapkan sebelum datang ke rumah sakit.</p>

                <ul class="informasi-checklist">
                    <li><i class="fas fa-check"></i> KTP atau identitas resmi yang masih berlaku</li>
                    <li><i class="fas fa-check"></i> Riwayat rekam medis sebelumnya (opsional)</li>
                    <li><i class="fas fa-check"></i> Nomor antrian yang sudah didaftarkan secara online</li>
                </ul>
            </div>

        </div>

        <div class="informasi-strip">

            <div class="informasi-card">
                <div class="informasi-icon"><i class="fas fa-clock"></i></div>
                <h3>Jam Operasional</h3>
                <p>Senin - Jumat, 07.00 - 21.00 WIB</p>
            </div>

            <div class="informasi-card">
                <div class="informasi-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h3>Lokasi</h3>
                <p>Jl. Kesehatan No. 10, Jakarta</p>
            </div>

            <div class="informasi-card">
                <div class="informasi-icon"><i class="fas fa-phone-alt"></i></div>
                <h3>Kontak Darurat</h3>
                <p>(021) 123-4567 / 0858-5221-1145</p>
            </div>

        </div>

    </div>


</div>

     <div class="chat-button">
        <i class="fas fa-headset"></i>
    </div>

</div>

    <footer>
    <div class="footer-grid" id="kontak">

        <!-- Alamat -->
        <div class="footer-col">
            <h4>Alamat</h4>
            <div class="footer-info-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>Jl. Kesehatan No. 10, Jakarta Pusat, DKI Jakarta, 10110</span>
            </div>
            <div class="footer-info-item">
                <i class="fas fa-phone-alt"></i>
                <span>(021) 123-4567</span>
            </div>
            <div class="footer-info-item">
                <i class="fas fa-mobile-alt"></i>
                <span>0812-3456-7890</span>
            </div>
            <div class="footer-info-item">
                <i class="fas fa-envelope"></i>
                <span>info@sequentrahealth.id</span>
            </div>
            <div class="footer-socials">
                <a href="#" class="footer-social-btn"><i class="fab fa-twitter"></i></a>
                <a href="#" class="footer-social-btn"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="footer-social-btn"><i class="fab fa-youtube"></i></a>
                <a href="#" class="footer-social-btn"><i class="fab fa-instagram"></i></a>
            </div>
        </div>

        <!-- Layanan -->
        <div class="footer-col">
            <h4>Layanan</h4>
            <ul class="footer-links">
                <li><a href="#"><i class="fas fa-angle-right"></i>Poli Umum</a></li>
                <li><a href="#"><i class="fas fa-angle-right"></i>Poli Spesialis</a></li>
                <li><a href="#"><i class="fas fa-angle-right"></i>Poli Gigi</a></li>
                <li><a href="#"><i class="fas fa-angle-right"></i>Kesehatan Anak</a></li>
                <li><a href="#"><i class="fas fa-angle-right"></i>Vaksinasi</a></li>
                <li><a href="#"><i class="fas fa-angle-right"></i>Medical Check Up</a></li>
            </ul>
        </div>

        <!-- Tautan Cepat -->
        <div class="footer-col">
            <h4>Tautan Cepat</h4>
            <ul class="footer-links">
                <li><a href="#"><i class="fas fa-angle-right"></i>Tentang Kami</a></li>
                <li><a href="#"><i class="fas fa-angle-right"></i>Hubungi Kami</a></li>
                <li><a href="#"><i class="fas fa-angle-right"></i>Layanan Kami</a></li>
                <li><a href="#"><i class="fas fa-angle-right"></i>Syarat & Ketentuan</a></li>
                <li><a href="#"><i class="fas fa-angle-right"></i>Kebijakan Privasi</a></li>
                <li><a href="#"><i class="fas fa-angle-right"></i>Bantuan</a></li>
            </ul>
        </div>

        <!-- Lokasi / Maps -->
        <div class="footer-col">
            <h4>Lokasi Kami</h4>
            <div class="footer-map">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322728!2d106.82495!3d-6.194702!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5d2e764b12d%3A0x3d2ad6e1e0e9bcc8!2sJakarta%20Pusat!5e0!3m2!1sid!2sid!4v1234567890"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Lokasi Sequentra Health">
                </iframe>
            </div>
            <div class="footer-map-label">
                <i class="fas fa-map-marker-alt"></i>
                <span>Jl. Kesehatan No. 10, Jakarta Pusat</span>
            </div>
        </div>

    </div>

</footer>


<script>
const menuItems = document.querySelectorAll('.nav-item');

menuItems.forEach(item=>{
    item.addEventListener('click',function(){
        document.querySelector('.nav-item.active')
        ?.classList.remove('active');

        this.classList.add('active');
    });
});

</script>

</body>
</html>