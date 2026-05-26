<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once "koneksi.php";

// ===============================
// CEK LOGIN
// ===============================
if (!isset($_SESSION['ses_username']) || empty($_SESSION['ses_username'])) {

    echo "<script>
        alert('Silakan login terlebih dahulu!');
        window.location='login.php';
    </script>";
    exit;
}

$data_username = mysqli_real_escape_string($con, $_SESSION['ses_username']);

// ===============================
// VALIDASI PARAMETER TIPE
// ===============================
$tipe = $_GET['tipe'] ?? '';

$allowed_tipe = [
    'cv',
    'ijazah',
    'skhun',
    'foto',
    'dokumen'
];

if (empty($tipe) || !in_array($tipe, $allowed_tipe)) {

    echo "<script>
        alert('Akses ditolak!');
        window.history.back();
    </script>";
    exit;
}

// ===============================
// AMBIL DATA USER
// ===============================
$query = mysqli_query(
    $con,
    "SELECT * FROM tb_user 
     WHERE username='$data_username' 
     LIMIT 1"
);

if (!$query || mysqli_num_rows($query) == 0) {

    echo "<script>
        alert('Data user tidak ditemukan!');
        window.history.back();
    </script>";
    exit;
}

$data = mysqli_fetch_assoc($query);

// ===============================
// AMBIL NAMA FILE
// ===============================
$file = $data[$tipe] ?? '';

// ===============================
// PATH FOLDER DOKUMEN
// ===============================
$folder_dokumen = $_SERVER['DOCUMENT_ROOT'] . "/adminbkk/dokumen/";

$file_path = $folder_dokumen . $file;

// ===============================
// HAPUS FILE FISIK
// ===============================
if (!empty($file)) {

    if (file_exists($file_path)) {

        unlink($file_path);
    }
}

// ===============================
// KOSONGKAN DATABASE
// ===============================
$update = mysqli_query(
    $con,
    "UPDATE tb_user 
     SET `$tipe`='' 
     WHERE username='$data_username'"
);

// ===============================
// HASIL PROSES
// ===============================
if ($update) {

    echo "<script>
        alert('Dokumen berhasil dihapus');
        window.location='?halaman=profile&tab=dokumen#dokumen';
    </script>";

} else {

    $error = mysqli_error($con);

    echo "<script>
        alert('Gagal menghapus dokumen!\\n$error');
        window.history.back();
    </script>";
}
?>