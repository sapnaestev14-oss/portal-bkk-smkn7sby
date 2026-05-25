<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once "koneksi.php";

// cek session login
if (!isset($_SESSION['ses_username'])) {
    echo "<script>
        alert('Silakan login terlebih dahulu!');
        window.location='login.php';
    </script>";
    exit;
}

$data_username = mysqli_real_escape_string($con, $_SESSION['ses_username']);

// validasi parameter tipe
if (!isset($_GET['tipe']) || empty($_GET['tipe'])) {
    echo "<script>
        alert('Tipe dokumen tidak valid!');
        window.history.back();
    </script>";
    exit;
}

$tipe = mysqli_real_escape_string($con, $_GET['tipe']);

// daftar field yang diizinkan
$allowed_tipe = ['cv', 'ijazah', 'skhun', 'foto', 'dokumen'];

if (!in_array($tipe, $allowed_tipe)) {
    echo "<script>
        alert('Akses ditolak!');
        window.history.back();
    </script>";
    exit;
}

// ambil data user
$query = mysqli_query($con, "SELECT * FROM tb_user WHERE username='$data_username'");

if (!$query || mysqli_num_rows($query) == 0) {
    echo "<script>
        alert('Data user tidak ditemukan!');
        window.history.back();
    </script>";
    exit;
}

$data = mysqli_fetch_assoc($query);

$file = $data[$tipe] ?? '';

// path absolut agar aman di Railway/Linux
$folder_dokumen = __DIR__ . "/dokumen/";
$file_path = $folder_dokumen . $file;

/* hapus file dari folder */
if (!empty($file) && file_exists($file_path)) {
    unlink($file_path);
}

/* kosongkan database */
$update = mysqli_query(
    $con,
    "UPDATE tb_user SET `$tipe`='' WHERE username='$data_username'"
);

if ($update) {
    echo "<script>
        alert('Dokumen berhasil dihapus');
        window.location='?halaman=profile&tab=dokumen#dokumen';
    </script>";
} else {
    echo "<script>
        alert('Gagal menghapus dokumen!');
        window.history.back();
    </script>";
}
?>