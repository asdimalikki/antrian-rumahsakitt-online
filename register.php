<html>
<head>
    <title>Register Akun Pasien</title>

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
            color: #16aeb9;
            transform: scale(1.1); 
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

.login-container{
    width:100%;
    padding:50px;
    display:flex;
    justify-content:center;
    align-items:flex-start;
    gap:60px;
    background:#f5f7fa;
    flex-wrap:wrap;
}

.login-info{
    width:600px;
    margin-top:0px;
}

.login-info p:first-child{
    color:#555;
    margin-bottom:15px;
}

.login-info h2{
    color:#0d3ca7;
    font-size:32px;
    line-height:40px;
    margin-bottom:10px;
}

.login-info .desc{
    color:#666;
    font-size:14px;
    margin-bottom:30px;
}

.info-item{
    display:flex;
    gap:15px;
    margin-bottom:25px;
}

.info-icon{
    width:45px;
    height:45px;
    border:2px solid #0d3ca7;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    color:#0d3ca7;
    font-size:20px;
}

.info-text h4{
    font-size:16px;
    color:#222;
}

.info-text p{
    font-size:13px;
    color:#666;
}

/* Bagian kanan */
.login-box{
    width:450px;
    background:#fff;
    padding:35px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,0.08);
}

.login-box h2{
    color:#222;
    margin-bottom:5px;
}

.login-box .sub{
    color:#666;
    font-size:13px;
    margin-bottom:25px;
}

.form-group{
    margin-bottom:20px;
}

.form-group label{
    display:block;
    margin-bottom:8px;
    font-weight:500;
}

.input-box{
    position:relative;
}

.input-box i{
    position:absolute;
    top:15px;
    left:15px;
    color:#666;
}

.input-box input{
    width:100%;
    padding:12px 15px 12px 45px;
    border:1px solid #cfcfcf;
    border-radius:5px;
    outline:none;
}

.remember{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
    font-size:14px;
}

.remember a{
    text-decoration:none;
    color: #2d57a3;
}

.remember a:hover{
    text-decoration:underline;
}

.remember a:focus{
    text-decoration:underline;
}

.login-submit{
    width:100%;
    padding:12px;
    background:#081b8f;
    color:#fff;
    border:none;
    border-radius:5px;
    cursor:pointer;
    font-size:16px;
}
.login-submit:hover{
        background: #16aeb9;
    } 

.syarat{
    margin-bottom:20px;
    font-size:14px;
    color:#666;
}

.syarat label{
    display:flex;
    align-items:center;
    gap:8px;
    font-weight:400;
    cursor:pointer;
    white-space:nowrap;
}

.syarat input[type="checkbox"]{
    flex-shrink:0;
    cursor:pointer;
}

.syarat a{
    color:#081b8f;
    font-weight:600;
    text-decoration:none;
}

.syarat a:hover{
    text-decoration:underline;
}

.pesan-galat{
    background:#ffe3e6;
    color:#ff3d5a;
    padding:12px 16px;
    border-radius:6px;
    margin-bottom:18px;
    font-size:13px;
}

.register{
    margin-top:15px;
    font-size:14px;
    color:#666;
}
.register a{
    text-decoration:none;
    color:#081b8f;
    font-weight:600;
    margin-left:5px;
}

