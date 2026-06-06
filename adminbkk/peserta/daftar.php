<?php
include_once("../koneksi.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nisn = isset($_SESSION['ses_nisn']) ? $_SESSION['ses_nisn'] : '';

if (empty($nisn)) {
    echo "<script>alert('Silakan login!'); window.location='../login.php';</script>";
    exit;
}

// ✅ Ambil ID lowongan dari GET atau POST
$id_lowongan = isset($_GET['kode']) ? mysqli_real_escape_string($con, $_GET['kode']) : 
               (isset($_POST['txtkode_loker']) ? mysqli_real_escape_string($con, $_POST['txtkode_loker']) : '');

$data_cek = null;

// ✅ Hanya cek lowongan jika ada ID dan bukan hasil POST
if (!empty($id_lowongan) && !isset($_POST['btnDaftar'])) {
    $sql_cek = "SELECT l.*, p.nama_perusahaan 
                FROM tb_lowongan l
                JOIN tb_perusahaan p ON l.id_perusahaan = p.id_perusahaan
                WHERE l.id_lowongan = '$id_lowongan' AND l.status = 'aktif'";
    
    $query_cek = mysqli_query($con, $sql_cek);
    
    if ($query_cek && mysqli_num_rows($query_cek) > 0) {
        $data_cek = mysqli_fetch_array($query_cek, MYSQLI_BOTH);
    } else {
        echo "<script>alert('Lowongan tidak ditemukan!'); window.location='?halaman=loker';</script>";
        exit;
    }
}

// ✅ PROSES UPLOAD & SIMPAN (TAMBAHKAN INI)
if (isset($_POST['btnDaftar'])) {
    
    // Ambil data siswa untuk cek profil & id_siswa
    $sql_profil = mysqli_query($con, "SELECT * FROM tb_siswa WHERE nisn='$nisn'");
    $profil = mysqli_fetch_assoc($sql_profil);
    $id_siswa = $profil['id_siswa'];
    
    // Cek tracer study
    $sql_tracer = mysqli_query($con, "SELECT * FROM tb_tracer WHERE id_siswa='$id_siswa'");
    $tracer = mysqli_fetch_assoc($sql_tracer);
    
    // Hitung kelengkapan profil (100% required)
    $persen = 0;
    if (!empty($profil['nik'])) $persen += 10;
    if (!empty($profil['nama'])) $persen += 10;
    if (!empty($profil['alamat'])) $persen += 10;
    if (!empty($profil['no_hp'])) $persen += 10;
    if (!empty($profil['deskripsi'])) $persen += 10;
    if (!empty($profil['email'])) $persen += 10;
    if (!empty($profil['status_perkawinan'])) $persen += 10;
    if (!empty($profil['foto'])) $persen += 10;
    if ($tracer && !empty($tracer['id_tracer'])) $persen += 20;
    
   if ($persen < 100) {

    $id_siswa = $profil['id_siswa'];

    echo "<script>
        alert('⚠️ Profil belum lengkap (".$persen."%). Lengkapi profil terlebih dahulu!');
        window.location='?halaman=profile_peserta&id_siswa=".$id_siswa."&tab=personal';
    </script>";
    exit;
}
    
    // Cek apakah sudah melamar
    $cek_lamaran = mysqli_query($con, "SELECT * FROM tb_lamaran WHERE id_siswa='$id_siswa' AND id_lowongan='$id_lowongan'");
    if (mysqli_num_rows($cek_lamaran) > 0) {
        echo "<script>alert('ℹ️ Anda sudah melamar lowongan ini!'); window.location='?halaman=daftar';</script>";
        exit;
    }
    
    // ✅ Proses upload file
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        
        $allowed = ['pdf', 'doc', 'docx'];
        $ext = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            echo "<script>alert('Format file tidak diperbolehkan! Gunakan PDF, DOC, atau DOCX'); window.history.back();</script>";
            exit;
        }
        
        if ($_FILES['cv']['size'] > 5 * 1024 * 1024) {
            echo "<script>alert('Ukuran file terlalu besar! Maksimal 5MB'); window.history.back();</script>";
            exit;
        }
        
        $namaFile = time() . "_" . $nisn . "_" . $_FILES['cv']['name'];
        $dirUpload = "../pages/pendaftar/upload/";
        
        if (!is_dir($dirUpload)) {
            mkdir($dirUpload, 0777, true);
        }
        
        if (move_uploaded_file($_FILES['cv']['tmp_name'], $dirUpload . $namaFile)) {
            
            $tanggal = date('Y-m-d');
            
            // ✅ Insert ke tb_lamaran (kolom 'cv' sesuai database)
            $sql_insert = "INSERT INTO tb_lamaran 
                (id_lowongan, id_siswa, cv, status, tanggal_lamaran, created_at) 
                VALUES 
                ('$id_lowongan', '$id_siswa', '$namaFile', 'Diproses', '$tanggal', NOW())";
            
            if (mysqli_query($con, $sql_insert)) {
                echo "<script>
                    alert('✅ Lamaran berhasil dikirim!');
                    window.location='?halaman=pendaftar';
                </script>";
                exit;
            } else {
                echo "<script>alert('❌ Gagal simpan: " . mysqli_error($con) . "'); window.history.back();</script>";
                exit;
            }
            
        } else {
            echo "<script>alert('❌ Gagal upload file!'); window.history.back();</script>";
            exit;
        }
        
    } else {
        echo "<script>alert('⚠️ Pilih file CV terlebih dahulu!'); window.history.back();</script>";
        exit;
    }
}
?>

