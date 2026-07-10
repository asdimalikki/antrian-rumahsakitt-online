<?php
include "koneksi.php";

if (!isset($_GET['id_dokter']) || !isset($_GET['tanggal'])) {
    exit("Parameter tidak lengkap.");
}

$id_dokter = (int)$_GET['id_dokter'];
$tanggal   = mysqli_real_escape_string($conn, $_GET['tanggal']);

$query = mysqli_query($conn, "
SELECT
    id_jadwal,
    jam_mulai,
    jam_selesai
FROM jadwal
WHERE id_dokter = '$id_dokter'
AND tanggal = '$tanggal'
ORDER BY jam_mulai
");

$jadwalTersedia = [];

while ($row = mysqli_fetch_assoc($query)) {

    $jamMulai = date('H:i', strtotime($row['jam_mulai']));
    $jamSelesai = date('H:i', strtotime($row['jam_selesai']));

    $jadwalTersedia[$jamMulai] = [
        'id_jadwal'   => $row['id_jadwal'],
        'jam_selesai' => $jamSelesai
    ];
}

/*
|--------------------------------------------------------------------------
| Semua jam yang ingin ditampilkan
|--------------------------------------------------------------------------
*/
$semuaJam = [
    "08:00",
    "09:00",
    "10:00",
    "11:00",
    "12:00",
    "13:00",
    "14:00",
    "15:00",
    "16:00",
    "17:00"
];

foreach ($semuaJam as $jam) {

    if (isset($jadwalTersedia[$jam])) {

        $idJadwal   = $jadwalTersedia[$jam]['id_jadwal'];
        $jamSelesai = $jadwalTersedia[$jam]['jam_selesai'];
        ?>

        <button
            type="button"
            class="tersedia"
            onclick="pilihJam(
                <?= $idJadwal ?>,
                '<?= $jam ?>',
                '<?= $jamSelesai ?>'
            )">
            <?= $jam ?>
        </button>

        <?php

    } else {

        ?>

        <button
            type="button"
            class="tidak-tersedia"
            disabled>
            <?= $jam ?>
        </button>

        <?php
    }
}
?>