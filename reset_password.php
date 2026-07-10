<?php
session_start();
include "koneksi.php";

$mode       = $_POST['mode'] ?? '';
$password   = $_POST['password'] ?? '';
$konfirmasi = $_POST['konfirmasi_password'] ?? '';

if (empty($password) || $password !== $konfirmasi) {
    $_SESSION['flash_error'] = "Password dan Konfirmasi Password tidak sama.";
    header("Location: reset_password.php" . (isset($_POST['token']) ? "?token=" . urlencode($_POST['token']) : ""));
    exit;
}

if (strlen($password) < 8) {
    $_SESSION['flash_error'] = "Password minimal 8 karakter.";
    header("Location: reset_password.php" . (isset($_POST['token']) ? "?token=" . urlencode($_POST['token']) : ""));
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// ================== MODE: TOKEN EMAIL ==================
if ($mode === 'token') {

    $token = mysqli_real_escape_string($conn, $_POST['token'] ?? '');

    $cek = mysqli_query($conn, "SELECT * FROM users
                                 WHERE reset_token='$token'
                                 AND reset_expired >= NOW()");

    if (mysqli_num_rows($cek) == 0) {
        echo "<script>
                alert('Link reset password sudah kadaluarsa. Silakan minta ulang.');
                window.location='lupa_password.php';
              </script>";
        exit;
    }

    $user = mysqli_fetch_assoc($cek);

    mysqli_query($conn, "UPDATE users
                          SET password='$passwordHash', reset_token=NULL, reset_expired=NULL
                          WHERE id_user='{$user['id_user']}'");

    echo "<script>
            alert('Password berhasil diubah. Silakan login dengan password baru Anda.');
            window.location='index.php';
          </script>";
    exit;

// ================== MODE: OTP HP ==================
} elseif ($mode === 'otp') {

    if (!isset($_SESSION['id_user_reset'])) {
        header("Location: lupa_password.php");
        exit;
    }

    $id_user = (int) $_SESSION['id_user_reset'];
    $otp     = mysqli_real_escape_string($conn, $_POST['otp'] ?? '');

    $cek = mysqli_query($conn, "SELECT * FROM users
                                 WHERE id_user='$id_user'
                                 AND otp_code='$otp'
                                 AND otp_expired >= NOW()");

    if (mysqli_num_rows($cek) == 0) {
        $_SESSION['flash_error'] = "Kode OTP salah atau sudah kadaluarsa.";
        header("Location: reset_password.php");
        exit;
    }

    $user = mysqli_fetch_assoc($cek);

    mysqli_query($conn, "UPDATE users
                          SET password='$passwordHash', otp_code=NULL, otp_expired=NULL
                          WHERE id_user='{$user['id_user']}'");

    unset($_SESSION['id_user_reset']);

    echo "<script>
            alert('Password berhasil diubah. Silakan login dengan password baru Anda.');
            window.location='index.php';
          </script>";
    exit;

} else {
    header("Location: lupa_password.php");
    exit;
}
?>