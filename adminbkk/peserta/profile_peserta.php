<?php
require_once "../koneksi.php";
if (!isset($_GET['id_siswa'])) {
    echo "Data tidak ditemukan";
    exit;
}

// Pastikan session aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ PROSES AJAX: Set status pengalaman kerja
if (isset($_POST['set_pengalaman'])) {
    $_SESSION['punya_pengalaman'] = $_POST['set_pengalaman'];
    echo "OK";
    exit(); // Pastikan exit() dipanggil
}

// ✅ PROSES AJAX: Reset status pengalaman kerja  
if (isset($_POST['reset_pengalaman'])) {
    unset($_SESSION['punya_pengalaman']);
    echo "OK";
    exit(); // Pastikan exit() dipanggil
}


$id_siswa = mysqli_real_escape_string($con, $_GET['id_siswa']);
$tab = isset($_GET['tab']) ? $_GET['tab'] : "personal";
$edit = $_GET['edit'] ?? "";
$edit_keluarga = $_GET['edit_keluarga'] ?? "";
$edit_sosial = $_GET['edit_sosial'] ?? "";
$edit_pendidikan = $_GET['edit_pendidikan'] ?? "";
$edit_pengalaman = $_GET['edit_pengalaman'] ?? "";
$edit_organisasi = isset($_GET['edit_organisasi']) ? $_GET['edit_organisasi'] : "";

/* =========================
AMBIL DATA SISWA
========================= */
$sql = mysqli_query($con, "SELECT * FROM tb_siswa WHERE id_siswa='$id_siswa'");
$tampil = mysqli_fetch_assoc($sql);
if (!$tampil) {
    echo "Data siswa tidak ditemukan";
    exit;
}

