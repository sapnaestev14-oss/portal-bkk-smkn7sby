<?php
include "../../koneksi.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// =======================
// CEK LOGIN
// =======================
if (
    !isset($_SESSION['ses_level']) ||
    (
        $_SESSION['ses_level'] != 'admin' &&
        $_SESSION['ses_level'] != 'Ka. BKK' &&
        $_SESSION['ses_level'] != 'perusahaan'
    )
) {

    echo "<script>
        alert('Akses ditolak!');
        window.location='../../login_perusahaan.php';
    </script>";
    exit;
}

// =======================
// VALIDASI AKSI
// =======================
$aksi = $_GET['aksi'] ?? '';
$kode = $_GET['kode'] ?? '';

$aksi = mysqli_real_escape_string($con, $aksi);
$kode = mysqli_real_escape_string($con, $kode);

if ($aksi == '') {

    echo "<script>
        alert('Aksi tidak ditemukan!');
        window.history.back();
    </script>";
    exit;
}

if ($kode == '') {

    echo "<script>
        alert('Kode pendaftaran tidak ditemukan!');
        window.history.back();
    </script>";
    exit;
}

// =======================
// HAPUS DATA
// =======================
if ($aksi == 'hapus') {

    // =======================
    // JIKA LOGIN PERUSAHAAN
    // =======================
    if ($_SESSION['ses_level'] == 'perusahaan') {

        // ambil id perusahaan dari session
        $id_perusahaan = $_SESSION['ses_id_perusahaan'] ?? '';

        if ($id_perusahaan == '') {

            echo "<script>
                alert('Session perusahaan tidak ditemukan!');
                window.history.back();
            </script>";
            exit;
        }

        // hapus hanya lamaran milik perusahaan tersebut
        $hapus = mysqli_query($con, "
            DELETE l
            FROM tb_lamaran l
            INNER JOIN tb_lowongan lw 
            ON l.id_lowongan = lw.id_lowongan
            WHERE l.id_lamaran = '$kode'
            AND lw.id_perusahaan = '$id_perusahaan'
        ");

    } else {

        // admin / ka bkk
        $hapus = mysqli_query(
            $con,
            "DELETE FROM tb_lamaran 
             WHERE id_lamaran='$kode'"
        );
    }

    // =======================
    // HASIL
    // =======================
    if ($hapus) {

        echo "<script>
            alert('Data lamaran berhasil dihapus!');
            window.location='../../index.php?halaman=pendaftar_tampil';
        </script>";

    } else {

        $error = mysqli_error($con);

        echo "<script>
            alert('Gagal menghapus data!\\n$error');
            window.history.back();
        </script>";
    }

} else {

    echo "<script>
        alert('Aksi tidak valid!');
        window.history.back();
    </script>";
}
?>