<?php
// loker_tambah.php - Handler Tambah Lowongan
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once("koneksi.php");

// ============================================================================
// 🔧 STEP 1: VALIDASI SESSION
// ============================================================================

if (!isset($_SESSION['ses_username'])) {
    echo "<script>
        alert('❌ Session habis! Silakan login ulang.');
        window.location.href='../../login.php';
    </script>";
    exit;
}

// ============================================================================
// 🔧 STEP 2: PROSES FORM SUBMIT
// ============================================================================
if (isset($_POST['btnSimpan'])) {

    // ✅ PERBAIKAN: Ambil id_perusahaan dari FORM, bukan session
    $id_perusahaan = isset($_POST['id_perusahaan']) ? intval($_POST['id_perusahaan']) : 0;
    
    // Validasi id_perusahaan dari form harus valid
    if ($id_perusahaan <= 0) {
        echo "<script>
            alert('❌ Perusahaan belum dipilih!');
            window.history.back();
        </script>";
        exit;
    }
    
    // Verifikasi bahwa perusahaan benar-benar ada di database
    $cek_perusahaan = mysqli_query($con, "SELECT id_perusahaan, nama_perusahaan FROM tb_perusahaan WHERE id_perusahaan = '$id_perusahaan'");
    if (mysqli_num_rows($cek_perusahaan) == 0) {
        echo "<script>
            alert('❌ Perusahaan tidak ditemukan di database!');
            window.history.back();
        </script>";
        exit;
    }

    // Ambil input form
    $judul       = mysqli_real_escape_string($con, $_POST['txtjudul_lowongan'] ?? '');
    $jekel       = mysqli_real_escape_string($con, $_POST['txtjekel'] ?? '');
    $posisi      = mysqli_real_escape_string($con, $_POST['txtposisi'] ?? '');
    $deskripsi   = mysqli_real_escape_string($con, $_POST['txtdeskripsi'] ?? '');
    $kualifikasi = mysqli_real_escape_string($con, $_POST['txtkualifikasi'] ?? '');
    $lokasi      = mysqli_real_escape_string($con, $_POST['txtlokasi'] ?? '');
    $jenis       = mysqli_real_escape_string($con, $_POST['txtjenis_pekerjaan'] ?? '');
    $gaji        = mysqli_real_escape_string($con, $_POST['txtgaji'] ?? '');
    $tanggal     = mysqli_real_escape_string($con, $_POST['txttanggal_posting'] ?? '');
    $batas       = mysqli_real_escape_string($con, $_POST['txtbatas_lamaran'] ?? '');

    // Validasi field wajib
    $errors = [];
    if (empty($judul)) $errors[] = "Judul lowongan wajib diisi";
    if (empty($jekel)) $errors[] = "Jenis kelamin wajib dipilih";
    if (empty($tanggal)) $errors[] = "Tanggal posting wajib diisi";
    if (empty($batas)) $errors[] = "Batas lamaran wajib diisi";
    
    if (!empty($tanggal) && !empty($batas) && $batas < $tanggal) {
        $errors[] = "Batas lamaran tidak boleh sebelum tanggal posting";
    }

    if (!empty($errors)) {
        echo "<script>
            alert('❌ Validasi gagal:\n• " . implode("\\n• ", $errors) . "');
            window.history.back();
        </script>";
        exit;
    }

    // ============================================================================
    // 🔧 STEP 3: INSERT KE DATABASE
    // ============================================================================
    
    $sql = "INSERT INTO tb_lowongan 
    (
        id_perusahaan, 
        judul_lowongan, 
        jekel, 
        posisi, 
        deskripsi, 
        kualifikasi, 
        lokasi, 
        jenis_pekerjaan, 
        gaji, 
        tanggal_posting, 
        batas_lamaran, 
        status
    ) VALUES (
        '$id_perusahaan', 
        '$judul', 
        '$jekel', 
        '$posisi', 
        '$deskripsi', 
        '$kualifikasi', 
        '$lokasi', 
        '$jenis', 
        '$gaji', 
        '$tanggal', 
        '$batas', 
        'aktif'
    )";

    $query = mysqli_query($con, $sql);

    if ($query) {
        echo "<script>
            alert('✅ Lowongan berhasil ditambahkan untuk " . mysqli_fetch_assoc(mysqli_query($con, "SELECT nama_perusahaan FROM tb_perusahaan WHERE id_perusahaan='$id_perusahaan'"))['nama_perusahaan'] . "!');
            window.location.href='index.php?halaman=loker_tampil';
        </script>";
    } else {
        $error_msg = mysqli_error($con);
        
        if (strpos($error_msg, 'foreign key') !== false) {
            echo "<script>
                alert('❌ Gagal: Perusahaan tidak valid.');
                window.history.back();
            </script>";
        } else {
            echo "<script>
                alert('❌ Gagal menyimpan: " . addslashes($error_msg) . "');
                window.history.back();
            </script>";
        }
    }
    exit;
}

// Redirect jika diakses langsung
echo "<script>window.location.href='../../index.php?halaman=loker_tampil';</script>";
exit;
?>