/* =========================
PROSES UPDATE DATA PERSONAL
========================= */
if (isset($_POST['simpan_personal'])) {
    $nama = $_POST['nama'] ?? '';
    $jekel = $_POST['jekel'] ?? '';
    $tempat_lhr = $_POST['tempat_lahir'] ?? '';
    $tgl_lhr = !empty($_POST['tanggal_lahir']) 
    ? mysqli_real_escape_string($con, $_POST['tanggal_lahir']) 
    : NULL;
    $nik = $_POST['nik'] ?? '';
    $email = $_POST['email'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $telp = $_POST['no_hp'] ?? '';
    $kewarganegaraan = $_POST['kewarganegaraan'] ?? '';
    $status_perkawinan = $_POST['status_perkawinan'] ?? '';
    $tinggi = $_POST['tinggi_badan'] ?? '';
    $berat = $_POST['berat_badan'] ?? '';
    $jurusan = $_POST['jurusan'] ?? '';
    $tahun_lulus = $_POST['tahun_lulus'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $prestasi = $_POST['prestasi'] ?? '';


    mysqli_query($con, "UPDATE tb_siswa SET
        nama='$nama',
        jekel='$jekel',
        tempat_lahir='$tempat_lhr',
        tanggal_lahir=" . ($tgl_lhr ? "'$tgl_lhr'" : "NULL") . ",
        nik='$nik',
        alamat='$alamat',
        email='$email',
        no_hp='$telp',
        kewarganegaraan='$kewarganegaraan',
        status_perkawinan='$status_perkawinan',
        tinggi_badan='$tinggi',
        berat_badan='$berat',
        jurusan='$jurusan',
        tahun_lulus='$tahun_lulus',
        deskripsi='$deskripsi',
        prestasi='$prestasi'
        WHERE id_siswa='$id_siswa'
    ");
    echo "<script>
        alert('Data personal berhasil diperbarui');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=personal';
    </script>";
    exit;
}

/* =========================
AMBIL DATA KELUARGA
========================= */
$keluarga = mysqli_query($con, "
    SELECT * FROM tb_keluarga
    WHERE id_siswa='$id_siswa'
    ORDER BY id_keluarga ASC
");

/* =========================
AMBIL DATA EDIT KELUARGA
========================= */
$edit_keluarga = isset($_GET['edit_keluarga']) ? $_GET['edit_keluarga'] : "";
if ($edit_keluarga != "") {
    $data_edit_keluarga = mysqli_query($con, "
        SELECT * FROM tb_keluarga
        WHERE id_keluarga='$edit_keluarga'
    ");
    $row_edit_keluarga = mysqli_fetch_assoc($data_edit_keluarga);
}

/* =========================
PROSES TAMBAH DAsTA KELUARGA
========================= */
if (isset($_POST['simpan_keluarga'])) {
    $nama = $_POST['nama_lengkap'];
    $pekerjaan = $_POST['pekerjaan'];
    $status = $_POST['status'];
    mysqli_query($con, "INSERT INTO tb_keluarga
        (id_siswa,nama_lengkap,pekerjaan,status)
        VALUES
        ('$id_siswa','$nama','$pekerjaan','$status')
    ");
    echo "<script>
        alert('Data keluarga berhasil ditambahkan');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=keluarga';
    </script>";
}

/* =========================
PROSES UPDATE DATA KELUARGA
========================= */
if (isset($_POST['update_keluarga'])) {
    $id = $_POST['id_keluarga'];
    $nama = $_POST['nama_lengkap'];
    $pekerjaan = $_POST['pekerjaan'];
    $status = $_POST['status'];
    mysqli_query($con, "UPDATE tb_keluarga SET
        nama_lengkap='$nama',
        pekerjaan='$pekerjaan',
        status='$status'
        WHERE id_keluarga='$id'
    ");
    echo "<script>
        alert('Data keluarga berhasil diperbarui');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=keluarga';
    </script>";
    exit;
}

/* =========================
PROSES HAPUS DATA KELUARGA
========================= */
if (isset($_GET['hapus_keluarga'])) {
    $id = mysqli_real_escape_string($con, $_GET['hapus_keluarga']);
    mysqli_query($con, "DELETE FROM tb_keluarga WHERE id_keluarga='$id'");
    echo "<script>
        alert('Data keluarga berhasil dihapus');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=keluarga';
    </script>";
}

/* =========================
AMBIL DATA SOSIAL MEDIA
========================= */
// ✅ AMBIL id_user yang benar dulu
$sql_get_user = mysqli_query($con, "SELECT id_user FROM tb_siswa WHERE id_siswa='$id_siswa' LIMIT 1");
$row_user = mysqli_fetch_assoc($sql_get_user);
$id_user_benar = $row_user['id_user'] ?? 0;

$sosial = mysqli_query($con, "
SELECT * FROM tb_sosial_media
WHERE id_user='$id_user_benar'  
ORDER BY id_sosial_media ASC
");

/* =========================
AMBIL DATA EDIT SOSIAL
========================= */
$edit_sosial = isset($_GET['edit_sosial']) ? $_GET['edit_sosial'] : "";
if ($edit_sosial != "") {
    $data_edit_sosial = mysqli_query($con, "
        SELECT * FROM tb_sosial_media
        WHERE id_sosial_media='$edit_sosial'
    ");
    $row_edit_sosial = mysqli_fetch_assoc($data_edit_sosial);
}

/* =========================
PROSES TAMBAH SOSIAL MEDIA
========================= */
if (isset($_POST['simpan_sosial'])) {
    $platform = mysqli_real_escape_string($con, $_POST['nama_platform']);
    $link = mysqli_real_escape_string($con, $_POST['link']);

    // ✅ AMBIL id_user yang benar dari tb_siswa
    $sql_get_user = mysqli_query($con, "SELECT id_user FROM tb_siswa WHERE id_siswa='$id_siswa' LIMIT 1");
    $row_user = mysqli_fetch_assoc($sql_get_user);
    $id_user_benar = $row_user['id_user'] ?? 0;

    // ✅ VALIDASI: Pastikan id_user ditemukan
    if ($id_user_benar == 0) {
        echo "<script>
        alert('Error: Data user tidak ditemukan!');
        window.history.back();
        </script>";
        exit;
    }

    // ✅ INSERT dengan id_user yang benar
    $insert = mysqli_query($con, "
    INSERT INTO tb_sosial_media
    (id_user, nama_platform, link, created_at)
    VALUES
    ('$id_user_benar', '$platform', '$link', NOW())
    ");

    if ($insert) {
        echo "<script>
        alert('Sosial media berhasil ditambahkan');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=sosial';
        </script>";
    } else {
        echo "<script>
        alert('Gagal menambahkan: " . mysqli_error($con) . "');
        window.history.back();
        </script>";
    }
}

/* =========================
PROSES UPDATE SOSIAL MEDIA
========================= */
if (isset($_POST['update_sosial'])) {
    $id = $_POST['id_sosial_media'];
    $platform = $_POST['nama_platform'];
    $link = $_POST['link'];
    mysqli_query($con, "
        UPDATE tb_sosial_media SET
        nama_platform='$platform',
        link='$link'
        WHERE id_sosial_media='$id'
    ");
    echo "<script>
        alert('Sosial media berhasil diperbarui');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=sosial';
    </script>";
    exit;
}

/* =========================
PROSES HAPUS SOSIAL MEDIA
========================= */
if (isset($_GET['hapus_sosial'])) {
    $id = $_GET['hapus_sosial'];
    mysqli_query($con, "
        DELETE FROM tb_sosial_media
        WHERE id_sosial_media='$id'
    ");
    echo "<script>
        alert('Sosial media dihapus');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=sosial';
    </script>";
}

/* =========================
AMBIL DATA PENDIDIKAN
========================= */
$pendidikan = mysqli_query($con, "
    SELECT * FROM tb_pendidikan
    WHERE id_siswa='$id_siswa'
    ORDER BY tgl_mulai DESC
");
$edit_pendidikan = isset($_GET['edit_pendidikan']) ? $_GET['edit_pendidikan'] : "";
if ($edit_pendidikan != "") {
    $data_edit_pendidikan = mysqli_query($con, "
        SELECT * FROM tb_pendidikan
        WHERE id_pendidikan='$edit_pendidikan'
    ");
    $row_edit_pendidikan = mysqli_fetch_assoc($data_edit_pendidikan);
}

/* =========================
PROSES TAMBAH DATA PENDIDIKAN
========================= */
if (isset($_POST['simpan_pendidikan'])) {
    $tingkat = $_POST['tingkat'] ?? '';
    $sekolah = $_POST['sekolah'] ?? '';
    $jurusan = $_POST['jurusan'] ?? '';
    $ipk = $_POST['ipk'] ?? '';
    $akreditasi = $_POST['akreditasi'] ?? '';
    $tgl_mulai = $_POST['tgl_mulai'] ?? '';
    $tgl_selesai = $_POST['tgl_selesai'] ?? '';
    $negara = $_POST['negara'] ?? '';
    $provinsi = $_POST['provinsi'] ?? '';
    $kota = $_POST['kota'] ?? '';
    $terakhir = isset($_POST['terakhir']) ? "Ya" : "Tidak";

    // reset pendidikan terakhir lama
    if ($terakhir == "Ya") {
        mysqli_query($con, "
            UPDATE tb_pendidikan
            SET pendidikan_terakhir='Tidak'
            WHERE id_siswa='$id_siswa'
        ");
    }

    mysqli_query($con, "INSERT INTO tb_pendidikan
        (id_siswa,tingkat,sekolah,jurusan,ipk,akreditasi,tgl_mulai,tgl_selesai,negara,provinsi,kota,pendidikan_terakhir)
        VALUES
        ('$id_siswa','$tingkat','$sekolah','$jurusan','$ipk','$akreditasi','$tgl_mulai','$tgl_selesai','$negara','$provinsi','$kota','$terakhir')
    ");
    echo "<script>
        alert('Data pendidikan berhasil ditambahkan');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=pendidikan';
    </script>";
}

/* =========================
PROSES UPDATE PENDIDIKAN
========================= */
if (isset($_POST['update_pendidikan'])) {
    $id = $_POST['id_pendidikan'];
    $tingkat = $_POST['tingkat'];
    $sekolah = $_POST['sekolah'];
    $jurusan = $_POST['jurusan'];
    $ipk = $_POST['ipk'];
    $akreditasi = $_POST['akreditasi'];
    $tgl_mulai = $_POST['tgl_mulai'];
    $tgl_selesai = $_POST['tgl_selesai'];
    $negara = $_POST['negara'];
    $provinsi = $_POST['provinsi'];
    $kota = $_POST['kota'];
    $terakhir = isset($_POST['terakhir']) ? "Ya" : "Tidak";

    // reset pendidikan terakhir jika dicentang
    if ($terakhir == "Ya") {
        mysqli_query($con, "
            UPDATE tb_pendidikan
            SET pendidikan_terakhir='Tidak'
            WHERE id_siswa='$id_siswa'
        ");
    }

    mysqli_query($con, "UPDATE tb_pendidikan SET
        tingkat='$tingkat',
        sekolah='$sekolah',
        jurusan='$jurusan',
        ipk='$ipk',
        akreditasi='$akreditasi',
        tgl_mulai='$tgl_mulai',
        tgl_selesai='$tgl_selesai',
        negara='$negara',
        provinsi='$provinsi',
        kota='$kota',
        pendidikan_terakhir='$terakhir'
        WHERE id_pendidikan='$id'
    ");
    echo "<script>
        alert('Data pendidikan berhasil diperbarui');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=pendidikan';
    </script>";
}

/* =========================
PROSES HAPUS DATA PENDIDIKAN
========================= */
if (isset($_GET['hapus_pendidikan'])) {
    $id = $_GET['hapus_pendidikan'];
    mysqli_query($con, "DELETE FROM tb_pendidikan WHERE id_pendidikan='$id'");
    echo "<script>
        alert('Data pendidikan berhasil dihapus');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=pendidikan';
    </script>";
}

/* =========================
AMBIL DATA SERTIFIKASI
========================= */
$sertifikasi = mysqli_query($con, "
    SELECT * FROM tb_sertifikasi
    WHERE id_siswa='$id_siswa'
    ORDER BY id_sertifikasi DESC
");

/* =========================
PROSES TAMBAH SERTIFIKASI
========================= */
if (isset($_POST['simpan_sertifikasi'])) {
    $nama = $_POST['nama_sertifikat'];
    $lembaga = $_POST['lembaga'];
    $tahun = $_POST['tahun_sertifikat'];
    $berlaku = $_POST['tahun_berlaku'];
    $skor = $_POST['skor'];
    mysqli_query($con, "
        INSERT INTO tb_sertifikasi
        (id_siswa,nama_sertifikat,lembaga,tahun_sertifikat,tahun_berlaku,skor)
        VALUES
        ('$id_siswa','$nama','$lembaga','$tahun','$berlaku','$skor')
    ");
    echo "<script>
        alert('Sertifikasi berhasil ditambahkan');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=sertifikasi';
    </script>";
}

/* =========================
PROSES UPDATE SERTIFIKASI
========================= */
if (isset($_POST['update_sertifikasi'])) {
    $id = $_POST['id_sertifikasi'];
    $nama = $_POST['nama_sertifikat'];
    $lembaga = $_POST['lembaga'];
    $tahun = $_POST['tahun_sertifikat'];
    $berlaku = $_POST['tahun_berlaku'];
    $skor = $_POST['skor'];
    mysqli_query($con, "
        UPDATE tb_sertifikasi SET
        nama_sertifikat='$nama',
        lembaga='$lembaga',
        tahun_sertifikat='$tahun',
        tahun_berlaku='$berlaku',
        skor='$skor'
        WHERE id_sertifikasi='$id'
    ");
    echo "<script>
        alert('Sertifikasi berhasil diupdate');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=sertifikasi';
    </script>";
}

/* =========================
PROSES HAPUS SERTIFIKASI
========================= */
if (isset($_GET['hapus_sertifikasi'])) {
    $id = $_GET['hapus_sertifikasi'];
    mysqli_query($con, "DELETE FROM tb_sertifikasi WHERE id_sertifikasi='$id'");
    echo "<script>
        alert('Sertifikasi dihapus');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=sertifikasi';
    </script>";
}

/* ===============================
PROSES TAMBAH RIWAYAT ORGANISASI
================================== */
$organisasi = mysqli_query($con, "
    SELECT * FROM tb_organisasi
    WHERE id_siswa='$id_siswa'
    ORDER BY id_organisasi DESC
");

if (isset($_POST['simpan_organisasi'])) {
    $nama = $_POST['nama_organisasi'];
    $posisi = $_POST['posisi'];
    $lokasi = $_POST['lokasi'];
    $mulai = $_POST['tahun_mulai'];
    $selesai = $_POST['tahun_selesai'];
    $ket = $_POST['keterangan'];
    mysqli_query($con, "
        INSERT INTO tb_organisasi
        (id_siswa,nama_organisasi,posisi,lokasi,tahun_mulai,tahun_selesai,keterangan)
        VALUES
        ('$id_siswa','$nama','$posisi','$lokasi','$mulai','$selesai','$ket')
    ");
    echo "<script>
        alert('Data organisasi berhasil ditambahkan');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=organisasi';
    </script>";
}

/* ===============================
PROSES UPDATE RIWAYAT ORGANISASI
================================== */
if (isset($_POST['update_organisasi'])) {
    $id = $_POST['id_organisasi'];
    $nama = $_POST['nama_organisasi'];
    $posisi = $_POST['posisi'];
    $lokasi = $_POST['lokasi'];
    $mulai = $_POST['tahun_mulai'];
    $selesai = $_POST['tahun_selesai'];
    $ket = $_POST['keterangan'];
    mysqli_query($con, "
        UPDATE tb_organisasi SET
        nama_organisasi='$nama',
        posisi='$posisi',
        lokasi='$lokasi',
        tahun_mulai='$mulai',
        tahun_selesai='$selesai',
        keterangan='$ket'
        WHERE id_organisasi='$id'
    ");
    echo "<script>
        alert('Data organisasi diupdate');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=organisasi';
    </script>";
}

/* =========================
AMBIL DATA EDIT ORGANISASI
========================= */
if ($edit_organisasi != "") {
    $data_edit_organisasi = mysqli_query($con, "
        SELECT * FROM tb_organisasi
        WHERE id_organisasi='$edit_organisasi'
    ");
    $row_edit_organisasi = mysqli_fetch_assoc($data_edit_organisasi);
}

/* ===============================
PROSES HAPUS RIWAYAT ORGANISASI
================================== */
if (isset($_GET['hapus_organisasi'])) {
    $id = $_GET['hapus_organisasi'];
    mysqli_query($con, "DELETE FROM tb_organisasi WHERE id_organisasi='$id'");
    echo "<script>
        alert('Data dihapus');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=organisasi';
    </script>";
}

/* =========================
AMBIL DATA PENGALAMAN KERJA
========================= */
$pengalaman = mysqli_query($con, "
    SELECT * FROM tb_pengalaman
    WHERE id_siswa='$id_siswa'
    ORDER BY id_pengalaman DESC
");

/* =========================
AMBIL DATA DOKUMEN
========================= */
$dokumen = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT * FROM tb_dokumen WHERE id_siswa='$id_siswa'
")) ?? [];

/* =========================
AMBIL DATA EDIT PENGALAMAN
========================= */
$edit_pengalaman = isset($_GET['edit_pengalaman']) ? $_GET['edit_pengalaman'] : "";
if ($edit_pengalaman != "") {
    $data_edit_pengalaman = mysqli_query($con, "
        SELECT * FROM tb_pengalaman
        WHERE id_pengalaman='$edit_pengalaman'
    ");
    $row_edit_pengalaman = mysqli_fetch_assoc($data_edit_pengalaman);
}

/* =========================
PROSES SIMPAN PENGALAMAN
========================= */
if (isset($_POST['simpan_pengalaman'])) {
    $id = $_POST['id_pengalaman'];
    if ($id == "") {
        $saat_ini = isset($_POST['saat_ini']) ? "Ya" : "Tidak";
        $industri = $_POST['industri'];
        if ($industri == "lainnya") {
            $industri = $_POST['industri_lainnya'];
        }
        mysqli_query($con, "INSERT INTO tb_pengalaman SET
            id_siswa='$id_siswa',
            nama_perusahaan='$_POST[nama_perusahaan]',
            posisi='$_POST[posisi]',
            level_jabatan='$_POST[level_jabatan]',
            status_pegawai='$_POST[status_pegawai]',
            negara='$_POST[negara]',
            provinsi='$_POST[provinsi]',
            kota='$_POST[kota]',
            kecamatan='$_POST[kecamatan]',
            industri='$industri',
            tanggal_mulai='$_POST[dari]',
            tanggal_selesai='$_POST[sampai]',
            saat_ini='$saat_ini',
            mata_uang='$_POST[mata_uang]',
            gaji='$_POST[gaji]',
            nama_referensi='$_POST[nama_referensi]',
            kontak_referensi='$_POST[kontak_referensi]',
            hubungan_referensi='$_POST[hubungan_referensi]',
            fasilitas='$_POST[fasilitas]',
            deskripsi='$_POST[deskripsi]',
            alasan='$_POST[alasan]'
        ");
    } else {
        $saat_ini = isset($_POST['saat_ini']) ? "Ya" : "Tidak";
        $industri = $_POST['industri'];
        if ($industri == "lainnya") {
            $industri = $_POST['industri_lainnya'];
        }
        mysqli_query($con, "UPDATE tb_pengalaman SET
            nama_perusahaan='$_POST[nama_perusahaan]',
            posisi='$_POST[posisi]',
            level_jabatan='$_POST[level_jabatan]',
            status_pegawai='$_POST[status_pegawai]',
            negara='$_POST[negara]',
            provinsi='$_POST[provinsi]',
            kota='$_POST[kota]',
            kecamatan='$_POST[kecamatan]',
            industri='$industri',
            tanggal_mulai='$_POST[dari]',
            tanggal_selesai='$_POST[sampai]',
            saat_ini='$saat_ini',
            mata_uang='$_POST[mata_uang]',
            gaji='$_POST[gaji]',
            nama_referensi='$_POST[nama_referensi]',
            kontak_referensi='$_POST[kontak_referensi]',
            hubungan_referensi='$_POST[hubungan_referensi]',
            fasilitas='$_POST[fasilitas]',
            deskripsi='$_POST[deskripsi]',
            alasan='$_POST[alasan]'
            WHERE id_pengalaman='$id'
        ");
    }
    echo "<script>
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=pengalaman';
    </script>";
}

/* =========================
PROSES HAPUS PENGALAMAN
========================= */
if (isset($_GET['hapus_pengalaman'])) {
    $id = $_GET['hapus_pengalaman'];
    mysqli_query($con, "DELETE FROM tb_pengalaman WHERE id_pengalaman='$id'");
    echo "<script>
        alert('Data pengalaman dihapus');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=pengalaman';
    </script>";
}

/* =========================
PROSES SIMPAN DOKUMEN
========================= */
if (isset($_POST['simpan_dokumen'])) {
    function upload($name)
    {
        if ($_FILES[$name]['name'] != "") {
            $file = time() . "_" . $_FILES[$name]['name'];
            $tmp = $_FILES[$name]['tmp_name'];
            move_uploaded_file($tmp, "file/" . $file);
            return $file;
        }
        return null;
    }

    $ijazah = upload('ijazah');
    $ktp_file = upload('ktp_file');
    $transkrip = upload('transkrip');
    $lain = upload('dokumen_lain');

    // cek apakah sudah ada data dokumen
    $cek = mysqli_fetch_assoc(mysqli_query($con, "
        SELECT * FROM tb_dokumen WHERE id_siswa='$id_siswa'
    "));

    if ($cek) {
        // UPDATE
        $update_parts = [];
        if ($ijazah)
            $update_parts[] = "ijazah = '$ijazah'";
        if ($ktp_file)
            $update_parts[] = "ktp_file = '$ktp_file'";
        if ($transkrip)
            $update_parts[] = "transkrip = '$transkrip'";
        if ($lain)
            $update_parts[] = "dokumen_lain = '$lain'";

        if (!empty($update_parts)) {
            mysqli_query($con, "
                UPDATE tb_dokumen SET
                " . implode(", ", $update_parts) . "
                WHERE id_siswa='$id_siswa'
            ");
        }
    } else {
        // INSERT
        mysqli_query($con, "
            INSERT INTO tb_dokumen
            (id_siswa, ijazah, ktp_file, transkrip, dokumen_lain)
            VALUES
            ('$id_siswa','$ijazah','$ktp_file','$transkrip','$lain')
        ");
    }
    echo "<script>
        alert('Dokumen berhasil disimpan');
        window.location='?halaman=profile_peserta&id_siswa=$id_siswa&tab=dokumen';
    </script>";
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Profil Siswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        input:invalid {
            border: 1px solid red;
        }

        body {
            background: #f4f7fb;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
        }

        /* ================= CARD ================= */
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
            max-width: 1350px;
            margin: 0;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(120deg, #007bff, #00c6ff);
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            padding: 14px 18px;
        }

        /* ================= FOTO ================= */
        .profile-img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .2);
        }

        .edit-foto {
            position: absolute;
            bottom: 6px;
            right: 6px;
            background: #007bff;
            color: #fff;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .25);
        }

        /* ================= TAB ================= */
        /* WRAPPER */
        .tab-wrapper {
            display: flex;
            align-items: center;
            position: relative;
        }

        .tab-wrapper::after {
            content: "";
            position: absolute;
            right: 0;
            top: 0;
            width: 40px;
            height: 100%;
            background: linear-gradient(to left, white, transparent);
        }

        /* TAB CONTAINER */
        .tab-nav {
            display: flex;
            gap: 5px;
            overflow-x: auto;
            scroll-behavior: smooth;
            flex-wrap: nowrap;
            white-space: nowrap;
            width: 100%;
            border-bottom: 2px solid #e5e7eb;
        }

        /* HILANGKAN SCROLLBAR */
        .tab-nav::-webkit-scrollbar {
            display: none;
        }

        /* paksa konten lebih panjang dari layar */
        .tab-nav::after {
            content: '';
            padding-right: 50px;
        }

        /* ITEM */
        .tab-nav a {
            flex: 0 0 auto;
            padding: 14px 22px;
            font-size: 15px;
            min-width: max-content;
            text-decoration: none;
            color: #6b7280;
            border-bottom: 2px solid transparent;
            transition: 0.2s;
        }

        /* ACTIVE */
        .tab-nav a.active {
            color: #2563eb;
            border-bottom: 3px solid #2563eb;
        }

        /* HOVER */
        .tab-nav a:hover {
            color: #2563eb;
        }

        /* BUTTON */
        .tab-btn {
            position: relative;
            z-index: 10;
            border: none;
            background: white;
            cursor: pointer;
            padding: 6px 10px;
            font-size: 16px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
        }

        /* POSISI */
        .tab-btn.left {
            margin-right: 5px;
        }

        .tab-btn.right {
            margin-left: 5px;
        }

        /* RESPONSIVE */
        @media(max-width:768px) {
            .tab-btn {
                display: none;
            }
        }

        /* ================= TAB CONTENT ================= */
        .tab-pane {
            display: none;
            opacity: 0;
            transform: translateY(8px);
            transition: all .3s ease;
        }

        .tab-pane.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        /* ================= DATA CARD ================= */
        .data-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
        }

        .data-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            font-weight: 600;
            font-size: 15px;
        }

        /* ================= BUTTON ================= */
        .btn {
            padding: 7px 16px;
            border-radius: 6px;
            background: #2563eb;
            color: #fff;
            border: none;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
            position: relative;
            z-index: 10;
        }

        .btn-edit {
            border: 1px solid #ddd;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            color: #333;
        }

        /* ================= POPUP ================= */
        .overlay-edit {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.55);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
        }

        .edit-box {
            background: #fff;
            width: 650px;
            max-width: 95%;
            max-height: 90vh;
            overflow-y: auto;
            border-radius: 14px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, .25);
            padding: 25px 28px;
            animation: popup .25s ease;
        }

        @keyframes popup {
            from {
                transform: translateY(25px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .edit-box label {
            font-weight: 600;
            font-size: 13px;
            margin-top: 10px;
            display: block;
            color: #444;
        }

        .edit-box input,
        .edit-box select {
            width: 100%;
            padding: 8px 10px;
            margin-top: 4px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        /* ================= PROFILE edit button ================= */
        .btn-simpan {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: .2s;
        }

        .btn-simpan:hover {
            background: #1e4ed8;
        }

        .btn-batal {
            background: #e5e7eb;
            color: #333;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            margin-left: 10px;
        }

        .btn-batal:hover {
            background: #d1d5db;
        }

        /* ================= PROFILE GRID ================= */
        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            padding: 15px;
        }

        /* ITEM PROFILE */
        .profile-item {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 12px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
            transition: .25s;
        }

        .profile-item:hover {
            background: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .08);
        }

        /* ICON */
        .profile-icon {
            font-size: 15px;
            color: #2563eb;
            width: 22px;
            text-align: center;
            margin-top: 2px;
        }

        /* TEXT */
        .profile-content {
            flex: 1;
        }

        .profile-label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .profile-value {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
        }

        /* ================= DESKRIPSI ================= */
        .profile-desc {
            padding: 18px;
            border-top: 1px solid #eee;
        }

        .profile-desc h4 {
            margin-bottom: 5px;
            font-size: 15px;
        }

        .profile-desc p {
            font-size: 14px;
            color: #555;
            line-height: 1.5;
        }

        /* ================= RESPONSIVE ================= */
        @media(max-width:768px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }

            .card {
                margin: 20px;
            }
        }

        /* ================= PROFILE TEXT SECTION ================= */
        .profile-section {
            margin-top: 18px;
            border-top: 1px solid #e5e7eb;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .profile-text-card {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 14px 18px;
            transition: .25s;
            width: 100%;
            min-height: 90px;
            box-sizing: border-box;
        }

        .profile-text-card:hover {
            background: #fff;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .profile-text-title {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #111827;
        }

        .profile-text-title i {
            color: #2563eb;
            font-size: 16px;
        }

        .profile-text-content {
            font-size: 14px;
            line-height: 1.5;
            color: #555;
            margin-top: 5px;
        }

        /* jika kosong */
        .profile-empty {
            color: #9ca3af;
            font-style: italic;
        }

        /* mobile */
        @media(max-width:768px) {
            .profile-section {
                padding: 15px;
            }

            .profile-text-card {
                padding: 15px;
            }
        }

        .edit-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 10px;
        }

        .edit-grid label {
            font-size: 13px;
            font-weight: 600;
            color: #555;
        }

        .edit-grid input,
        .edit-grid select {
            width: 100%;
            padding: 9px 11px;
            border-radius: 7px;
            border: 1px solid #ddd;
            font-size: 14px;
            background: #fafafa;
            transition: .2s;
        }

        .edit-grid input:focus,
        .edit-grid select:focus {
            outline: none;
            border-color: #2563eb;
            background: #fff;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, .1);
        }

        @media(max-width:700px) {
            .edit-grid {
                grid-template-columns: 1fr;
            }
        }

        .full-width {
            grid-column: 1 / 3;
        }

        .profile-text-card {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 18px;
            width: 100%;
        }

        .profile-text-title {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #111827;
        }

        .profile-text-title i {
            color: #2563eb;
        }

        .profile-text-content {
            font-size: 14px;
            line-height: 1.6;
            color: #555;
        }

        .profile-empty {
            color: #9ca3af;
            font-style: italic;
        }

        .keluarga-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 15px;
            background: #fff;
            transition: .25s;
        }

        .keluarga-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, .08);
        }

        /* top card */
        .keluarga-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        /* badge status */
        .badge-status {
            background: #2563eb;
            color: #fff;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .keluarga-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        /* grid */
        .keluarga-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .keluarga-grid label {
            font-size: 12px;
            color: #6b7280;
        }

        .keluarga-grid p {
            font-weight: 600;
            margin: 2px 0;
        }

        /* responsive */
        @media(max-width:768px) {
            .keluarga-grid {
                grid-template-columns: 1fr;
            }
        }

        /* item */
        .keluarga-item .label {
            font-size: 12px;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 3px;
        }

        .keluarga-item p {
            font-weight: 600;
            margin: 0;
        }

        .aksi a {
            margin-left: 10px;
            color: #555;
            font-size: 14px;
        }

        .aksi a:hover {
            color: #2563eb;
        }

        .empty-keluarga {
            text-align: center;
            padding: 40px;
            color: #9ca3af;
        }

        .empty-keluarga i {
            font-size: 28px;
            margin-bottom: 10px;
            display: block;
        }

        .sosmed-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .sosmed-info {
            display: flex;
            flex-direction: column;
        }

        .sosmed-title {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .sosmed-title i {
            margin-right: 6px;
            color: #2563eb;
        }

        .sosmed-link a {
            color: #555;
            text-decoration: none;
        }

        .sosmed-link a:hover {
            text-decoration: underline;
        }

        .sosmed-action {
            display: flex;
            gap: 10px;
        }

        .sosmed-action i {
            cursor: pointer;
            color: #555;
        }

        .sosmed-action i:hover {
            color: #2563eb;
        }

        /* ================= SERTIFIKASI CLEAN ================= */
        .sertifikat-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 18px;
            margin: 15px;
            background: #fff;
        }

        /* HEADER */
        .sertifikat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding: 0 18px 10px 18px;
            margin-left: -18px;
            margin-right: -18px;
            border-bottom: 1px solid #e5e7eb;
        }

        .sertifikat-title {
            font-weight: 600;
            font-size: 15px;
            color: #111827;
        }

        /* ACTION */
        .sertifikat-action a {
            margin-left: 10px;
            color: #555;
            font-size: 14px;
        }

        .sertifikat-action a:hover {
            color: #2563eb;
        }

        /* GRID */
        .sertifikat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .sertifikat-grid label {
            font-size: 12px;
            color: #6b7280;
        }

        .sertifikat-grid p {
            font-weight: 600;
            margin-top: 3px;
        }

        /* SKOR */
        .skor {
            color: #10b981;
        }

        /* MOBILE */
        @media(max-width:768px) {
            .sertifikat-grid {
                grid-template-columns: 1fr;
            }
        }

        .item-label {
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #374151;
            font-size: 13px;
        }

        .item-label i {
            color: #6366f1;
            font-size: 13px;
        }

        .item-value {
            margin-top: 3px;
            color: #6b7280;
            font-size: 14px;
        }

        .file-upload-box {
            margin-bottom: 15px;
        }

        .btn-file {
            padding: 6px 12px;
            border: 1px solid #ccc;
            background: #f8f8f8;
            border-radius: 6px;
            cursor: pointer;
        }

        .file-preview {
            margin-top: 6px;
            color: #16a34a;
            font-size: 14px;
        }

        .file-preview a {
            margin-left: 8px;
            color: #333;
            text-decoration: none;
        }

        .file-preview a:hover {
            text-decoration: underline;
        }

        .upload-group {
            margin-bottom: 15px;
        }

        .file-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #e0ecff;
            color: #1e3a8a;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 13px;
        }

        .file-item i {
            color: #2563eb;
        }

        .file-item button {
            border: none;
            background: none;
            color: red;
            font-weight: bold;
            cursor: pointer;
            margin-left: 5px;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="card-header">
            <i class="fa fa-user"></i>
            Profil Saya : <?php echo $tampil['nama']; ?>
        </div>
        <div style="padding:20px">
            <center>
                <?php
                if ($tampil['foto'] == "") {
                    $foto = "dist/img/pegawai.png";
                } else {
                    $foto = "foto/" . $tampil['foto'];
                }
                ?>
                <div style="position:relative;display:inline-block;">
                    <img src="<?php echo $foto; ?>" class="profile-img">
                    <a href="?halaman=edit_foto&id_siswa=<?php echo $id_siswa ?>" class="edit-foto">
                        <i class="fa fa-camera"></i>
                    </a><br><br>
                </div>
            </center>
            <!-- TAB MENU -->
            <br>
            <div class="tab-wrapper">
                <!-- TOMBOL KIRI -->
                <button type="button" class="tab-btn left" onclick="scrollTab(-300)">
                    <i class="fa fa-chevron-left"></i>
                </button>
                <!-- TAB -->
                <div class="tab-nav" id="tabNav">
                    <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=personal" class="<?php if ($tab == 'personal')
                           echo 'active'; ?>">
                        <i class="fa fa-user"></i> Data Personal
                    </a>
                    <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=sosial" class="<?php if ($tab == 'sosial')
                           echo 'active'; ?>">
                        <i class="fa fa-share-alt"></i> Akun Sosial Media
                    </a>
                <!--    <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=keluarga" class="<?php if ($tab == 'keluarga')
                           echo 'active'; ?>">
                        <i class="fa fa-users"></i> Data Keluarga
                    </a> -->
                    <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=pendidikan" class="<?php if ($tab == 'pendidikan')
                           echo 'active'; ?>">
                        <i class="fa fa-graduation-cap"></i> Riwayat Pendidikan
                    </a>
                <!--    <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=sertifikasi" class="<?php if ($tab == 'sertifikasi')
                           echo 'active'; ?>">
                        <i class="fa fa-certificate"></i> Sertifikasi
                    </a> -->
                <!--    <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=organisasi" class="<?php if ($tab == 'organisasi')
                           echo 'active'; ?>">
                        <i class="fa fa-history"></i> Riwayat organisasi
                    </a> -->
                    <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=pengalaman" class="<?php if ($tab == 'pengalaman')
                           echo 'active'; ?>">
                        <i class="fa fa-briefcase"></i> Pengalaman Kerja
                    </a>
                    <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=dokumen" class="<?php if ($tab == 'dokumen')
                           echo 'active'; ?>">
                        <i class="fa fa-file"></i> Data Pendukung
                    </a>
                </div>
                <!-- TOMBOL KANAN -->
                <button type="button" class="tab-btn right" onclick="scrollTab(300)">
                    <i class="fa fa-chevron-right"></i>
                </button>
            </div>

            <!-- PERSONAL -->
            <div class="tab-pane <?php if ($tab == 'personal')
                echo 'active'; ?>">
                <div class="data-card">
                    <div class="data-header">
                        Data Personal
                        <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=personal&edit=personal"
                            class="btn-edit">
                            <i class="fa fa-edit"></i> Ubah
                        </a>
                    </div>
                    <div class="profile-grid">
                        <div class="profile-item">
                            <div class="profile-icon"><i class="fa fa-id-card"></i></div>
                            <div class="profile-content">
                                <div class="profile-label">Nomor NIK KTP</div>
                                <div class="profile-value"><?php echo $tampil['nik'] ?? '-'; ?></div>
                            </div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-icon"><i class="fa fa-map-marker"></i></div>
                            <div class="profile-content">
                                <div class="profile-label">Tempat Lahir</div>
                                <div class="profile-value"><?php echo $tampil['tempat_lahir']; ?></div>
                            </div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-icon"><i class="fa fa-user"></i></div>
                            <div class="profile-content">
                                <div class="profile-label">Nama Lengkap</div>
                                <div class="profile-value"><?php echo $tampil['nama']; ?></div>
                            </div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-icon"><i class="fa fa-calendar"></i></div>
                            <div class="profile-content">
                                <div class="profile-label">Tanggal Lahir</div>
                                <div class="profile-value"><?php echo $tampil['tanggal_lahir']; ?></div>
                            </div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-icon"><i class="fa fa-envelope"></i></div>
                            <div class="profile-content">
                                <div class="profile-label">Email</div>
                                <div class="profile-value"><?php echo $tampil['email'] ?? '-'; ?></div>
                            </div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-icon"><i class="fa fa-birthday-cake"></i></div>
                            <div class="profile-content">
                                <div class="profile-label">Usia</div>
                                <div class="profile-value">
                                    <?php
                                    if (!empty($tampil['tanggal_lahir'])) {
                                        $birthDate = new DateTime($tampil['tanggal_lahir']);
                                        $today = new DateTime();
                                        echo $today->diff($birthDate)->y . " Tahun";
                                    } else {
                                        echo "-";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-icon"><i class="fa fa-home"></i></div>
                            <div class="profile-content">
                                <div class="profile-label">Alamat</div>
                                <div class="profile-value"><?php echo $tampil['alamat'] ?? '-'; ?></div>
                            </div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-icon"><i class="fa fa-phone"></i></div>
                            <div class="profile-content">
                                <div class="profile-label">Nomor Handphone</div>
                                <div class="profile-value"><?php echo $tampil['no_hp']; ?></div>
                            </div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-icon"><i class="fa fa-flag"></i></div>
                            <div class="profile-content">
                                <div class="profile-label">Kewarganegaraan</div>
                                <div class="profile-value"><?php echo $tampil['kewarganegaraan'] ?? 'Indonesia'; ?>
                                </div>
                            </div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-icon"><i class="fa fa-venus-mars"></i></div>
                            <div class="profile-content">
                                <div class="profile-label">Jenis Kelamin</div>
                                <div class="profile-value"><?php echo $tampil['jekel']; ?></div>
                            </div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-icon"><i class="fa fa-heart"></i></div>
                            <div class="profile-content">
                                <div class="profile-label">Status Perkawinan</div>
                                <div class="profile-value"><?php echo $tampil['status_perkawinan'] ?? '-'; ?></div>
                            </div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-icon"><i class="fa fa-arrows-v"></i></div>
                            <div class="profile-content">
                                <div class="profile-label">Tinggi Badan</div>
                                <div class="profile-value"><?php echo $tampil['tinggi_badan'] ?? '-'; ?> cm</div>
                            </div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-icon"><i class="fa fa-balance-scale"></i></div>
                            <div class="profile-content">
                                <div class="profile-label">Berat Badan</div>
                                <div class="profile-value"><?php echo $tampil['berat_badan'] ?? '-'; ?> kg</div>
                            </div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-icon"><i class="fa fa-book"></i></div>
                            <div class="profile-content">
                                <div class="profile-label">Jurusan</div>
                                <div class="profile-value"><?php echo $tampil['jurusan'] ?? '-'; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-icon"><i class="fa fa-history"></i></div>
                        <div class="profile-content">
                            <div class="profile-label">Tahun Lulus</div>
                            <div class="profile-value"><?php echo $tampil['tahun_lulus'] ?? '-'; ?></div>
                        </div>
                    </div>
                    <div class="profile-item full-width">
                        <div class="profile-text-card">
                            <div class="profile-text-title">
                                <i class="fa fa-user"></i> Deskripsi Diri
                            </div>
                            <div class="profile-text-content">
                                <?php echo !empty($tampil['deskripsi']) ? $tampil['deskripsi'] : '<span class="profile-empty">Belum diisi</span>'; ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-item full-width">
                        <div class="profile-text-card">
                            <div class="profile-text-title">
                                <i class="fa fa-trophy"></i> Prestasi
                            </div>
                            <div class="profile-text-content">
                                <?php echo !empty($tampil['prestasi']) ? $tampil['prestasi'] : '<span class="profile-empty">Belum ada prestasi</span>'; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- SOSIAL MEDIA -->
        <div class="tab-pane <?php if ($tab == 'sosial')
            echo 'active'; ?>">
            <div class="data-card">
                <div class="data-header">
                    <span>Akun Sosial Media</span>
                    <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=sosial&add_sosial=1"
                        class="btn-edit">
                        <i class="fa fa-plus"></i> Tambah
                    </a>
                </div>
                <div class="sosmed-list">
                    <?php if (mysqli_num_rows($sosial) == 0) { ?>
                        <div style="text-align:center;color:#888;padding:30px;">
                            <i class="fa fa-info-circle"></i>
                            Belum ada akun sosial media
                        </div>
                    <?php } ?>
                    <?php while ($row = mysqli_fetch_assoc($sosial)) { ?>
                        <div class="sosmed-item">
                            <div class="sosmed-info">
                                <div class="sosmed-title">
                                    <i
                                        class="fa fa-<?php echo $row['nama_platform'] == 'website' ? 'globe' : $row['nama_platform']; ?>"></i>
                                    <?php echo ucfirst($row['nama_platform']); ?>
                                </div>
                                <div class="sosmed-link">
                                    <a href="<?php echo $row['link']; ?>" target="_blank">
                                        <?php echo $row['link']; ?>
                                    </a>
                                </div>
                            </div>
                            <div class="sosmed-action">
                                <a
                                    href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=sosial&edit_sosial=<?php echo $row['id_sosial_media']; ?>">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=sosial&hapus_sosial=<?php echo $row['id_sosial_media']; ?>"
                                    onclick="return confirm('Hapus akun ini?')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- KELUARGA -->
        <div class="tab-pane <?php if ($tab == 'keluarga')
            echo 'active'; ?>">
            <div class="data-card">
                <div class="data-header">
                    Data Keluarga
                    <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=keluarga&add=1"
                        class="btn-edit">
                        <i class="fa fa-plus"></i> Tambah
                    </a>
                </div>
                <?php if (mysqli_num_rows($keluarga) == 0) { ?>
                    <div class="empty-keluarga">
                        <i class="fa fa-users"></i>
                        <p>Belum ada data keluarga</p>
                    </div>
                <?php } ?>
                <?php while ($row = mysqli_fetch_assoc($keluarga)) { ?>
                    <div class="keluarga-card">
                        <div class="keluarga-top">
                            <span class="badge-status">
                                <i class="fa fa-user"></i>
                                <?php echo $row['status']; ?>
                            </span>
                            <div class="aksi">
                                <a
                                    href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=keluarga&edit_keluarga=<?php echo $row['id_keluarga']; ?>">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=keluarga&hapus_keluarga=<?php echo $row['id_keluarga']; ?>"
                                    onclick="return confirm('Hapus data keluarga ini?')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <div class="keluarga-grid">
                            <div class="keluarga-item">
                                <span class="label">
                                    <i class="fa fa-id-card"></i>
                                    Nama Lengkap
                                </span>
                                <p><?php echo $row['nama_lengkap']; ?></p>
                            </div>
                            <div class="keluarga-item">
                                <span class="label">
                                    <i class="fa fa-briefcase"></i>
                                    Pekerjaan
                                </span>
                                <p><?php echo $row['pekerjaan']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- PENDIDIKAN -->
        <div class="tab-pane <?php if ($tab == 'pendidikan')
            echo 'active'; ?>">
            <div class="data-card">
                <div class="data-header">
                    Pendidikan
                    <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=pendidikan&add_pendidikan=1"
                        class="btn-edit">
                        <i class="fa fa-plus"></i> Tambah
                    </a>
                </div>
                <div class="pendidikan-list">
                    <?php if (mysqli_num_rows($pendidikan) == 0) { ?>
                        <div class="empty-keluarga">
                            <i class="fa fa-graduation-cap"></i>
                            <p>Belum ada riwayat pendidikan</p>
                        </div>
                    <?php } ?>
                    <?php while ($row = mysqli_fetch_assoc($pendidikan)) { ?>
                        <div class="keluarga-card">
                            <div class="keluarga-top">
                                <span class="badge-status">
                                    <i class="fa fa-graduation-cap"></i>
                                    <?php echo $row['tingkat']; ?>
                                </span>
                                <div class="aksi">
                                    <a
                                        href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=pendidikan&edit_pendidikan=<?php echo $row['id_pendidikan']; ?>">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=pendidikan&hapus_pendidikan=<?php echo $row['id_pendidikan']; ?>"
                                        onclick="return confirm('Hapus riwayat pendidikan ini?')">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="keluarga-grid">
                                <div class="keluarga-item">
                                    <span class="label"><i class="fa fa-university"></i>Sekolah / Universitas</span>
                                    <p><?php echo $row['sekolah']; ?></p>
                                </div>
                                <div class="keluarga-item">
                                    <span class="label"><i class="fa fa-book"></i>Jurusan</span>
                                    <p><?php echo $row['jurusan']; ?></p>
                                </div>
                                <div class="keluarga-item">
                                    <span class="label"><i class="fa fa-line-chart"></i>IPK / Nilai</span>
                                    <p><?php echo $row['ipk']; ?></p>
                                </div>
                                <div class="keluarga-item">
                                    <span class="label"><i class="fa fa-star"></i>Akreditasi</span>
                                    <p><?php echo $row['akreditasi']; ?></p>
                                </div>
                                <div class="keluarga-item">
                                    <span class="label"><i class="fa fa-calendar"></i>Tanggal Mulai</span>
                                    <p><?= !empty($row['tgl_mulai']) ? date('d M Y', strtotime($row['tgl_mulai'])) : '-' ?>
                                    </p>
                                </div>
                                <div class="keluarga-item">
                                    <span class="label"><i class="fa fa-calendar-check-o"></i>Tanggal Selesai</span>
                                    <p><?= $row['tgl_selesai'] == '' ? '<span style="color:green;">Masih Berjalan</span>' : date('d M Y', strtotime($row['tgl_selesai'])) ?>
                                    </p>
                                </div>
                            </div>
                            <?php if ($row['pendidikan_terakhir'] == "Ya") { ?>
                                <div
                                    style="margin-top:10px;display:inline-block;background:#10b981;color:white;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                                    <i class="fa fa-star"></i> Pendidikan Terakhir
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- SERTIFIKASI -->
        <div class="tab-pane <?php if ($tab == 'sertifikasi')
            echo 'active'; ?>">
            <div class="data-card">
                <div class="data-header">
                    Sertifikasi
                    <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=sertifikasi&add_sertifikasi=1"
                        class="btn-edit">
                        <i class="fa fa-plus"></i> Tambah
                    </a>
                </div>
                <?php if (mysqli_num_rows($sertifikasi) == 0) { ?>
                    <div class="empty-keluarga">
                        <i class="fa fa-certificate"></i>
                        <p>Belum ada sertifikasi</p>
                    </div>
                <?php } ?>
                <?php while ($row = mysqli_fetch_assoc($sertifikasi)) { ?>
                    <div class="sertifikat-card">
                        <!-- HEADER -->
                        <div class="sertifikat-header">
                            <div class="sertifikat-title">
                                <?php echo $row['nama_sertifikat']; ?>
                            </div>
                            <div class="sertifikat-action">
                                <a
                                    href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=sertifikasi&edit_sertifikasi=<?php echo $row['id_sertifikasi']; ?>">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=sertifikasi&hapus_sertifikasi=<?php echo $row['id_sertifikasi']; ?>"
                                    onclick="return confirm('Hapus sertifikasi ini?')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <!-- CONTENT -->
                        <div class="sertifikat-grid">
                            <div>
                                <label>Lembaga Sertifikasi</label>
                                <p><?php echo $row['lembaga']; ?></p>
                            </div>
                            <div>
                                <label>Tahun Sertifikat</label>
                                <p><?php echo $row['tahun_sertifikat']; ?></p>
                            </div>
                            <div>
                                <label>Tahun Berlaku</label>
                                <p><?php echo $row['tahun_berlaku'] ?: '-'; ?></p>
                            </div>
                            <div>
                                <label>Skor</label>
                                <p class="skor"><?php echo $row['skor']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- ORGANISASI -->
        <div class="tab-pane <?php if ($tab == 'organisasi')
            echo 'active'; ?>">
            <div class="data-card">
                <div class="data-header">
                    Riwayat organisasi
                    <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=organisasi&add_organisasi=1"
                        class="btn-edit">
                        <i class="fa fa-plus"></i> Tambah
                    </a>
                </div>
                <?php if (mysqli_num_rows($organisasi) == 0) { ?>
                    <div class="empty-keluarga">
                        <i class="fa fa-users"></i>
                        <p>Belum ada riwayat organisasi</p>
                    </div>
                <?php } ?>
                <?php while ($row = mysqli_fetch_assoc($organisasi)) { ?>
                    <div class="sertifikat-card">
                        <!-- HEADER -->
                        <div class="sertifikat-header">
                            <div class="sertifikat-title">
                                <?php echo $row['nama_organisasi']; ?>
                            </div>
                            <div class="sertifikat-action">
                                <a
                                    href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=organisasi&edit_organisasi=<?php echo $row['id_organisasi']; ?>">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a
                                    href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=organisasi&hapus_organisasi=<?php echo $row['id_organisasi']; ?>">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <!-- CONTENT -->
                        <div class="sertifikat-grid">
                            <div>
                                <label>Posisi</label>
                                <p><?php echo $row['posisi']; ?></p>
                            </div>
                            <div>
                                <label>Lokasi</label>
                                <p><?php echo $row['lokasi']; ?></p>
                            </div>
                            <div>
                                <label>Dari</label>
                                <p><?php echo $row['tahun_mulai']; ?></p>
                            </div>
                            <div>
                                <label>Sampai</label>
                                <p><?php echo $row['tahun_selesai']; ?></p>
                            </div>
                        </div>
                        <!-- KETERANGAN -->
                        <div style="margin-top:15px;">
                            <label style="font-size:12px;color:#6b7280;">Keterangan</label>
                            <p style="margin-top:5px;"><?php echo $row['keterangan']; ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- PENGALAMAN KERJA -->
        <!-- PENGALAMAN KERJA -->
        <div class="tab-pane <?php if ($tab == 'pengalaman')
            echo 'active'; ?>">
            <div class="data-card">
                <div class="data-header">
                    Pengalaman Kerja
                    <?php
                    // Tampilkan tombol tambah HANYA jika user pilih "Sudah" DAN ada data
                    if ((isset($_SESSION['punya_pengalaman']) && $_SESSION['punya_pengalaman'] == 'Ya') || mysqli_num_rows($pengalaman) > 0) {
                        ?>
                        <a href="?halaman=profile_peserta&id_siswa=<?= $id_siswa ?>&tab=pengalaman&add_pengalaman=1"
                            class="btn-edit">
                            <i class="fa fa-plus"></i> Tambah
                        </a>
                    <?php } ?>
                </div>

                <?php
                $jumlah_pengalaman = mysqli_num_rows($pengalaman);

                // 🔹 KASUS 1: Belum pilih status (tampilkan pilihan)
                if ($jumlah_pengalaman == 0 && !isset($_SESSION['punya_pengalaman'])) {
                    ?>
                    <div style="padding:60px 40px; text-align:center;">
                        <i class="fa fa-briefcase" style="font-size:64px; margin-bottom:20px; color:#9ca3af;"></i>
                        <h3 style="margin-bottom:10px; color:#111827;">Apakah Anda sudah memiliki pengalaman kerja?</h3>
                        <p style="color:#6b7280; margin-bottom:30px;">Silakan pilih salah satu opsi di bawah ini</p>

                        <div style="display:flex; gap:20px; justify-content:center; flex-wrap:wrap;">
                            <button type="button" onclick="setPunyaPengalaman('Ya')" class="btn"
                                style="background:#10b981; padding:15px 40px; font-size:16px;">
                                <i class="fa fa-check"></i> Sudah Punya
                            </button>
                            <button type="button" onclick="setPunyaPengalaman('Tidak')" class="btn"
                                style="background:#6b7280; padding:15px 40px; font-size:16px;">
                                <i class="fa fa-times"></i> Belum Punya
                            </button>
                        </div>
                    </div>

                    <?php
                    // 🔹 KASUS 2: Pilih "Belum Punya" 
                } else if (isset($_SESSION['punya_pengalaman']) && $_SESSION['punya_pengalaman'] == 'Tidak') {
                    ?>
                        <div style="padding:60px 40px; text-align:center; color:#6b7280;">
                            <i class="fa fa-info-circle" style="font-size:64px; margin-bottom:20px; color:#9ca3af;"></i>
                            <h3 style="margin-bottom:10px; color:#111827;">Anda belum memiliki pengalaman kerja</h3>
                            <p style="margin-bottom:20px;">Status ini akan tetap dihitung dalam kelengkapan profil Anda ✓
                            </p>
                            <button onclick="resetPilihanPengalaman()" class="btn" style="background:#2563eb;">
                                <i class="fa fa-edit"></i> Ubah Pilihan
                            </button>
                        </div>


                    <?php
                    // SUDAH PILIH "YA" TAPI BELUM ISI DATA
                } else if ($jumlah_pengalaman == 0) {
                    ?>
                            <div style="padding:60px 40px; text-align:center; color:#6b7280;">
                                <i class="fa fa-plus-circle" style="font-size:64px; margin-bottom:20px; color:#9ca3af;"></i>
                                <h3 style="margin-bottom:10px; color:#111827;">Silakan tambahkan pengalaman kerja Anda</h3>
                                <p style="margin-bottom:20px;">Klik tombol di bawah untuk mulai menambahkan</p>
                                <a href="?halaman=profile_peserta&id_siswa=<?= $id_siswa ?>&tab=pengalaman&add_pengalaman=1"
                                    class="btn" style="padding:12px 30px;">
                                    <i class="fa fa-plus"></i> Tambah Pengalaman Kerja
                                </a>

                                <!-- ✅ TAMBAHKAN INI untuk reset pilihan -->
                                <br><br>
                                <button onclick="resetPilihanPengalaman()"
                                    style="background:none;border:none;color:#6b7280;cursor:pointer;text-decoration:underline;font-size:14px;">
                                    ← Ubah Pilihan
                                </button>
                            </div>

                    <?php
                    // 🔹 KASUS 4: Ada data pengalaman (tampilkan seperti biasa)
                } else {
                    ?>
                    <?php while ($row = mysqli_fetch_assoc($pengalaman)) { ?>
                                <div class="sertifikat-card">
                                    <div class="sertifikat-header">
                                        <div class="sertifikat-title">
                                    <?php echo $row['nama_perusahaan']; ?>
                                        </div>
                                        <div class="sertifikat-action">
                                            <a
                                                href="?halaman=profile_peserta&id_siswa=<?= $id_siswa ?>&tab=pengalaman&edit_pengalaman=<?= $row['id_pengalaman']; ?>">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a href="?halaman=profile_peserta&id_siswa=<?= $id_siswa ?>&tab=pengalaman&hapus_pengalaman=<?= $row['id_pengalaman']; ?>"
                                                onclick="return confirm('Hapus data ini?')">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="sertifikat-grid">
                                        <div>
                                            <div class="item-label"><i class="fa fa-briefcase"></i> Posisi</div>
                                            <div class="item-value"><?= $row['posisi'] ?></div>
                                        </div>
                                        <div>
                                            <div class="item-label"><i class="fa fa-sitemap"></i> Level</div>
                                            <div class="item-value"><?= $row['level_jabatan'] ?></div>
                                        </div>
                                        <div>
                                            <div class="item-label"><i class="fa fa-user"></i> Status Pegawai</div>
                                            <div class="item-value"><?= $row['status_pegawai'] ?></div>
                                        </div>
                                        <div>
                                            <div class="item-label"><i class="fa fa-globe"></i> Negara</div>
                                            <div class="item-value"><?= $row['negara'] ?></div>
                                        </div>
                                        <div>
                                            <div class="item-label"><i class="fa fa-map-marker"></i> Provinsi</div>
                                            <div class="item-value"><?= $row['provinsi'] ?></div>
                                        </div>
                                        <div>
                                            <div class="item-label"><i class="fa fa-map-marker"></i> Kota/Kabupaten</div>
                                            <div class="item-value"><?= $row['kota'] ?></div>
                                        </div>
                                        <div>
                                            <div class="item-label"><i class="fa fa-map-marker"></i> Kecamatan</div>
                                            <div class="item-value"><?= $row['kecamatan'] ?></div>
                                        </div>
                                        <div>
                                            <div class="item-label"><i class="fa fa-industry"></i> Industri</div>
                                            <div class="item-value"><?= $row['industri'] ?></div>
                                        </div>
                                        <div>
                                            <div class="item-label"><i class="fa fa-calendar"></i> Dari</div>
                                            <div class="item-value"><?= date('d M Y', strtotime($row['tanggal_mulai'])) ?></div>
                                        </div>
                                        <div>
                                            <div class="item-label"><i class="fa fa-calendar"></i> Sampai</div>
                                            <div class="item-value">
                                        <?= $row['saat_ini'] == 'Ya' ? 'Sekarang' : date('d M Y', strtotime($row['tanggal_selesai'])) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="item-label"><i class="fa fa-money"></i> Gaji</div>
                                            <div class="item-value"><?= $row['mata_uang'] ?>         <?= number_format($row['gaji']) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="item-label"><i class="fa fa-user-circle"></i> Nama Referensi</div>
                                            <div class="item-value"><?= $row['nama_referensi'] ?></div>
                                        </div>
                                        <div>
                                            <div class="item-label"><i class="fa fa-align-left"></i> Keterangan</div>
                                            <div class="item-value"><?= $row['deskripsi'] ?></div>
                                        </div>
                                        <div>
                                            <div class="item-label"><i class="fa fa-phone"></i> Kontak Referensi</div>
                                            <div class="item-value"><?= $row['kontak_referensi'] ?></div>
                                        </div>
                                        <div>
                                            <div class="item-label"><i class="fa fa-sign-out"></i> Alasan Berhenti</div>
                                            <div class="item-value"><?= $row['alasan'] ?></div>
                                        </div>
                                        <div>
                                            <div class="item-label"><i class="fa fa-users"></i> Hubungan Referensi</div>
                                            <div class="item-value"><?= $row['hubungan_referensi'] ?></div>
                                        </div>
                                    </div>
                                    <div style="margin-top:15px;">
                                        <div class="item-label"><i class="fa fa-gift"></i> Fasilitas</div>
                                        <div class="item-value"><?= $row['fasilitas'] ?></div>
                                    </div>
                                    <div style="margin-top:15px;">
                                        <div class="item-label"><i class="fa fa-briefcase"></i> Status Kerja</div>
                                        <div class="item-value">
                                    <?= $row['saat_ini'] == 'Ya' ? 'Masih Bekerja' : 'Sudah Keluar' ?>
                                    <?php if ($row['saat_ini'] == "Ya") { ?>
                                                <span
                                                    style="background:#10b981;color:#fff;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;margin-left:8px;">Aktif</span>
                                    <?php } else { ?>
                                                <span
                                                    style="background:#ef4444;color:#fff;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;margin-left:8px;">Tidak
                                                    Aktif</span>
                                    <?php } ?>
                                        </div>
                                    </div>
                                </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>

        <!-- DOKUMEN -->
        <div class="tab-pane <?php if ($tab == 'dokumen')
            echo 'active'; ?>">
            <div class="data-card">
                <div class="data-header">
                    Dokumen Pendukung
                    <a href="?halaman=profile_peserta&id_siswa=<?= $id_siswa ?>&tab=dokumen&edit_dokumen=1"
                        class="btn-edit">
                        <i class="fa fa-pencil"></i> Ubah
                    </a>
                </div>
                <div style="padding:15px;">
                    <?php
                    $ijazah = $dokumen['ijazah'] ?? '';
                    $ktp = $dokumen['ktp_file'] ?? '';
                    $transkrip = $dokumen['transkrip'] ?? '';
                    $dok_lain = $dokumen['dokumen_lain'] ?? '';
                    ?>
                    <!-- IJAZAH -->
                    <div class="profile-text-card">
                        <div class="profile-text-title">Ijazah</div>
                        <?php if (!empty($ijazah)) { ?>
                            <a href="file/<?= $ijazah ?>" target="_blank"><?= basename($ijazah) ?></a>
                        <?php } else { ?>
                            <span class="profile-empty">Belum upload</span>
                        <?php } ?>
                    </div>
                    <!-- KTP -->
                    <div class="profile-text-card">
                        <div class="profile-text-title">KTP</div>
                        <?php if (!empty($ktp)) { ?>
                            <a href="file/<?= $ktp ?>" target="_blank"><?= basename($ktp) ?></a>
                        <?php } else { ?>
                            <span class="profile-empty">Belum upload</span>
                        <?php } ?>
                    </div>
                    <!-- TRANSKRIP -->
                    <div class="profile-text-card">
                        <div class="profile-text-title">Transkrip</div>
                        <?php if (!empty($transkrip)) { ?>
                            <a href="file/<?= $transkrip ?>" target="_blank"><?= basename($transkrip) ?></a>
                        <?php } else { ?>
                            <span class="profile-empty">Belum upload</span>
                        <?php } ?>
                    </div>
                    <!-- DOKUMEN LAIN -->
                    <div class="profile-text-card">
                        <div class="profile-text-title">Dokumen Lain</div>
                        <?php if (!empty($dok_lain)) { ?>
                            <a href="file/<?= $dok_lain ?>" target="_blank"><?= basename($dok_lain) ?></a>
                        <?php } else { ?>
                            <span class="profile-empty">Belum upload</span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- POPUP EDIT -->
        <?php if ($edit == "personal") { ?>
            <!DOCTYPE html>
            <html lang="id">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Edit Data Personal - Portal BKK</title>
                <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
                    rel="stylesheet">
                <style>
                    .overlay-edit {
                        background: rgba(0, 0, 0, 0.6);
                        backdrop-filter: blur(5px);
                        animation: fadeIn 0.3s ease;
                    }

                    @keyframes fadeIn {
                        from {
                            opacity: 0;
                        }

                        to {
                            opacity: 1;
                        }
                    }

                    /* Modal Box yang lebih rapi */
                    .edit-box {
                        background: #ffffff;
                        border-radius: 16px;
                        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                        animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
                        max-height: 90vh;
                        overflow-y: auto;
                    }

                    @keyframes slideUp {
                        from {
                            transform: translateY(30px) scale(0.95);
                            opacity: 0;
                        }

                        to {
                            transform: translateY(0) scale(1);
                            opacity: 1;
                        }
                    }

                    /* Header Modal */
                    .edit-box>div:first-child {
                        background: linear-gradient(135deg, #19286b 0%, #6e82e7 100%);
                        color: white;
                        padding: 24px 28px;
                        border-radius: 16px 16px 0 0;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin: -25px -28px 24px -28px;
                    }

                    .edit-box>div:first-child h3 {
                        margin: 0;
                        font-size: 20px;
                        font-weight: 600;
                        display: flex;
                        align-items: center;
                        gap: 10px;
                    }

                    .edit-box>div:first-child a {
                        color: white;
                        opacity: 0.8;
                        transition: opacity 0.2s;
                        width: 32px;
                        height: 32px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border-radius: 8px;
                        background: rgba(255, 255, 255, 0.1);
                    }

                    .edit-box>div:first-child a:hover {
                        opacity: 1;
                        background: rgba(255, 255, 255, 0.2);
                    }

                    /* Form Grid yang lebih rapi */
                    .edit-grid {
                        display: grid;
                        grid-template-columns: repeat(2, 1fr);
                        gap: 20px;
                        margin-bottom: 0;
                    }

                    .edit-grid .form-group {
                        display: flex;
                        flex-direction: column;
                        gap: 6px;
                    }

                    .edit-grid label {
                        font-size: 12px;
                        font-weight: 600;
                        color: #4a5568;
                        text-transform: uppercase;
                        letter-spacing: 0.3px;
                        margin-bottom: 0;
                    }

                    .edit-grid input,
                    .edit-grid select,
                    .edit-grid textarea {
                        width: 100%;
                        padding: 10px 14px;
                        border: 2px solid #e2e8f0;
                        border-radius: 10px;
                        font-size: 14px;
                        background: #f8fafc;
                        transition: all 0.2s ease;
                        font-family: inherit;
                        box-sizing: border-box;
                    }

                    .edit-grid input:hover,
                    .edit-grid select:hover,
                    .edit-grid textarea:hover {
                        border-color: #cbd5e0;
                        background: #ffffff;
                    }

                    .edit-grid input:focus,
                    .edit-grid select:focus,
                    .edit-grid textarea:focus {
                        outline: none;
                        border-color: #667eea;
                        background: #ffffff;
                        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
                    }

                    .edit-grid input::placeholder {
                        color: #a0aec0;
                    }

                    /* Full width untuk field tertentu */
                    .edit-grid .full-width {
                        grid-column: 1 / -1;
                    }

                    /* Textarea yang lebih baik */
                    .edit-grid textarea {
                        resize: vertical;
                        min-height: 80px;
                    }

                    /* Footer dengan tombol */
                    .edit-box>div:last-child {
                        margin-top: 24px;
                        padding-top: 24px;
                        border-top: 1px solid #e2e8f0;
                        display: flex;
                        gap: 12px;
                        justify-content: flex-end;
                    }

                    .edit-box .btn {
                        padding: 11px 24px;
                        border-radius: 10px;
                        font-weight: 600;
                        font-size: 14px;
                        transition: all 0.2s ease;
                        border: none;
                        cursor: pointer;
                        display: inline-flex;
                        align-items: center;
                        gap: 8px;
                        text-decoration: none;
                    }

                    .edit-box .btn-primary {
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
                    }

                    .edit-box .btn-primary:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
                    }

                    .edit-box .btn-secondary {
                        background: #f1f5f9;
                        color: #475569;
                    }

                    .edit-box .btn-secondary:hover {
                        background: #e2e8f0;
                        transform: translateY(-2px);
                    }

                    /* Scrollbar yang lebih cantik */
                    .edit-box::-webkit-scrollbar {
                        width: 8px;
                    }

                    .edit-box::-webkit-scrollbar-track {
                        background: #f1f5f9;
                        border-radius: 4px;
                    }

                    .edit-box::-webkit-scrollbar-thumb {
                        background: #cbd5e0;
                        border-radius: 4px;
                    }

                    .edit-box::-webkit-scrollbar-thumb:hover {
                        background: #94a3b8;
                    }

                    /* Responsive untuk mobile */
                    @media (max-width: 768px) {
                        .edit-grid {
                            grid-template-columns: 1fr;
                            gap: 16px;
                        }

                        .edit-box {
                            margin: 20px;
                            max-width: calc(100% - 40px);
                        }

                        .edit-box>div:first-child {
                            margin: -25px -25px 20px -25px;
                            padding: 20px 24px;
                        }

                        .edit-box {
                            padding: 24px 25px !important;
                        }

                        .edit-box>div:last-child {
                            flex-direction: column;
                        }

                        .edit-box .btn {
                            width: 100%;
                            justify-content: center;
                        }
                    }

                    /* Helper untuk icon */
                    .edit-box h3 i {
                        font-size: 22px;
                    }

                    /* Select dropdown yang lebih baik */
                    .edit-grid select {
                        appearance: none;
                        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
                        background-repeat: no-repeat;
                        background-position: right 12px center;
                        padding-right: 40px;
                    }

                    /* Checkbox dan radio yang lebih baik */
                    .edit-grid input[type="checkbox"],
                    .edit-grid input[type="radio"] {
                        width: auto;
                        margin-right: 8px;
                    }

                    /* Label dengan checkbox */
                    .edit-grid label[style*="display:flex"] {
                        display: flex !important;
                        align-items: center;
                        gap: 8px;
                        text-transform: none;
                        cursor: pointer;
                    }
                </style>
            </head>

            <body>
                <div class="overlay-edit">
                    <div class="edit-box" style="padding: 25px 28px;">
                        <div>
                            <h3><i class="fa fa-user"></i> Edit Data Personal</h3>
                            <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=personal">×</a>
                        </div>

                        <form method="POST">
                            <div class="edit-grid">
                                <div class="form-group">
                                    <label>Nomor KTP</label>
                                    <input type="text" name="nik" value="<?php echo $tampil['nik'] ?? ''; ?>"
                                        placeholder="Masukkan NIK">
                                </div>

                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="nama" value="<?php echo $tampil['nama']; ?>"
                                        placeholder="Masukkan nama lengkap">
                                </div>

                                <div class="form-group">
                                    <label>Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" value="<?php echo $tampil['tempat_lahir']; ?>"
                                        placeholder="Masukkan tempat lahir">
                                </div>

                                <div class="form-group">
                                    <label>Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" value="<?php echo $tampil['tanggal_lahir']; ?>">
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" value="<?php echo $tampil['email'] ?? ''; ?>"
                                        placeholder="contoh@email.com">
                                </div>

                                <div class="form-group">
                                    <label>Nomor Handphone</label>
                                    <input type="text" name="no_hp" value="<?php echo $tampil['no_hp']; ?>"
                                        placeholder="08xxxxxxxxxx">
                                </div>

                                <div class="form-group full-width">
                                    <label>Alamat</label>
                                    <textarea name="alamat" rows="3"
                                        placeholder="Masukkan alamat lengkap"><?php echo $tampil['alamat'] ?? ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Berat Badan (kg)</label>
                                    <input type="number" name="berat_badan"
                                        value="<?php echo $tampil['berat_badan'] ?? ''; ?>" placeholder="Contoh: 58">
                                </div>

                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <select name="jekel">
                                        <option value="Pria" <?php if ($tampil['jekel'] == "Pria")
                                            echo "selected"; ?>>
                                            Pria</option>
                                        <option value="Wanita" <?php if ($tampil['jekel'] == "Wanita")
                                            echo "selected"; ?>>
                                            Wanita</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Kewarganegaraan</label>
                                    <input type="text" name="kewarganegaraan"
                                        value="<?php echo $tampil['kewarganegaraan'] ?? 'Indonesia'; ?>">
                                </div>

                                <div class="form-group">
                                    <label>Status Perkawinan</label>
                                    <select name="status_perkawinan">
                                        <option value="Belum Menikah" <?php if ($tampil['status_perkawinan'] == "Belum Menikah")
                                            echo "selected"; ?>>Belum Menikah</option>
                                        <option value="Menikah" <?php if ($tampil['status_perkawinan'] == "Menikah")
                                            echo "selected"; ?>>Menikah</option>
                                    </select>
                                </div>

                                <div class="form-group full-width">
                                    <label>Tinggi Badan (cm)</label>
                                    <input type="number" name="tinggi_badan"
                                        value="<?php echo $tampil['tinggi_badan'] ?? ''; ?>" placeholder="Contoh: 165">
                                </div>

                                <div class="form-group full-width">
                                    <label>jurusan</label>
                                    <textarea name="jurusan" rows="3"
                                        placeholder="Jurusan di SMKN 7 Surabaya"><?php echo $tampil['jurusan'] ?? ''; ?></textarea>
                                </div>

                                <div class="form-group full-width">
                                    <label>Tahun Lulus</label>
                                    <textarea name="tahun_lulus" rows="3"
                                        placeholder="Tahun Lulus Sekolah"><?php echo $tampil['tahun_lulus'] ?? ''; ?></textarea>
                                </div>

                                <div class="form-group full-width">
                                    <label>Deskripsi Diri</label>
                                    <textarea name="deskripsi" rows="4"
                                        placeholder="Tuliskan deskripsi singkat tentang diri Anda"><?php echo $tampil['deskripsi'] ?? ''; ?></textarea>
                                </div>

                                <div class="form-group full-width">
                                    <label>Prestasi</label>
                                    <textarea name="prestasi" rows="3"
                                        placeholder="Tuliskan prestasi yang pernah diraih"><?php echo $tampil['prestasi'] ?? ''; ?></textarea>
                                </div>

                            </div>

                            <div>
                                <button type="button" class="btn btn-secondary"
                                    onclick="window.location.href='?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=personal'">
                                    <i class="fa fa-times"></i> Batal
                                </button>
                                <button type="submit" name="simpan_personal" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </body>

            </html>
        <?php } ?>

        <?php if (isset($_GET['add_sosial'])) { ?>
            <div class="overlay-edit">
                <div class="edit-box">
                    <h3>Tambah Akun Sosial Media</h3>
                    <form method="POST">
                        <label>Platform</label>
                        <select name="nama_platform" id="platform" required>
                            <option value="">Pilih</option>
                            <option value="instagram">Instagram</option>
                            <option value="linkedin">LinkedIn</option>
                            <option value="facebook">Facebook</option>
                            <option value="twitter">Twitter / X</option>
                            <option value="github">Github</option>
                            <option value="website">Website / Portfolio</option>
                            <option value="whatsapp">Whatsapp</option>
                        </select>
                        <label id="label_link">Link / Username</label>
                        <input type="text" name="link" id="input_link" required>
                        <br><br>
                        <button class="btn" name="simpan_sosial">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                        <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=sosial" class="btn">Batal</a>
                    </form>
                </div>
            </div>
        <?php } ?>

        <?php if ($edit_sosial != "") { ?>
            <div class="overlay-edit">
                <div class="edit-box">
                    <h3>Edit Akun Sosial Media</h3>
                    <form method="POST">
                        <input type="hidden" name="id_sosial_media"
                            value="<?php echo $row_edit_sosial['id_sosial_media']; ?>">
                        <label>Platform</label>
                        <select name="nama_platform" required>
                            <option value="instagram" <?php if ($row_edit_sosial['nama_platform'] == "instagram")
                                echo "selected"; ?>>Instagram</option>
                            <option value="linkedin" <?php if ($row_edit_sosial['nama_platform'] == "linkedin")
                                echo "selected"; ?>>LinkedIn</option>
                            <option value="facebook" <?php if ($row_edit_sosial['nama_platform'] == "facebook")
                                echo "selected"; ?>>Facebook</option>
                            <option value="twitter" <?php if ($row_edit_sosial['nama_platform'] == "twitter")
                                echo "selected"; ?>>Twitter</option>
                            <option value="github" <?php if ($row_edit_sosial['nama_platform'] == "github")
                                echo "selected"; ?>>Github</option>
                            <option value="website" <?php if ($row_edit_sosial['nama_platform'] == "website")
                                echo "selected"; ?>>Website</option>
                            <option value="whatsapp" <?php if ($row_edit_sosial['nama_platform'] == "whatsapp")
                                echo "selected"; ?>>Whatsapp</option>
                        </select>
                        <label>Link / Username</label>
                        <input type="text" name="link" value="<?php echo $row_edit_sosial['link']; ?>" required>
                        <br><br>
                        <button class="btn" name="update_sosial">
                            <i class="fa fa-save"></i> Update
                        </button>
                        <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=sosial" class="btn">Batal</a>
                    </form>
                </div>
            </div>
        <?php } ?>

        <?php if ($edit_keluarga != "") { ?>
            <div class="overlay-edit">
                <div class="edit-box">
                    <h3 style="margin-top:0;">Edit Data Keluarga</h3>
                    <form method="POST">
                        <input type="hidden" name="id_keluarga" value="<?php echo $row_edit_keluarga['id_keluarga']; ?>">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="<?php echo $row_edit_keluarga['nama_lengkap']; ?>"
                            required>
                        <label>Pekerjaan</label>
                        <input type="text" name="pekerjaan" value="<?php echo $row_edit_keluarga['pekerjaan']; ?>" required>
                        <label>Status</label>
                        <select name="status" required>
                            <option value="">Pilih hubungan</option>
                            <option <?php if ($row_edit_keluarga['status'] == "Ayah")
                                echo "selected"; ?>>Ayah</option>
                            <option <?php if ($row_edit_keluarga['status'] == "Ibu")
                                echo "selected"; ?>>Ibu</option>
                            <option <?php if ($row_edit_keluarga['status'] == "Suami")
                                echo "selected"; ?>>Suami</option>
                            <option <?php if ($row_edit_keluarga['status'] == "Istri")
                                echo "selected"; ?>>Istri</option>
                            <option <?php if ($row_edit_keluarga['status'] == "Kakak")
                                echo "selected"; ?>>Kakak</option>
                            <option <?php if ($row_edit_keluarga['status'] == "Adik")
                                echo "selected"; ?>>Adik</option>
                            <option <?php if ($row_edit_keluarga['status'] == "Wali")
                                echo "selected"; ?>>Wali</option>
                        </select>
                        <br><br>
                        <button class="btn" name="update_keluarga">
                            <i class="fa fa-save"></i> Update
                        </button>
                        <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=keluarga"
                            class="btn">Batal</a>
                    </form>
                </div>
            </div>
        <?php } ?>

        <?php if (isset($_GET['add'])) { ?>
            <div class="overlay-edit">
                <div class="edit-box">
                    <h3 style="margin-top:0;margin-bottom:15px;">Tambah data anggota keluarga</h3>
                    <form method="POST">
                        <label>Nama lengkap *</label>
                        <input type="text" name="nama_lengkap" required>
                        <label>Pekerjaan *</label>
                        <input type="text" name="pekerjaan" required>
                        <label>Status *</label>
                        <select name="status" required>
                            <option value="">Pilih hubungan</option>
                            <option>Ayah</option>
                            <option>Ibu</option>
                            <option>Suami</option>
                            <option>Istri</option>
                            <option>Kakak</option>
                            <option>Adik</option>
                            <option>Wali</option>
                        </select>
                        <br><br>
                        <button class="btn" name="simpan_keluarga">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                        <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=keluarga"
                            class="btn">Batal</a>
                    </form>
                </div>
            </div>
        <?php } ?>

        <?php if (isset($_GET['add_pendidikan'])) { ?>
            <div class="overlay-edit">
                <div class="edit-box">
                    <h3>Tambah Riwayat Pendidikan</h3>
                    <form method="POST">

                        <label>Tingkat Pendidikan *</label>
                        <select name="tingkat" id="tingkat" required onchange="toggleFields()">
                            <option value="">Pilih Jenjang</option>
                            <optgroup label="Pendidikan Dasar">
                                <option value="SD">SD - Sekolah Dasar</option>
                                <option value="MI">MI - Madrasah Ibtidaiyah</option>
                                <option value="SMP">SMP - Sekolah Menengah Pertama</option>
                                <option value="MTs">MTs - Madrasah Tsanawiyah</option>
                            </optgroup>
                            <optgroup label="Pendidikan Menengah">
                                <option value="SMA">SMA - Sekolah Menengah Atas</option>
                                <option value="SMK">SMK - Sekolah Menengah Kejuruan</option>
                                <option value="MA">MA - Madrasah Aliyah</option>
                            </optgroup>
                            <optgroup label="Pendidikan Tinggi">
                                <option value="D3">D3 - Diploma III</option>
                                <option value="D4">D4 - Diploma IV</option>
                                <option value="S1">S1 - Sarjana</option>
                            </optgroup>
                        </select>

                        <label>Nama Sekolah / Universitas *</label>
                        <input type="text" name="sekolah" required placeholder="Contoh: SMKN 7 Surabaya">

                        <!-- Field Jurusan (Conditional) -->
                        <div id="jurusanField" style="display:none;">
                            <label>Jurusan / Program Studi</label>
                            <input type="text" name="jurusan" id="jurusanInput"
                                placeholder="Contoh: Teknik Komputer dan Jaringan">
                        </div>

                        <!-- Field IPK (Conditional - Only for College) -->
                        <div id="ipkField" style="display:none;">
                            <label>IPK / Nilai Rata-rata</label>
                            <input type="text" name="ipk" id="ipkInput" placeholder="Contoh: 3.75">
                        </div>

                        <label>Akreditasi</label>
                        <select name="akreditasi" required>
                            <option value="">Pilih</option>
                            <option>A / Unggul</option>
                            <option>B / Baik Sekali</option>
                            <option>C / Baik</option>
                            <option>Tidak Terakreditasi</option>
                        </select>

                        <label>Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai">

                        <label>Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai">

                        <label>Negara</label>
                        <input type="text" name="negara" value="Indonesia">

                        <label>Provinsi</label>
                        <input type="text" name="provinsi">

                        <label>Kota / Kabupaten</label>
                        <input type="text" name="kota">

                        <div style="display:flex;align-items:center;gap:8px;margin-top:12px;">
                            <input type="checkbox" name="terakhir" value="Ya" style="width:16px;height:16px;">
                            <label style="margin:0;font-size:13px;">✓ Pendidikan terakhir saat ini</label>
                        </div>

                        <br><br>
                        <button class="btn" name="simpan_pendidikan">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                        <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=pendidikan" class="btn">
                            Batal
                        </a>
                    </form>
                </div>
            </div>

            <!-- JavaScript untuk Toggle Field -->
            <script>
                function toggleFields() {
                    const tingkat = document.getElementById('tingkat').value;
                    const jurusanField = document.getElementById('jurusanField');
                    const ipkField = document.getElementById('ipkField');
                    const jurusanInput = document.getElementById('jurusanInput');
                    const ipkInput = document.getElementById('ipkInput');

                    // Reset semua field
                    jurusanField.style.display = 'none';
                    ipkField.style.display = 'none';
                    jurusanInput.required = false;
                    ipkInput.required = false;

                    // Logika berdasarkan jenjang
                    const kuliah = ['D3', 'D4', 'S1', 'S2', 'S3'];
                    const kejuruan = ['SMK'];
                    const umum = ['SD', 'MI', 'SMP', 'MTs', 'SMA', 'MA'];

                    if (kuliah.includes(tingkat)) {
                        // Kuliah: Tampilkan Jurusan + IPK
                        jurusanField.style.display = 'block';
                        ipkField.style.display = 'block';
                        jurusanInput.required = true;
                        ipkInput.required = true;
                        jurusanInput.placeholder = "Contoh: Teknik Informatika";
                    }
                    else if (kejuruan.includes(tingkat)) {
                        // SMK: Tampilkan Jurusan, Sembunyikan IPK
                        jurusanField.style.display = 'block';
                        ipkField.style.display = 'none';
                        jurusanInput.required = true;
                        ipkInput.required = false;
                        jurusanInput.placeholder = "Contoh: Teknik Komputer dan Jaringan";
                    }
                    else if (umum.includes(tingkat)) {
                        // SD/SMP/SMA/MI/MTs/MA: Sembunyikan Jurusan + IPK
                        jurusanField.style.display = 'none';
                        ipkField.style.display = 'none';
                        jurusanInput.required = false;
                        ipkInput.required = false;
                    }
                }

                // Jalankan saat halaman load (untuk form edit)
                document.addEventListener('DOMContentLoaded', function () {
                    if (document.getElementById('tingkat')) {
                        toggleFields();
                    }
                });
            </script>
        <?php } ?>

        <?php if ($edit_pendidikan != "") { ?>
            <div class="overlay-edit">
                <div class="edit-box">
                    <h3>Edit Riwayat Pendidikan</h3>
                    <form method="POST">
                        <input type="hidden" name="id_pendidikan"
                            value="<?php echo $row_edit_pendidikan['id_pendidikan']; ?>">

                        <label>Tingkat Pendidikan</label>
                        <select name="tingkat" id="tingkat_edit" required onchange="toggleFieldsEdit()">
                            <option value="">Pilih Jenjang</option>
                            <optgroup label="Pendidikan Dasar">
                                <option value="SD" <?php if ($row_edit_pendidikan['tingkat'] == "SD")
                                    echo "selected"; ?>>SD -
                                    Sekolah Dasar</option>
                                <option value="MI" <?php if ($row_edit_pendidikan['tingkat'] == "MI")
                                    echo "selected"; ?>>MI -
                                    Madrasah Ibtidaiyah</option>
                                <option value="SMP" <?php if ($row_edit_pendidikan['tingkat'] == "SMP")
                                    echo "selected"; ?>>
                                    SMP - Sekolah Menengah Pertama</option>
                                <option value="MTs" <?php if ($row_edit_pendidikan['tingkat'] == "MTs")
                                    echo "selected"; ?>>
                                    MTs - Madrasah Tsanawiyah</option>
                            </optgroup>
                            <optgroup label="Pendidikan Menengah">
                                <option value="SMA" <?php if ($row_edit_pendidikan['tingkat'] == "SMA")
                                    echo "selected"; ?>>
                                    SMA - Sekolah Menengah Atas</option>
                                <option value="SMK" <?php if ($row_edit_pendidikan['tingkat'] == "SMK")
                                    echo "selected"; ?>>
                                    SMK - Sekolah Menengah Kejuruan</option>
                                <option value="MA" <?php if ($row_edit_pendidikan['tingkat'] == "MA")
                                    echo "selected"; ?>>MA -
                                    Madrasah Aliyah</option>
                            </optgroup>
                            <optgroup label="Pendidikan Tinggi">
                                <option value="D3" <?php if ($row_edit_pendidikan['tingkat'] == "D3")
                                    echo "selected"; ?>>D3 -
                                    Diploma III</option>
                                <option value="D4" <?php if ($row_edit_pendidikan['tingkat'] == "D4")
                                    echo "selected"; ?>>D4 -
                                    Diploma IV</option>
                                <option value="S1" <?php if ($row_edit_pendidikan['tingkat'] == "S1")
                                    echo "selected"; ?>>S1 -
                                    Sarjana</option>
                                <option value="S2" <?php if ($row_edit_pendidikan['tingkat'] == "S2")
                                    echo "selected"; ?>>S2 -
                                    Magister</option>
                                <option value="S3" <?php if ($row_edit_pendidikan['tingkat'] == "S3")
                                    echo "selected"; ?>>S3 -
                                    Doktor</option>
                            </optgroup>
                        </select>

                        <label>Nama Sekolah / Universitas</label>
                        <input type="text" name="sekolah" value="<?php echo $row_edit_pendidikan['sekolah']; ?>">

                        <!-- Field Jurusan (Conditional) -->
                        <div id="jurusanField_edit"
                            style="display:<?= in_array($row_edit_pendidikan['tingkat'], ['SMK']) ? 'block' : 'none'; ?>;">
                            <label>Jurusan / Program Studi</label>
                            <input type="text" name="jurusan" id="jurusanInput_edit"
                                value="<?php echo $row_edit_pendidikan['jurusan']; ?>"
                                placeholder="Contoh: Teknik Komputer dan Jaringan">
                        </div>

                        <!-- Field IPK (Conditional - Only for College) -->
                        <div id="ipkField_edit"
                            style="display:<?= in_array($row_edit_pendidikan['tingkat'], ['D3', 'D4', 'S1', 'S2', 'S3']) ? 'block' : 'none'; ?>;">
                            <label>IPK / Nilai Rata-rata</label>
                            <input type="text" name="ipk" id="ipkInput_edit"
                                value="<?php echo $row_edit_pendidikan['ipk']; ?>" placeholder="Contoh: 3.75">
                        </div>

                        <label>Akreditasi</label>
                        <select name="akreditasi" required>
                            <option value="">Pilih</option>
                            <option <?php if ($row_edit_pendidikan['akreditasi'] == "A")
                                echo "selected"; ?>>A</option>
                            <option <?php if ($row_edit_pendidikan['akreditasi'] == "B")
                                echo "selected"; ?>>B</option>
                            <option <?php if ($row_edit_pendidikan['akreditasi'] == "C")
                                echo "selected"; ?>>C</option>
                            <option <?php if ($row_edit_pendidikan['akreditasi'] == "Unggul")
                                echo "selected"; ?>>Unggul
                            </option>
                            <option <?php if ($row_edit_pendidikan['akreditasi'] == "Baik")
                                echo "selected"; ?>>Baik</option>
                            <option <?php if ($row_edit_pendidikan['akreditasi'] == "Baik Sekali")
                                echo "selected"; ?>>Baik
                                Sekali</option>
                            <option <?php if ($row_edit_pendidikan['akreditasi'] == "Tidak Terakreditasi")
                                echo "selected"; ?>>Tidak Terakreditasi</option>
                        </select>

                        <label>Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" value="<?php echo $row_edit_pendidikan['tgl_mulai']; ?>">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" value="<?php echo $row_edit_pendidikan['tgl_selesai']; ?>">
                        <label>Negara</label>
                        <input type="text" name="negara" value="<?php echo $row_edit_pendidikan['negara']; ?>">
                        <label>Provinsi</label>
                        <input type="text" name="provinsi" value="<?php echo $row_edit_pendidikan['provinsi']; ?>">
                        <label>Kota / Kabupaten</label>
                        <input type="text" name="kota" value="<?php echo $row_edit_pendidikan['kota']; ?>">

                        <div style="display:flex;align-items:center;gap:8px;margin-top:12px;">
                            <input type="checkbox" name="terakhir" value="Ya" style="width:16px;height:16px;" <?php if ($row_edit_pendidikan['pendidikan_terakhir'] == "Ya")
                                echo "checked"; ?>>
                            <label style="margin:0;">Pendidikan terakhir saat ini</label>
                        </div>

                        <br><br>
                        <button class="btn" name="update_pendidikan"><i class="fa fa-save"></i> Update</button>
                        <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=pendidikan"
                            class="btn">Batal</a>
                    </form>
                </div>
            </div>

            <!-- ✅ JavaScript untuk Toggle Field pada Form Edit -->
            <script>
                function toggleFieldsEdit() {
                    const tingkat = document.getElementById('tingkat_edit').value;
                    const jurusanField = document.getElementById('jurusanField_edit');
                    const ipkField = document.getElementById('ipkField_edit');
                    const jurusanInput = document.getElementById('jurusanInput_edit');
                    const ipkInput = document.getElementById('ipkInput_edit');

                    // Reset semua field
                    jurusanField.style.display = 'none';
                    ipkField.style.display = 'none';
                    jurusanInput.required = false;
                    ipkInput.required = false;

                    // Logika berdasarkan jenjang
                    const kuliah = ['D3', 'D4', 'S1', 'S2', 'S3'];
                    const kejuruan = ['SMK'];

                    if (kuliah.includes(tingkat)) {
                        // Kuliah: Tampilkan Jurusan + IPK
                        jurusanField.style.display = 'block';
                        ipkField.style.display = 'block';
                        jurusanInput.required = true;
                        ipkInput.required = true;
                        jurusanInput.placeholder = "Contoh: Teknik Informatika";
                    }
                    else if (kejuruan.includes(tingkat)) {
                        // SMK: Tampilkan Jurusan, Sembunyikan IPK
                        jurusanField.style.display = 'block';
                        ipkField.style.display = 'none';
                        jurusanInput.required = true;
                        ipkInput.required = false;
                        jurusanInput.placeholder = "Contoh: Teknik Komputer dan Jaringan";
                    }
                    else {
                        // SD/SMP/SMA/MI/MTs/MA: Sembunyikan Jurusan + IPK
                        jurusanField.style.display = 'none';
                        ipkField.style.display = 'none';
                        jurusanInput.required = false;
                        ipkInput.required = false;
                    }
                }

                // Jalankan saat halaman load untuk set initial state
                document.addEventListener('DOMContentLoaded', function () {
                    if (document.getElementById('tingkat_edit')) {
                        toggleFieldsEdit();
                    }
                });
            </script>
        <?php } ?>

        <?php if (isset($_GET['add_sertifikasi'])) { ?>
            <div class="overlay-edit">
                <div class="edit-box">
                    <h3>Tambah Sertifikasi</h3>
                    <form method="POST">
                        <label>Nama Sertifikat</label>
                        <input type="text" name="nama_sertifikat" required>
                        <label>Lembaga Sertifikasi</label>
                        <input type="text" name="lembaga" required>
                        <label>Tahun Sertifikat</label>
                        <input type="date" name="tahun_sertifikat" required>
                        <label>Tahun Berlaku</label>
                        <input type="date" name="tahun_berlaku">
                        <label>Skor</label>
                        <input type="text" name="skor" required>
                        <br><br>
                        <button class="btn" name="simpan_sertifikasi">Simpan</button>
                        <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=sertifikasi"
                            class="btn">Batal</a>
                    </form>
                </div>
            </div>
        <?php } ?>

        <?php
        $edit_sertifikasi = $_GET['edit_sertifikasi'] ?? "";
        if ($edit_sertifikasi != "") {
            $data_edit = mysqli_query($con, "SELECT * FROM tb_sertifikasi WHERE id_sertifikasi='$edit_sertifikasi'");
            $row_edit = mysqli_fetch_assoc($data_edit);
            ?>
            <div class="overlay-edit">
                <div class="edit-box">
                    <h3>Edit Sertifikasi</h3>
                    <form method="POST">
                        <input type="hidden" name="id_sertifikasi" value="<?php echo $row_edit['id_sertifikasi']; ?>">
                        <label>Nama Sertifikat</label>
                        <input type="text" name="nama_sertifikat" value="<?php echo $row_edit['nama_sertifikat']; ?>">
                        <label>Lembaga</label>
                        <input type="text" name="lembaga" value="<?php echo $row_edit['lembaga']; ?>">
                        <label>Tahun Sertifikat</label>
                        <input type="date" name="tahun_sertifikat" value="<?php echo $row_edit['tahun_sertifikat']; ?>">
                        <label>Tahun Berlaku</label>
                        <input type="date" name="tahun_berlaku" value="<?php echo $row_edit['tahun_berlaku']; ?>">
                        <label>Skor</label>
                        <input type="text" name="skor" value="<?php echo $row_edit['skor']; ?>">
                        <br><br>
                        <button class="btn" name="update_sertifikasi">Update</button>
                        <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=sertifikasi"
                            class="btn">Batal</a>
                    </form>
                </div>
            </div>
        <?php } ?>

        <?php if (isset($_GET['add_organisasi'])) { ?>
            <div class="overlay-edit">
                <div class="edit-box">
                    <h3>Tambah Organisasi</h3>
                    <form method="POST">
                        <label>Nama Organisasi</label>
                        <input type="text" name="nama_organisasi" required>
                        <label>Posisi</label>
                        <input type="text" name="posisi" required>
                        <label>Lokasi</label>
                        <input type="text" name="lokasi">
                        <label>Dari</label>
                        <input type="date" name="tahun_mulai">
                        <label>Sampai</label>
                        <input type="date" name="tahun_selesai">
                        <label>Keterangan</label>
                        <textarea name="keterangan"></textarea>
                        <br><br>
                        <button class="btn" name="simpan_organisasi">Simpan</button>
                        <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=organisasi"
                            class="btn">Batal</a>
                    </form>
                </div>
            </div>
        <?php } ?>

        <?php if ($edit_organisasi != "") { ?>
            <div class="overlay-edit">
                <div class="edit-box">
                    <h3>Edit Organisasi</h3>
                    <form method="POST">
                        <input type="hidden" name="id_organisasi"
                            value="<?php echo $row_edit_organisasi['id_organisasi']; ?>">
                        <label>Nama Organisasi</label>
                        <input type="text" name="nama_organisasi"
                            value="<?php echo $row_edit_organisasi['nama_organisasi']; ?>" required>
                        <label>Posisi</label>
                        <input type="text" name="posisi" value="<?php echo $row_edit_organisasi['posisi']; ?>" required>
                        <label>Lokasi</label>
                        <input type="text" name="lokasi" value="<?php echo $row_edit_organisasi['lokasi']; ?>">
                        <label>Dari</label>
                        <input type="date" name="tahun_mulai" value="<?php echo $row_edit_organisasi['tahun_mulai']; ?>">
                        <label>Sampai</label>
                        <input type="date" name="tahun_selesai"
                            value="<?php echo $row_edit_organisasi['tahun_selesai']; ?>">
                        <label>Keterangan</label>
                        <textarea name="keterangan"><?php echo $row_edit_organisasi['keterangan']; ?></textarea>
                        <br><br>
                        <button class="btn" name="update_organisasi">Update</button>
                        <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa ?>&tab=organisasi" class="btn">
                            Batal
                        </a>
                    </form>
                </div>
            </div>
        <?php } ?>

        <?php if (isset($_GET['add_pengalaman'])) { ?>
            <div class="overlay-edit">
                <div class="edit-box">
                    <h3>Tambah Pengalaman Kerja</h3>
                    <form method="POST">
                        <input type="hidden" name="id_pengalaman">
                        <label>Nama Perusahaan</label>
                        <input type="text" name="nama_perusahaan" required>
                        <label>Posisi</label>
                        <input type="text" name="posisi" required>
                        <label>Level Jabatan *</label>
                        <select name="level_jabatan" required>
                            <option value="">Pilih</option>
                            <option value="Internship">Internship</option>
                            <option value="Entry Level">Entry Level</option>
                            <option value="Junior">Junior</option>
                            <option value="Mid-Level">Mid-Level</option>
                            <option value="Senior">Senior</option>
                            <option value="Manager">Manager</option>
                            <option value="Director">Director</option>
                        </select>
                        <label>Status Pegawai *</label>
                        <select name="status_pegawai" required>
                            <option value="">Pilih status</option>
                            <option value="PWT/Pekerja Waktu Tidak Tertentu (Permanent)">Permanent</option>
                            <option value="PWT/Pekerja Waktu Tertentu (Contract)">Contract</option>
                            <option value="Internship/Magang">Magang</option>
                            <option value="Freelance">Freelance</option>
                        </select>
                        <label>Negara</label>
                        <input type="text" name="negara">
                        <label>Provinsi</label>
                        <input type="text" name="provinsi">
                        <label>Kota/Kabupaten</label>
                        <input type="text" name="kota">
                        <label>Kecamatan *</label>
                        <input type="text" name="kecamatan">
                        <label>Industri *</label>
                        <select name="industri" id="industri" required style="width:100%;">
                            <option value="" disabled selected>Pilih</option>
                            <option>Teknologi Informasi</option>
                            <option>Perbankan & Keuangan</option>
                            <option>Pendidikan</option>
                            <option>Kesehatan & Rumah Sakit</option>
                            <option>Manufaktur</option>
                            <option>Retail</option>
                            <option>Perhotelan & Pariwisata</option>
                            <option>Konstruksi</option>
                            <option>Transportasi & Logistik</option>
                            <option>Telekomunikasi</option>
                            <option>Energi & Pertambangan</option>
                            <option>Pertanian & Perkebunan</option>
                            <option>Perikanan & Kelautan</option>
                            <option>Media & Hiburan</option>
                            <option>Otomotif</option>
                            <option>Farmasi</option>
                            <option>Asuransi</option>
                            <option>Konsultan</option>
                            <option>Startup</option>
                            <option>E-commerce</option>
                            <option>Properti</option>
                            <option>Pemerintahan</option>
                            <option>Non-Profit / NGO</option>
                            <option>Keamanan</option>
                            <option>Desain & Kreatif</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                        <div id="input_lainnya" style="display:none; margin-top:10px;">
                            <label>Isi Industri Lainnya *</label>
                            <input type="text" name="industri_lainnya" style="width:100%;">
                        </div>
                        <label>Dari *</label>
                        <input type="date" name="tanggal_mulai">
                        <label>Sampai *</label>
                        <input type="date" name="tanggal_selesai">
                        <label
                            style="display:flex;align-items:center;gap:10px;margin:12px 0;font-size:14px;cursor:pointer;">
                            <input type="checkbox" name="saat_ini" value="Ya"
                                style="width:16px;height:16px;cursor:pointer;">
                            <span style="position:relative; top:-4px;">Pekerjaan Saat Ini</span>
                        </label>
                        <label>Mata Uang *</label>
                        <select name="mata_uang" required>
                            <option value="" disabled selected>Pilih</option>
                            <option value="IDR">IDR</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                            <option value="JPY">JPY</option>
                            <option value="SGD">SGD</option>
                            <option value="MYR">MYR</option>
                            <option value="CNY">CNY</option>
                            <option value="KRW">KRW</option>
                            <option value="AUD">AUD</option>
                            <option value="CAD">CAD</option>
                            <option value="CHF">CHF</option>
                            <option value="HKD">HKD</option>
                            <option value="THB">THB</option>
                            <option value="PHP">PHP</option>
                            <option value="INR">INR</option>
                            <option value="SAR">SAR</option>
                            <option value="AED">AED</option>
                        </select>
                        <label>Gaji *</label>
                        <input type="number" name="gaji">
                        <label>Deskripsi Pekerjaan</label>
                        <textarea name="deskripsi"
                            style="width:100%;padding:10px;border-radius:7px;border:1px solid #ddd;margin-bottom:12px;box-sizing:border-box;resize:vertical;"></textarea>
                        <label>Alasan Berhenti</label>
                        <textarea name="alasan"
                            style="width:100%;padding:10px;border-radius:7px;border:1px solid #ddd;margin-bottom:12px;box-sizing:border-box;resize:vertical;"></textarea>
                        <label>Fasilitas *</label>
                        <textarea name="fasilitas"
                            style="width:100%;padding:10px;border-radius:7px;border:1px solid #ddd;margin-bottom:12px;box-sizing:border-box;resize:vertical;"></textarea>
                        <label>Nama Referensi *</label>
                        <input type="text" name="nama_referensi">
                        <label>Kontak Referensi *</label>
                        <input type="text" name="kontak_referensi">
                        <label>Hubungan dengan saudara/i (di luar hubungan keluarga kandung) *</label>
                        <select name="hubungan_referensi" required>
                            <option value="" disabled selected>Pilih</option>
                            <option>Atasan Langsung (Direct Report)</option>
                            <option>Personalia/SDM/HR</option>
                            <option>Manager</option>
                            <option>Dosen Pembimbing</option>
                            <option>Kepala Penelitian</option>
                            <option>Kepala Project</option>
                            <option>Guru</option>
                        </select>
                        <br><br>
                        <button class="btn" name="simpan_pengalaman">Simpan</button>
                        <a href="?halaman=profile_peserta&id_siswa=<?= $id_siswa ?>&tab=pengalaman" class="btn">Batal</a>
                    </form>
                </div>
            </div>
        <?php } ?>

        <?php if ($edit_pengalaman != "") { ?>
            <?php
            $industri_value = "";
            if ($edit_pengalaman != "") {
                $industri_value = $row_edit_pengalaman['industri'];
            }
            $list_industri = [
                "Teknologi Informasi",
                "Manufaktur",
                "Kesehatan",
                "Pendidikan",
                "Perbankan",
                "Retail"
            ];
            $is_lainnya = !in_array($industri_value, $list_industri);
            ?>
            <div class="overlay-edit">
                <div class="edit-box">
                    <h3>Edit Pengalaman Kerja</h3>
                    <form method="POST">
                        <input type="hidden" name="id_pengalaman" value="<?= $row_edit_pengalaman['id_pengalaman']; ?>">
                        <label>Nama Perusahaan</label>
                        <input type="text" name="nama_perusahaan" value="<?= $row_edit_pengalaman['nama_perusahaan']; ?>">
                        <label>Posisi</label>
                        <input type="text" name="posisi" value="<?= $row_edit_pengalaman['posisi']; ?>">
                        <label>Level Jabatan</label>
                        <input type="text" name="level_jabatan" value="<?= $row_edit_pengalaman['level_jabatan']; ?>">
                        <label>Status Pegawai</label>
                        <input type="text" name="status_pegawai" value="<?= $row_edit_pengalaman['status_pegawai']; ?>">
                        <label>Negara</label>
                        <input type="text" name="negara" value="<?= $row_edit_pengalaman['negara']; ?>">
                        <label>Provinsi</label>
                        <input type="text" name="provinsi" value="<?= $row_edit_pengalaman['provinsi']; ?>">
                        <label>Kota/Kabupaten</label>
                        <input type="text" name="kota" value="<?= $row_edit_pengalaman['kota']; ?>">
                        <label>Kecamatan *</label>
                        <input type="text" name="kecamatan" value="<?= $row_edit_pengalaman['kecamatan']; ?>">
                        <label>Industri *</label>
                        <select name="industri" id="industri">
                            <option value="">Pilih</option>
                            <?php foreach ($list_industri as $i) { ?>
                                <option value="<?= $i ?>" <?= $industri_value == $i ? 'selected' : '' ?>><?= $i ?></option>
                            <?php } ?>
                            <option value="lainnya" <?= $is_lainnya ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                        <div id="input_lainnya" style="<?= $is_lainnya ? '' : 'display:none;' ?>">
                            <input type="text" name="industri_lainnya" value="<?= $is_lainnya ? $industri_value : '' ?>"
                                placeholder="Isi industri lainnya">
                        </div>
                        <label>Dari *</label>
                        <input type="date" name="dari" value="<?= $row_edit_pengalaman['tanggal_mulai']; ?>">
                        <label>Sampai *</label>
                        <input type="date" name="sampai" value="<?= $row_edit_pengalaman['tanggal_selesai']; ?>">
                        <label style="display:flex;align-items:left;gap:10px;margin:12px 0;font-size:14px;cursor:pointer;">
                            <input type="checkbox" name="saat_ini" value="Ya" style="width:16px;height:16px;cursor:pointer;"
                                <?php if (($row_edit_pengalaman['saat_ini'] ?? '') == 'Ya')
                                    echo 'checked'; ?>>
                            <span style="position:relative; top:1px;">Pekerjaan Saat Ini</span>
                        </label>
                        <label>Mata Uang *</label>
                        <select name="mata_uang">
                            <option>IDR</option>
                            <option>USD</option>
                        </select>
                        <label>Gaji *</label>
                        <input type="number" name="gaji" value="<?= $row_edit_pengalaman['gaji']; ?>">
                        <label>Deskripsi Pekerjaan</label>
                        <textarea name="deskripsi"
                            style="width:100%;padding:10px;border-radius:7px;border:1px solid #ddd;margin-bottom:12px;box-sizing:border-box;resize:vertical;"><?= $row_edit_pengalaman['deskripsi'] ?></textarea>
                        <label>Alasan Berhenti</label>
                        <textarea name="alasan"
                            style="width:100%;padding:10px;border-radius:7px;border:1px solid #ddd;margin-bottom:12px;box-sizing:border-box;resize:vertical;"><?= $row_edit_pengalaman['alasan'] ?></textarea>
                        <label>Fasilitas *</label>
                        <textarea name="fasilitas"
                            style="width:100%;padding:10px;border-radius:7px;border:1px solid #ddd;margin-bottom:12px;box-sizing:border-box;resize:vertical;"><?= $row_edit_pengalaman['fasilitas'] ?></textarea>
                        <label>Nama Referensi *</label>
                        <input type="text" name="nama_referensi" value="<?= $row_edit_pengalaman['nama_referensi']; ?>">
                        <label>Kontak Referensi *</label>
                        <input type="text" name="kontak_referensi" value="<?= $row_edit_pengalaman['kontak_referensi']; ?>">
                        <label>Hubungan dengan saudara/i (di luar hubungan keluarga kandung) *</label>
                        <select name="hubungan_referensi">
                            <option>Atasan Langsung (Direct Report)</option>
                            <option>Personalia/SDM/HR</option>
                            <option>Manager</option>
                            <option>Dosen Pembimbing</option>
                            <option>Kepala Penelitian</option>
                            <option>Kepala Project</option>
                            <option>Guru</option>
                        </select>
                        <br><br>
                        <button class="btn" name="simpan_pengalaman">Update</button>
                        <a href="?halaman=profile_peserta&id_siswa=<?= $id_siswa ?>&tab=pengalaman" class="btn">Batal</a>
                    </form>
                </div>
            </div>
        <?php } ?>

        <?php if (isset($_GET['edit_dokumen'])) { ?>
            <div class="overlay-edit">
                <div class="edit-box">
                    <div style="display:flex;justify-content:space-between;margin-bottom:15px;">
                        <h3>Atur dokumen pendukung</h3>
                        <a href="?halaman=profile_peserta&id_siswa=<?= $id_siswa ?>&tab=dokumen">&times;</a>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <h4>Dokumen utama</h4>
                        <!-- ================= IJAZAH ================= -->
                        <div class="upload-group">
                            <label>Ijazah *</label>
                            <div id="ijazahInputBox" style="<?= !empty($dokumen['ijazah']) ? 'display:none;' : '' ?>">
                                <input type="file" name="ijazah" id="ijazahInput">
                            </div>
                            <div id="ijazahFileBox" class="file-item"
                                style="<?= empty($dokumen['ijazah']) ? 'display:none;' : '' ?>">
                                <i class="fa fa-file-pdf-o"></i>
                                <span><?= $dokumen['ijazah'] ?></span>
                                <button type="button" onclick="hapusFile('ijazah')">×</button>
                            </div>
                        </div>
                        <!-- ================= KTP ================= -->
                        <div class="upload-group">
                            <label>KTP *</label>
                            <div id="ktpInputBox" style="<?= !empty($dokumen['ktp_file']) ? 'display:none;' : '' ?>">
                                <input type="file" name="ktp_file" id="ktpInput">
                            </div>
                            <div id="ktpFileBox" class="file-item"
                                style="<?= empty($dokumen['ktp_file']) ? 'display:none;' : '' ?>">
                                <i class="fa fa-file-pdf-o"></i>
                                <span><?= $dokumen['ktp_file'] ?></span>
                                <button type="button" onclick="hapusFile('ktp')">×</button>
                            </div>
                        </div>
                        <!-- ================= TRANSKRIP ================= -->
                        <div class="upload-group">
                            <label>Transkrip *</label>
                            <div id="transkripInputBox" style="<?= !empty($dokumen['transkrip']) ? 'display:none;' : '' ?>">
                                <input type="file" name="transkrip" id="transkripInput">
                            </div>
                            <div id="transkripFileBox" class="file-item"
                                style="<?= empty($dokumen['transkrip']) ? 'display:none;' : '' ?>">
                                <i class="fa fa-file-pdf-o"></i>
                                <span><?= $dokumen['transkrip'] ?></span>
                                <button type="button" onclick="hapusFile('transkrip')">×</button>
                            </div>
                        </div>
                        <h4>Dokumen tambahan</h4>
                        <!-- ================= DOKUMEN LAIN ================= -->
                        <div class="upload-group">
                            <label>Dokumen lainnya</label>
                            <div id="dokumenInputBox"
                                style="<?= !empty($dokumen['dokumen_lain']) ? 'display:none;' : '' ?>">
                                <input type="file" name="dokumen_lain" id="dokumenInput">
                            </div>
                            <div id="dokumenFileBox" class="file-item"
                                style="<?= empty($dokumen['dokumen_lain']) ? 'display:none;' : '' ?>">
                                <i class="fa fa-file-pdf-o"></i>
                                <span><?= !empty($dokumen['dokumen_lain']) ? $dokumen['dokumen_lain'] : '' ?></span>
                                <button type="button" onclick="hapusFile('dokumen')">×</button>
                            </div>
                        </div>
                        <br><br>
                        <button type="submit" name="simpan_dokumen" class="btn-simpan">Simpan</button>
                        <a href="?halaman=profile_peserta&id_siswa=<?= $id_siswa ?>&tab=dokumen" class="btn-batal">Batal</a>
                    </form>
                </div>
            </div>
        <?php } ?>

        <script>
            const platform = document.getElementById("platform");
            const label = document.getElementById("label_link");
            const input = document.getElementById("input_link");
            if (platform) {
                platform.addEventListener("change", function () {
                    let value = this.value;
                    if (value === "whatsapp") {
                        label.innerHTML = "Nomor WhatsApp";
                        input.placeholder = "Contoh: 628123456789";
                        input.type = "tel";
                    } else if (value === "instagram") {
                        label.innerHTML = "Username Instagram";
                        input.placeholder = "contoh: username";
                        input.type = "text";
                    } else if (value === "linkedin") {
                        label.innerHTML = "Link LinkedIn";
                        input.placeholder = "https://www.linkedin.com/in/username";
                        input.type = "url";
                    } else if (value === "github") {
                        label.innerHTML = "Link Github";
                        input.placeholder = "https://github.com/username";
                        input.type = "url";
                    } else if (value === "website") {
                        label.innerHTML = "Link Website / Portfolio";
                        input.placeholder = "https://website.com";
                        input.type = "url";
                    } else {
                        label.innerHTML = "Link / Username";
                        input.placeholder = "";
                        input.type = "text";
                    }
                });
            }
        </script>
        <script>
            document.getElementById("industri").addEventListener("change", function () {
                let box = document.getElementById("input_lainnya");
                if (this.value === "lainnya") {
                    box.style.display = "block";
                } else {
                    box.style.display = "none";
                }
            });
        </script>
        <script>
            function hapusFile(type) {
                // sembunyikan badge
                document.getElementById(type + 'FileBox').style.display = 'none';
                // tampilkan input
                let inputBox = document.getElementById(type + 'InputBox');
                inputBox.style.display = 'block';
                let input = document.getElementById(type + 'Input');
                // reset file
                input.value = '';
                // AKTIFKAN REQUIRED 🔥
                input.setAttribute('required', 'required');
            }
        </script>
        <script>
            ['ijazahInput', 'ktpInput', 'transkripInput', 'dokumenInput'].forEach(id => {
                document.getElementById(id)?.addEventListener('change', function () {
                    if (this.files.length > 0) {
                        this.removeAttribute('required'); // hilangkan required
                    }
                });
            });
        </script>
        <script>
            function scrollTab(val) {
                document.getElementById("tabNav").scrollLeft += val;
            }
        </script>
        <script>
            function scrollTab(amount) {
                const tab = document.getElementById("tabNav");
                if (!tab) return;
                tab.scrollBy({
                    left: amount,
                    behavior: "smooth"
                });
            }
        </script>

        <script>
            const tabNav = document.getElementById("tabNav");
            // 🔹 SIMPAN posisi scroll
            tabNav.addEventListener("scroll", function () {
                localStorage.setItem("tabScroll", tabNav.scrollLeft);
            });
            // 🔹 KEMBALIKAN posisi scroll setelah reload
            window.addEventListener("load", function () {
                const saved = localStorage.getItem("tabScroll");
                if (saved) {
                    tabNav.scrollLeft = saved;
                }
            });
        </script>
        <script>
            window.addEventListener("load", function () {
                const activeTab = document.querySelector(".tab-nav a.active");
                if (activeTab) {
                    activeTab.scrollIntoView({
                        behavior: "smooth",
                        inline: "center"
                    });
                }
            });
        </script>


        <script>
            function setPunyaPengalaman(status) {
                fetch(window.location.href, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "set_pengalaman=" + status
                })
                    .then(res => res.text())
                    .then(data => {
                        if (data.includes("OK")) {
                            location.reload(); // 🔥 ini yang bikin langsung berubah
                        }
                    });
            }

            function resetPilihanPengalaman() {
                fetch(window.location.href, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "reset_pengalaman=1"
                })
                    .then(res => res.text())
                    .then(data => {
                        if (data.includes("OK")) {
                            location.reload();
                        }
                    });
            }
        </script>

</body>

</html>