<style>
    /* === CSS ANDA TETAP SAMA (tidak diubah) === */
    .daftar-container { max-width: 1500px; margin: 0 auto; padding: 20px; }
    .card-modern { background: #ffffff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); overflow: hidden; border: none; }
    .card-modern-header { background: linear-gradient(135deg, #226de6 0%, #8596e2 100%); padding: 30px; text-align: center; color: white; }
    .card-modern-header .icon { font-size: 48px; margin-bottom: 10px; display: block; }
    .card-modern-header h3 { margin: 0; font-size: 24px; font-weight: 600; }
    .card-modern-header .job-title { margin-top: 8px; font-size: 14px; opacity: 0.9; font-weight: 500; }
    .card-modern-body { padding: 30px; }
    .form-group-modern { margin-bottom: 20px; }
    .form-group-modern label { display: block; font-weight: 600; color: #2c3e50; margin-bottom: 8px; font-size: 14px; }
    .form-group-modern label i { margin-right: 8px; color: #4facfe; }
    .form-control-modern { width: 100%; padding: 12px 15px; border: 2px solid #e8ecf1; border-radius: 10px; font-size: 14px; transition: all 0.3s ease; background: #f8f9fa; color: #333; }
    .form-control-modern:focus { outline: none; border-color: #4facfe; background: #ffffff; box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1); }
    .form-control-modern[readonly] { background: #f1f3f5; cursor: not-allowed; color: #555; }
    .upload-area { border: 2px dashed #4facfe; border-radius: 12px; padding: 25px; text-align: center; background: #f8faff; transition: all 0.3s ease; }
    .upload-area:hover { border-color: #00f2fe; background: #f0f8ff; }
    .upload-area .upload-icon { font-size: 36px; color: #4facfe; margin-bottom: 10px; }
    .upload-area .upload-text { font-size: 14px; color: #555; margin-bottom: 10px; }
    .upload-area .format-info { font-size: 12px; color: #888; font-style: italic; }
    .upload-area input[type="file"] { margin-top: 15px; padding: 8px; }
    #fileName { margin-top: 10px; font-size: 13px; color: #4facfe; font-weight: 600; }
    .btn-group-modern { display: flex; gap: 12px; justify-content: center; margin-top: 30px; flex-wrap: wrap; }
    .btn-modern { padding: 12px 30px; border: none; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
    .btn-submit-modern { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
    .btn-submit-modern:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(79, 172, 254, 0.3); color: white; }
    .btn-cancel-modern { background: #e8ecf1; color: #555; }
    .btn-cancel-modern:hover { background: #dde2e8; transform: translateY(-2px); color: #333; }
    .box.box-primary { background: transparent !important; box-shadow: none !important; border: none !important; }
    .box-header.with-border { display: none !important; }
    .box-body { padding: 0 !important; }
    @media (max-width: 991px) { .daftar-container { padding: 15px; } .card-modern-header { padding: 25px 20px; } .card-modern-body { padding: 25px 20px; } }
    @media (max-width: 767px) { .daftar-container { padding: 10px; } .card-modern { border-radius: 12px; } .card-modern-header { padding: 20px 15px; } .card-modern-header .icon { font-size: 40px; } .card-modern-header h3 { font-size: 20px; } .card-modern-body { padding: 20px 15px; } .form-group-modern { margin-bottom: 15px; } .btn-group-modern { flex-direction: column; } .btn-modern { width: 100%; justify-content: center; } }
    @media (max-width: 480px) { .card-modern-header h3 { font-size: 18px; } .form-control-modern { font-size: 13px; padding: 10px 12px; } .upload-area { padding: 20px 15px; } }
</style>

<div class="daftar-container">
    <div class="card-modern">
        <!-- Header -->
        <div class="card-modern-header">
            <span class="icon">📋</span>
            <h3>Daftar Lowongan Kerja</h3>
            <div class="job-title">
                <?php echo isset($data_cek['judul_lowongan']) ? htmlspecialchars($data_cek['judul_lowongan']) : ''; ?>
            </div>
        </div>
        
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Lowongan Kerja</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            
            <div class="box-body">
                <div class="card-modern-body">
                    <!-- ✅ Form action="" (submit ke file ini sendiri) -->
                    <form action="" method="post" enctype="multipart/form-data">
                        
                        <!-- Kode Lowongan -->
                        <div class="form-group-modern">
                            <label><i class="fa fa-hashtag"></i> Kode Lowongan</label>
                            <input type="text" class="form-control-modern" name="txtkode_loker" 
                                value="<?php echo isset($data_cek['id_lowongan']) ? htmlspecialchars($data_cek['id_lowongan']) : ''; ?>" 
                                required readonly>
                        </div>

                        <!-- Nama Perusahaan -->
                        <div class="form-group-modern">
                            <label><i class="fa fa-building"></i> Nama Perusahaan</label>
                            <input type="text" class="form-control-modern" name="txtnm_perusahaan" 
                                value="<?php echo isset($data_cek['nama_perusahaan']) ? htmlspecialchars($data_cek['nama_perusahaan']) : ''; ?>" 
                                required readonly>
                        </div>
                        
                        <!-- NISN -->
                        <div class="form-group-modern">
                            <label><i class="fa fa-user"></i> NISN</label>
                            <input type="text" class="form-control-modern" name="txtnisn" 
                                value="<?php echo htmlspecialchars($nisn); ?>" 
                                required readonly>
                        </div>
                        
                        <!-- Posisi Lowongan -->
                        <div class="form-group-modern">
                            <label><i class="fa fa-briefcase"></i> Posisi Lowongan</label>
                            <input type="text" class="form-control-modern" name="txtnm_loker" 
                                value="<?php echo isset($data_cek['judul_lowongan']) ? htmlspecialchars($data_cek['judul_lowongan']) : ''; ?>" 
                                required readonly>
                        </div>
                        
                        <!-- Berkas Lamaran -->
                        <div class="form-group-modern">
                            <label><i class="fa fa-file-pdf"></i> Berkas Lamaran</label>
                            <div class="upload-area">
                                <div class="upload-icon">📎</div>
                                <div class="upload-text">Pilih file lamaran Anda</div>
                                <div class="format-info">Format: NIM_NAMA_PT_POSISI.pdf</div>
                                <!-- ✅ GANTI name="berkas" JADI name="cv" -->
                                <input type="file" name="cv" accept=".pdf,.doc,.docx" required onchange="updateFileName(this)" />
                                <div id="fileName"></div>
                            </div>
                        </div>

                        <!-- Tombol -->
                        <div class="btn-group-modern">
                            <button type="submit" class="btn-modern btn-submit-modern" name="btnDaftar">
                                <i class="fa fa-paper-plane"></i> Kirim Lamaran
                            </button>
                            <a href="?halaman=loker" class="btn-modern btn-cancel-modern">
                                <i class="fa fa-times"></i> Batal
                            </a>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateFileName(input) {
    var fileName = input.files[0] ? input.files[0].name : '';
    document.getElementById('fileName').textContent = fileName ? '✓ File: ' + fileName : '';
}
</script>