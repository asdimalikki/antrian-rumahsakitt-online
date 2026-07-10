<?php
session_start();
include "koneksi.php";

$id_user = intval($_SESSION['id_user']);

/* =====================================================
   1. Ambil SEMUA pasien milik akun ini (bukan cuma 1)
   ===================================================== */
$query_list = mysqli_query($conn, "
    SELECT id_pasien, nama_lengkap, nik
    FROM pasien
    WHERE id_user = '$id_user'
    ORDER BY nama_lengkap ASC
");

$list_pasien = [];
while ($row = mysqli_fetch_assoc($query_list)) {
    $list_pasien[] = $row;
}

/* =====================================================
   2. Tentukan pasien mana yang sedang aktif/dipilih
      - ?pasien=baru      -> form kosong (tambah pasien baru)
      - ?pasien=<id>       -> tampilkan data pasien tsb
      - tidak ada parameter -> default ke pasien pertama
        (kalau belum ada pasien sama sekali -> otomatis mode baru)
   ===================================================== */
$mode_tambah   = false;
$selected_id   = isset($_GET['pasien']) ? $_GET['pasien'] : null;

if ($selected_id === 'baru' || count($list_pasien) === 0) {
    $mode_tambah = true;
    $pasien = [
        'id_pasien'      => '',
        'nama_lengkap'   => '',
        'nik'            => '',
        'no_hp'          => '',
        'no_bpjs'        => '',
        'jenis_pasien'   => 'umum',
        'alamat'         => '',
        'tanggal_lahir'  => '',
        'jenis_kelamin'  => '',
        'email'          => ''
    ];
} else {
    // kalau tidak ada pilihan eksplisit, pakai pasien pertama di daftar
    $id_pasien_aktif = $selected_id !== null ? intval($selected_id) : intval($list_pasien[0]['id_pasien']);

    // pastikan pasien yang diminta benar-benar milik akun ini (keamanan)
    $query = mysqli_query($conn, "
        SELECT *
        FROM pasien
        WHERE id_pasien = '$id_pasien_aktif' AND id_user = '$id_user'
    ");
    $pasien = mysqli_fetch_assoc($query);

    // fallback kalau ternyata tidak ketemu / bukan miliknya
    if (!$pasien) {
        $mode_tambah = true;
        $pasien = [
            'id_pasien' => '', 'nama_lengkap' => '', 'nik' => '',
            'no_hp' => '', 'no_bpjs' => '', 'jenis_pasien' => 'umum',
            'alamat' => '', 'tanggal_lahir' => '', 'jenis_kelamin' => '',
            'email' => ''
        ];
    }
}

// Pastikan key jenis_pasien & no_bpjs selalu ada walau kolomnya NULL di DB
$pasien['no_bpjs']      = $pasien['no_bpjs'] ?? '';
$pasien['jenis_pasien'] = $pasien['jenis_pasien'] ?? 'umum';
?>
<html>
<head>
    <title>Buat Antrian</title>

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
.form-antrian{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:40px;
    margin-top:30px;
}

.card-pasien{
    flex:1;
    max-width:720px;
    background:#fff;
    border:1px solid #e9edf3;
    border-radius:14px;
    padding:25px;
}

.card-pasien h3{
    color:#1d3557;
    font-size:18px;
    width:60%;
    margin-bottom:25px;
}

/* ===== Pemilih Pasien ===== */
.pilih-pasien-box{
    background:#fff;
    border:1px solid #e9edf3;
    border-radius:14px;
    padding:20px 25px;
    margin-bottom:20px;
    display:flex;
    align-items:flex-end;
    gap:15px;
    flex-wrap:wrap;
}

.pilih-pasien-box .form-group{
    margin-bottom:0;
    flex:1;
    min-width:220px;
}

.btn-tambah-pasien{
    height:48px;
    padding:0 22px;
    border:2px solid #0a1d8f;
    background:#fff;
    color:#0a1d8f;
    border-radius:8px;
    font-size:14px;
    font-weight:600;
    cursor:pointer;
    white-space:nowrap;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    gap:8px;
}

.btn-tambah-pasien:hover{
    background:#0a1d8f;
    color:#fff;
}

.badge-mode-baru{
    display:inline-block;
    background:#eef4ff;
    color:#0a5cff;
    font-size:12px;
    font-weight:600;
    padding:4px 12px;
    border-radius:20px;
    margin-bottom:15px;
}

.form-group{
    margin-bottom:28px;
}

.form-group label{
    display:block;
    margin-bottom:8px;
    color:#4b5563;
    font-size:14px;
    font-weight:500;
}

.form-group input,
.form-group select{
    width:100%;
    height:48px;
    border:1px solid #e5e7eb;
    border-radius:8px;
    padding:0 15px;
    font-size:14px;
    outline:none;
    background:#fff;
}

.form-group input:focus,
.form-group select:focus{
    border:1px solid #2563eb;
}

.nik-box,
.tanggal-box{
    position:relative;
}

.nik-box span{
    position:absolute;
    right:15px;
    top:13px;
    color:#9ca3af;
    font-size:13px;
}

.tanggal-box i{
    position:absolute;
    right:15px;
    top:15px;
    color:#9ca3af;
}

.radio-group{
    display:flex;
    gap:40px;
    margin-top:10px;
}

.radio{
    display:flex;
    align-items:center;
    gap:8px;
    font-size:14px;
    color:#555;
}

.radio input{
    width:18px;
    height:18px;
}

.checkbox{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:13px;
    color:#666;
    margin-top:10px;
    margin-bottom:20px;
}

.checkbox input{
    width:16px;
    height:16px;
    accent-color:#0a1d8f;
}

.btn-lanjut{
    width:100%;
    height:48px;
    border:none;
    background: #486fb7 45%;
    color:#fff;
    border-radius:8px;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
}

.btn-lanjut:hover{
    background: #16aeb9;
}

/* ================= KANAN ================= */

.info-kanan{
    width:380px;
    flex-shrink:0;
    display:flex;
    flex-direction:column;
    gap:20px;
}

.card-keamanan,
.card-keunggulan{
    background:#fff;
    border-radius:18px;
    padding:25px;
    box-shadow:0 10px 30px rgba(0,0,0,0.06);
}

.judul-card{
    display:flex;
    align-items:center;
    gap:10px;
    margin-bottom:15px;
}

.judul-card i{
    color:#16a34a;
    font-size:22px;
}

.judul-card h3{
    color:#1d3557;
}

.card-keamanan p{
    color:#666;
    line-height:28px;
}

.card-statistik{
    display:flex;
    gap:15px;
}

.box-stat{
    flex:1;
    background:#fff;
    border-radius:18px;
    padding:10px;
    text-align:center;
    box-shadow:0 10px 30px rgba(0,0,0,0.06);
}

.box-stat i{
    font-size:25px;
    color:#0a5cff;
    margin-bottom:15px;
}

.box-stat h2{
    color:#0a5cff;
    margin-bottom:10px;
}

.box-stat p{
    color:#666;
    font-size:14px;
}

.card-keunggulan h3{
    color:#1d3557;
    margin-bottom:20px;
}

.card-keunggulan i{
    color: #16a34a;
}

.list-layanan p{
    margin-bottom:15px;
    color:#555;
    font-size:14px;
}

.list-layanan p i{
    margin-right:10px;
}

.card-bantuan{
    display:flex;
    align-items:center;
    gap:20px;
}

.icon-bantuan{
    width:70px;
    height:70px;
    background:#eef4ff;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
}

.icon-bantuan i{
    font-size:30px;
    color:#0a5cff;
}

.card-bantuan h3{
    color:#1d3557;
    margin-bottom:5px;
}

.card-bantuan h2{
    color:#0a5cff;
    margin-bottom:5px;
}

.card-bantuan p{
    color:#777;
    font-size:14px;
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

        <h2>Buat Antrian</h2>
        <p class="sub-title">
            Isi data diri Anda untuk melanjutkan
        </p>

        <!-- ========= PEMILIH PASIEN ========= -->
        <form action="buat_antrian.php" method="GET" class="pilih-pasien-box">
            <div class="form-group">
                <label>Pilih Pasien</label>
                <select name="pasien" onchange="this.form.submit()">
                    <?php if (count($list_pasien) === 0): ?>
                        <option value="baru" selected>Belum ada pasien tersimpan</option>
                    <?php else: ?>
                        <?php foreach ($list_pasien as $p): ?>
                            <option value="<?= $p['id_pasien']; ?>"
                                <?= (!$mode_tambah && $pasien['id_pasien'] == $p['id_pasien']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($p['nama_lengkap']); ?> — NIK <?= htmlspecialchars($p['nik']); ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="baru" <?= $mode_tambah ? 'selected' : ''; ?>>+ Tambah Pasien Baru</option>
                    <?php endif; ?>
                </select>
            </div>

            <a href="buat_antrian.php?pasien=baru" class="btn-tambah-pasien">
                <i class="fa-solid fa-plus"></i> Tambah Pasien Baru
            </a>
        </form>

        <div class="form-antrian">

    <form action="proses_pasien.php" method="POST" class="card-pasien">

        <h3>Data Pasien</h3>

        <?php if ($mode_tambah): ?>
            <span class="badge-mode-baru">Mendaftarkan pasien baru</span>
        <?php endif; ?>

        <!-- id_pasien kosong = insert pasien baru, terisi = update pasien yang sudah ada -->
        <input type="hidden" name="id_pasien" value="<?= htmlspecialchars($pasien['id_pasien']); ?>">

        <div class="form-group">
            <label>Nama Lengkap</label>
            <input
                type="text"
                name="nama"
                value="<?= htmlspecialchars($pasien['nama_lengkap']); ?>"
                placeholder="Masukkan nama lengkap pasien"
                required>
        </div>

         <div class="form-group">
            <label>Email <?= $mode_tambah ? '(Opsional)' : ''; ?></label>
            <input
                type="email"
                name="email"
                value="<?= htmlspecialchars($pasien['email'] ?? ''); ?>"
                placeholder="Masukkan email pasien"
                <?= $mode_tambah ? '' : 'readonly'; ?>>
        </div>


        <div class="form-group">
            <label>NIK</label>
            <div class="nik-box">
                <input
                type="text"
                name="nik"
                value="<?= htmlspecialchars($pasien['nik']); ?>"
                maxlength="16"
                inputmode="numeric"
                placeholder="Masukkan nomor NIK" required
                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <span>16/16</span>
            </div>
        </div>

        <div class="form-group">
            <label>No. Handphone</label>
            <input
            type="text"
            name="no_hp"
            value="<?= htmlspecialchars($pasien['no_hp']); ?>"
            maxlength="13"
            inputmode="numeric"
            pattern="[0-9]*"
            placeholder="08xxxxxxxxxx"
            required
            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        </div>

        <!-- ========= KATEGORI PASIEN (UMUM / BPJS) ========= -->
        <div class="form-group">
            <label>Kategori Pasien</label>
            <div class="radio-group">

                <label class="radio">
                    <input type="radio"
                    name="jenis_pasien"
                    value="umum"
                    onchange="toggleBpjs()"
                    <?= $pasien['jenis_pasien'] == 'umum' ? 'checked' : ''; ?>>
                    Umum
                </label>

                <label class="radio">
                    <input type="radio"
                    name="jenis_pasien"
                    value="bpjs"
                    onchange="toggleBpjs()"
                    <?= $pasien['jenis_pasien'] == 'bpjs' ? 'checked' : ''; ?>>
                    BPJS
                </label>

            </div>
        </div>

        <div class="form-group" id="bpjs-field" style="display:none;">
            <label>No. BPJS</label>
            <input
            type="text"
            name="no_bpjs"
            id="input-bpjs"
            value="<?= htmlspecialchars($pasien['no_bpjs']); ?>"
            maxlength="13"
            inputmode="numeric"
            pattern="[0-9]*"
            placeholder="Masukkan nomor BPJS"
            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        </div>

        <div class="form-group">
            <label>Alamat</label>
            <input
            type="text"
            name="alamat"
            value="<?= htmlspecialchars($pasien['alamat']); ?>"
            placeholder="Masukkan Alamat" required>
        </div>


        <div class="form-group">
            <label>Tanggal Lahir</label>

            <div class="tanggal-box">
                <input
                type="date"
                name="tanggal_lahir"
                value="<?= htmlspecialchars($pasien['tanggal_lahir']); ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Jenis Kelamin</label>

            <div class="radio-group">

                <label class="radio">
                    <input type="radio"
                    name="jk"
                    value="Laki-laki"
                    <?= $pasien['jenis_kelamin']=='Laki-laki' ? 'checked' : ''; ?>>
                    Laki-laki
                </label>

                <label class="radio">
                    <input type="radio"
                    name="jk"
                    value="Perempuan"
                    <?= $pasien['jenis_kelamin']=='Perempuan' ? 'checked' : ''; ?>>
                    Perempuan
                </label>

            </div>
        </div>


        <label class="checkbox">
            <input type="checkbox" checked>
            Simpan data ini untuk pemesanan berikutnya
        </label>

        <button class="btn-lanjut">
            Lanjutkan
        </button>

</form>

<div class="info-kanan">

    <div class="card-keamanan">
        <div class="judul-card">
            <h3>Data Anda Aman</h3>
        </div>

        <p>
            Seluruh data pasien dienkripsi dan dilindungi
            menggunakan standar keamanan rumah sakit modern.
        </p>
    </div>

    <div class="card-statistik">

        <div class="box-stat">
            <i class="fa-solid fa-user-doctor"></i>
            <h2>15+</h2>
            <p>Dokter Spesialis</p>
        </div>

        <div class="box-stat">
            <i class="fa-solid fa-clock"></i>
            <h2>24 Jam</h2>
            <p>Layanan IGD</p>
        </div>

        <div class="box-stat">
            <i class="fa-solid fa-users"></i>
            <h2>10.000+</h2>
            <p>Pasien Terlayani</p>
        </div>

    </div>

    <div class="card-keunggulan">

        <h3>
            <i class="fa-solid fa-star"></i>
            Keunggulan Layanan Kami
        </h3>

        <div class="list-layanan">
            <p>
                <i class="fa-solid fa-circle-check"></i>
                Pendaftaran Online Cepat & Mudah
            </p>

            <p>
                <i class="fa-solid fa-circle-check"></i>
                Dokter Berpengalaman & Profesional
            </p>

            <p>
                <i class="fa-solid fa-circle-check"></i>
                Antrian Real-time & Transparan
            </p>

            <p>
                <i class="fa-solid fa-circle-check"></i>
                Layanan 24 Jam Setiap Hari
            </p>

            <p>
                <i class="fa-solid fa-circle-check"></i>
                Fasilitas Modern & Nyaman
            </p>
        </div>

    </div>


</div>

</div>
    

</div>

<div class="chat-button">
    <i class="fas fa-headset"></i>
</div>


</div>

<script>
function toggleBpjs(){
    const checked = document.querySelector('input[name="jenis_pasien"]:checked');
    const isBpjs = checked && checked.value === 'bpjs';
    const bpjsField = document.getElementById('bpjs-field');
    const bpjsInput = document.getElementById('input-bpjs');

    bpjsField.style.display = isBpjs ? 'block' : 'none';
    bpjsInput.required = isBpjs;

    if (!isBpjs) {
        bpjsInput.value = '';
    }
}

// Jalankan saat halaman dimuat, supaya field BPJS otomatis tampil
// kalau pasien yang dipilih memang berkategori BPJS
window.addEventListener('DOMContentLoaded', toggleBpjs);
</script>

</body>
</html>