.register a:hover{
    text-decoration:underline;
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

    </style>
</head>
<body>

<div class="container">


  <div class="login-container">

    <div class="login-info">
          <p>Selamat Datang</p>

          <h2>
              Sistem Antrian Online
              <br>
              Sequenutra
              <br>
              Health
          </h2>

          <p class="desc">
              Buat akun untuk mulai mengambil nomor antrian,
              melihat jadwal dokter, dan mengelola reservasi anda.
          </p>

          <div class="info-item">
              <div class="info-icon">
                  <i class="fas fa-clock"></i>
              </div>
              <div class="info-text">
                  <h4>Hemat Waktu</h4>
                  <p>Daftar antrian tanpa perlu lama.</p>
              </div>
          </div>

          <div class="info-item">
              <div class="info-icon">
                  <i class="fas fa-calendar-check"></i>
              </div>
              <div class="info-text">
                  <h4>Jadwal Fleksibel</h4>
                  <p>Pilih jadwal yang paling sesuai.</p>
              </div>
          </div>

          <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-lock"></i>
            </div>
            <div class="info-text">
                <h4>Aman & Terpercaya</h4>
                <p>Data Anda terlindungi dengan aman.</p>
            </div>
          </div>
    </div> 
            <div class="login-box">
    <h2>Buat Akun Baru</h2>
    <p class="sub">
        Silakan lengkapi data diri Anda untuk mendaftar
    </p>

    <form action="cekregister.php" method="POST">

        <div class="form-group">
            <label>Nama Lengkap</label>

            <div class="input-box">
                <i class="fas fa-user"></i>
                <input type="text"
                       name="nama"
                       placeholder="Masukkan nama lengkap Anda"
                       required>
            </div>
        </div>

        <div class="form-group">
            <label>Email</label>

            <div class="input-box">
                <i class="fas fa-envelope"></i>
                <input type="email"
                       name="email"
                       placeholder="Masukkan email Anda"
                       required>
            </div>
        </div>

        <div class="form-group">
            <label>Nomor Telepon</label>

            <div class="input-box">
                <i class="fas fa-phone"></i>
                <input type="tel"
                    name="no_telepon"
                    placeholder="Masukkan nomor telepon Anda"
                    pattern="[0-9]{10,15}"
                    inputmode="numeric"
                    required>
            </div>
             <small id="notif-telepon" style="color:#ff3d5a; display:none; margin-top:5px;">
                Nomor telepon hanya boleh berisi angka.
            </small>
        </div>

        <div class="form-group">
            <label>Password</label>

            <div class="input-box">
                <i class="fas fa-lock"></i>
                <input type="password"
                       name="password"
                       placeholder="Masukkan password Anda"
                       required>
            </div>
        </div>

        <div class="form-group">
            <label>Konfirmasi Password</label>

            <div class="input-box">
                <i class="fas fa-lock"></i>
                <input type="password"
                       name="konfirmasi_password"
                       placeholder="Ulangi password Anda"
                       required>
            </div>
        </div>

        <div class="syarat">
            <label>
                <input type="checkbox" name="setuju" required>
                Saya setuju dengan
                <a href="syarat_ketentuan.php" target="_blank">Syarat &amp; Ketentuan</a>
            </label>
        </div>

        <button type="submit" class="login-submit">
            Daftar
        </button>

    </form>

    <div class="register">
        Sudah punya akun?
        <a href="index.php">
            Login
        </a>
    </div>
</div>

          <div class="chat-button">
            <i class="fas fa-headset"></i>
          </div>
</div>

</div>

<script>
    const noTelepon = document.querySelector('input[name="no_telepon"]');
const notifTelepon = document.getElementById('notif-telepon');

noTelepon.addEventListener('input', function(e){
    const hasHuruf = /[^0-9]/.test(this.value);

    if(hasHuruf){
        notifTelepon.style.display = 'block';
    } else {
        notifTelepon.style.display = 'none';
    }

    // Bersihkan karakter selain angka
    this.value = this.value.replace(/[^0-9]/g, '');
});

noTelepon.addEventListener('paste', function(e){
    e.preventDefault();
    let text = (e.clipboardData || window.clipboardData).getData('text');
    const bersih = text.replace(/[^0-9]/g, '');
    this.value += bersih;

    if(text !== bersih){
        notifTelepon.style.display = 'block';
    }
});

    document.querySelector('form').addEventListener('submit', function(e){
        const password = this.querySelector('input[name="password"]').value;
        const konfirmasi = this.querySelector('input[name="konfirmasi_password"]').value;

        if(password !== konfirmasi){
            e.preventDefault();
            alert('Password dan Konfirmasi Password tidak sama. Silakan periksa kembali.');
        }

    });
</script>

</body>
</html>