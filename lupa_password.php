<?php
session_start();

$flash_error   = $_SESSION['flash_error']   ?? '';
$flash_success = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_error'], $_SESSION['flash_success']);
?>
<html>
<head>
    <title>Lupa Password</title>

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

    /* Tab switcher untuk pilih metode */
    .tab-switcher{
        display:flex;
        background:#f1f4f9;
        border-radius:8px;
        padding:5px;
        margin-bottom:25px;
    }

    .tab-btn{
        flex:1;
        border:none;
        background:transparent;
        padding:10px;
        font-size:14px;
        font-weight:600;
        color:#666;
        border-radius:6px;
        cursor:pointer;
        transition:0.2s;
        font-family:'Poppins',sans-serif;
    }

    .tab-btn.active{
        background:#081b8f;
        color:#fff;
    }

    .tab-btn i{
        margin-right:6px;
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
        font-family:'Poppins',sans-serif;
        font-size:14px;
    }

    .input-box input:focus{
        border:1px solid #2563eb;
    }

    .keterangan{
        font-size:12.5px;
        color:#888;
        margin-top:10px;
        margin-bottom:20px;
        line-height:18px;
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
        font-family:'Poppins',sans-serif;
    }
    .login-submit:hover{
        background: #16aeb9;
    }

    .pesan-galat{
        background:#ffe3e6;
        color:#ff3d5a;
        padding:12px 16px;
        border-radius:6px;
        margin-bottom:18px;
        font-size:13px;
    }

    .pesan-sukses{
        background:#e0f9ec;
        color:#16a34a;
        padding:12px 16px;
        border-radius:6px;
        margin-bottom:18px;
        font-size:13px;
    }

    .register{
        margin-top:15px;
        font-size:14px;
        color:#666;
        text-align:center;
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

    .back-link{
        display:inline-flex;
        align-items:center;
        gap:6px;
        font-size:13px;
        color:#666;
        text-decoration:none;
        margin-bottom:18px;
    }

    .back-link:hover{
        color:#081b8f;
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
          <p>Lupa Password?</p>

          <h2>
              Sistem Antrian Online
              <br>
              Sequenutra
              <br>
              Health
          </h2>

          <p class="desc">
              Jangan khawatir, kami akan bantu Anda memulihkan akses ke akun
              Anda dengan cepat dan aman.
          </p>

          <div class="info-item">
              <div class="info-icon">
                  <i class="fas fa-envelope"></i>
              </div>
              <div class="info-text">
                  <h4>Verifikasi via Email</h4>
                  <p>Kami kirim link reset password ke email terdaftar.</p>
              </div>
          </div>

          <div class="info-item">
              <div class="info-icon">
                  <i class="fas fa-phone"></i>
              </div>
              <div class="info-text">
                  <h4>Verifikasi via No. HP</h4>
                  <p>Kami kirim kode OTP ke nomor telepon terdaftar.</p>
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

        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Login
        </a>

        <h2>Lupa Password</h2>
        <p class="sub">
            Pilih metode verifikasi untuk memulihkan akun Anda
        </p>

        <?php if($flash_error): ?>
            <div class="pesan-galat"><?= htmlspecialchars($flash_error) ?></div>
        <?php endif; ?>

        <?php if($flash_success): ?>
            <div class="pesan-sukses"><?= htmlspecialchars($flash_success) ?></div>
        <?php endif; ?>

        <div class="tab-switcher">
            <button type="button" class="tab-btn active" id="btn-tab-email" onclick="pilihTab('email')">
                <i class="fas fa-envelope"></i> Via Email
            </button>
            <button type="button" class="tab-btn" id="btn-tab-hp" onclick="pilihTab('hp')">
                <i class="fas fa-mobile-screen"></i> Via No. HP
            </button>
        </div>

        <form action="proses_lupa_password.php" method="POST">

            <input type="hidden" name="metode" id="metode" value="email">

            <div class="form-group" id="tab-email">
                <label>Email Terdaftar</label>
                <div class="input-box">
                    <i class="fas fa-envelope"></i>
                    <input type="email"
                           name="email"
                           id="input-email"
                           placeholder="Masukkan email Anda"
                           required>
                </div>
                <p class="keterangan">
                    Link untuk membuat password baru akan dikirim ke email ini.
                </p>
            </div>

            <div class="form-group" id="tab-hp" style="display:none;">
                <label>Nomor Telepon Terdaftar</label>
                <div class="input-box">
                    <i class="fas fa-phone"></i>
                    <input type="text"
                           name="no_hp"
                           id="input-hp"
                           placeholder="Masukkan nomor telepon Anda"
                           inputmode="numeric"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                <p class="keterangan">
                    Kode OTP 6 digit akan dikirim melalui SMS/WhatsApp ke nomor ini.
                </p>
            </div>

            <button type="submit" class="login-submit">
                Kirim Kode Verifikasi
            </button>

        </form>

        <div class="register">
            Sudah ingat password?
            <a href="index.php">Login</a>
        </div>

    </div>

  </div>

        <div class="chat-button">
            <i class="fas fa-headset"></i>
          </div>

</div>

<script>
function pilihTab(tab){
    const metode   = document.getElementById('metode');
    const btnEmail = document.getElementById('btn-tab-email');
    const btnHp    = document.getElementById('btn-tab-hp');
    const boxEmail = document.getElementById('tab-email');
    const boxHp    = document.getElementById('tab-hp');
    const inputEmail = document.getElementById('input-email');
    const inputHp     = document.getElementById('input-hp');

    if(tab === 'email'){
        metode.value = 'email';
        btnEmail.classList.add('active');
        btnHp.classList.remove('active');
        boxEmail.style.display = 'block';
        boxHp.style.display = 'none';
        inputEmail.required = true;
        inputHp.required = false;
        inputHp.value = '';
    } else {
        metode.value = 'hp';
        btnHp.classList.add('active');
        btnEmail.classList.remove('active');
        boxHp.style.display = 'block';
        boxEmail.style.display = 'none';
        inputHp.required = true;
        inputEmail.required = false;
        inputEmail.value = '';
    }
}
</script>

</body>
</html>