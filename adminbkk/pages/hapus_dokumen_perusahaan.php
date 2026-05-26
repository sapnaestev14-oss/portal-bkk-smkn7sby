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

// tipe dokumen perusahaan
$mapping = [
    'nib'   => 'file_nib',
    'npwp'  => 'file_npwp',
    'mou'   => 'file_mou'
];

if (empty($tipe) || !array_key_exists($tipe, $mapping)) {

    echo "<script>
        alert('Akses ditolak!');
        window.history.back();
    </script>";
    exit;
}

$kolom = $mapping[$tipe];

// ===============================
// AMBIL DATA USER
// ===============================
$query_user = mysqli_query(
    $con,
    "SELECT id_user 
     FROM tb_user
     WHERE username='$data_username'
     LIMIT 1"
);

$user = mysqli_fetch_assoc($query_user);

$id_user = $user['id_user'] ?? 0;

// ===============================
// AMBIL ID PERUSAHAAN
// ===============================
$query_perusahaan = mysqli_query(
    $con,
    "SELECT id_perusahaan
     FROM tb_perusahaan
     WHERE id_user='$id_user'
     LIMIT 1"
);

$perusahaan = mysqli_fetch_assoc($query_perusahaan);

$id_perusahaan = $perusahaan['id_perusahaan'] ?? 0;

// ===============================
// AMBIL DATA DOKUMEN
// ===============================
$query_dokumen = mysqli_query(
    $con,
    "SELECT *
     FROM tb_dokumen_perusahaan
     WHERE id_perusahaan='$id_perusahaan'
     LIMIT 1"
);

$dokumen = mysqli_fetch_assoc($query_dokumen);

$file = $dokumen[$kolom] ?? '';

// ===============================
// PATH FILE
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
    "UPDATE tb_dokumen_perusahaan
     SET `$kolom`=''
     WHERE id_perusahaan='$id_perusahaan'"
);

// ===============================
// HASIL
// ===============================
if ($update) {

    echo "<script>
        alert('Dokumen berhasil dihapus');
        window.location='?halaman=profile_perusahaan&tab=dokumen#dokumen';
    </script>";

} else {

    $error = mysqli_error($con);

    echo "<script>
        alert('Gagal menghapus dokumen!\\n$error');
        window.history.back();
    </script>";
}
?>