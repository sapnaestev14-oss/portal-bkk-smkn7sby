<?php

// Hanya Admin / Operator
if ($data_status != "Admin" && $data_status != "admin") {
    echo "<script>alert('Akses ditolak!'); window.location='login.php';</script>";
    exit;
}

// ================= TAMBAH AKUN PESERTA =================
if (isset($_POST['simpan'])) {

    // Amankan input
    $nisn = mysqli_real_escape_string($con, trim($_POST['nisn_input']));
    $password_input = trim($_POST['password_input']);

    if ($nisn == "" || $password_input == "") {
        echo "<script>alert('NISN dan Password tidak boleh kosong!');</script>";
    } else {

        $password = password_hash($password_input, PASSWORD_DEFAULT);
        $role = "siswa";

        // cek username di tb_user
        $cek = mysqli_query($con, "SELECT username FROM tb_user WHERE username='$nisn'");

        if (mysqli_num_rows($cek) > 0) {

            echo "<script>alert('Akun sudah ada');</script>";

        } else {

            // insert ke tb_user
            $insert = mysqli_query($con, "INSERT INTO tb_user (username,password,role) 
            VALUES ('$nisn','$password','$role')");

            if ($insert) {

                // 🔥 AMBIL ID USER TERAKHIR
                $id_user = mysqli_insert_id($con);

                // 🔥 CEK DI tb_siswa (BIAR TIDAK DOUBLE)
                $cek_siswa = mysqli_query($con, "SELECT * FROM tb_siswa WHERE nisn='$nisn'");

                if (mysqli_num_rows($cek_siswa) == 0) {

                    // 🔥 INSERT KE tb_siswa (WAJIB)
                    mysqli_query($con, "INSERT INTO tb_siswa (id_user, nisn) 
                    VALUES ('$id_user','$nisn')");
                }

                echo "<script>alert('✅ Akun siswa berhasil dibuat!'); window.location='?halaman=data_user';</script>";
                exit;

            } else {
                echo "<script>alert('❌ Gagal membuat akun!');</script>";
                die(mysqli_error($con));
            }
        }
    }
}



// ================= TAMBAH AKUN PERUSAHAAN =================
if (isset($_POST['simpan_perusahaan'])) {

    $username = mysqli_real_escape_string($con, trim($_POST['username_perusahaan_input']));
    $password_input = trim($_POST['password_perusahaan_input']);

    if ($username == "" || $password_input == "") {
        echo "<script>alert('Username dan Password tidak boleh kosong!');</script>";
    } else {

        $password = password_hash($password_input, PASSWORD_DEFAULT);
        $role = "perusahaan"; // ✅ WAJIB ADA

        // ✅ tabel diperbaiki
        $cek = mysqli_query($con, "SELECT username FROM tb_user WHERE username='$username'");

        if (mysqli_num_rows($cek) > 0) {

            echo "<script>alert('Akun sudah ada!');</script>";

        } else {

            // ✅ tabel & kolom diperbaiki
            $insert = mysqli_query($con, "INSERT INTO tb_user (username,password,role) 
            VALUES ('$username','$password','$role')");

            if ($insert) {

                // 🔥 AMBIL ID USER BARU
                $id_user = mysqli_insert_id($con);

                // 🔥 INSERT KE tb_perusahaan (WAJIB)
                mysqli_query($con, "INSERT INTO tb_perusahaan (id_user) VALUES ('$id_user')");

                echo "<script>alert('✅ Akun perusahaan berhasil dibuat!'); window.location='?halaman=data_user';</script>";
            } else {
                echo "<script>alert('❌ Gagal membuat akun perusahaan!');</script>";
                die(mysqli_error($con));
            }
        }
    }
}

?>

<style>
    /* Modern Form Styling */
    .modern-forms-container {
        max-width: 1200px;
        margin: -1px auto;
        padding: 0 20px;
        animation: fadeIn 0.6s ease;
    }
    
    .page-header-modern {
        text-align: center;
        margin-bottom: 50px;
    }
    
    .page-header-modern h1 {
        font-size: 36px;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 10px;
    }
    
    .page-header-modern p {
        color: #718096;
        font-size: 16px;
    }
    
    .forms-grid {
        display: grid;
        margin: -20px auto;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 30px;
        margin-bottom: 10px;
    }
    
    .modern-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }
    
    .card-header-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px 30px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .card-header-modern.perusahaan {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    
    .card-icon {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .card-title {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
    }
    
    .card-body-modern {
        padding: 35px;
    }
    
    .form-group-modern {
        margin-bottom: 25px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        color: #2d3748;
        font-weight: 600;
        font-size: 14px;
    }
    
    .form-control-modern {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #f8fafc;
    }
    
    .form-control-modern:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    
    .form-control-modern::placeholder {
        color: #a0aec0;
    }
    
    .button-group {
        display: flex;
        gap: 12px;
        margin-top: 30px;
    }
    
    .btn-modern {
        padding: 12px 28px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-success-modern {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
    }
    
    .btn-success-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
        color: white;
    }
    
    .btn-primary-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .info-box {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
        border-left: 4px solid #667eea;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        font-size: 14px;
        color: #4a5568;
    }
    
    .info-box i {
        margin-right: 8px;
        color: #667eea;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @media (max-width: 768px) {

    .modern-forms-container {
        padding: 0 2px;
    }

    .modern-card {
        width: 100%;
    }

    .card-body-modern {
        padding: 20px 15px;
    }
        .forms-grid {
            grid-template-columns: 1fr;
        }
        
        .page-header-modern h1 {
            font-size: 28px;
        }
        
        .card-body-modern {
            padding: 25px 20px;
        }
        
        .button-group {
            flex-direction: column;
        }
        
        .btn-modern {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="modern-forms-container">
    <div class="page-header-modern">
        <h1>🔐 Manajemen Akun</h1>
        <p>Kelola akun peserta dan perusahaan</p>
    </div>
    
    <div class="forms-grid">
        <!-- Form Tambah Akun Peserta -->
        <div class="modern-card">
            <div class="card-header-modern">
                <div class="card-icon">
                    <i class="fa fa-graduation-cap"></i>
                </div>
                <h3 class="card-title">Tambah Akun Peserta</h3>
            </div>
            
            <div class="card-body-modern">
                <div class="info-box">
                    <i class="fa fa-info-circle"></i>
                    Akun peserta akan otomatis terhubung dengan data siswa berdasarkan NISN
                </div>
                
                <form method="POST" autocomplete="off">
                    <input type="text" style="display:none">
                    <input type="password" style="display:none">
                    
                    <div class="form-group-modern">
                        <label class="form-label">
                            <i class="fa fa-id-card"></i> NISN
                        </label>
                        <input type="text" 
                               name="nisn_input" 
                               class="form-control-modern" 
                               placeholder="Masukkan NISN siswa"
                               readonly
                               onfocus="this.removeAttribute('readonly');" 
                               required>
                    </div>
                    
                    <div class="form-group-modern">
                        <label class="form-label">
                            <i class="fa fa-lock"></i> Password
                        </label>
                        <input type="password" 
                               name="password_input" 
                               class="form-control-modern" 
                               placeholder="Masukkan password"
                               readonly
                               onfocus="this.removeAttribute('readonly');" 
                               required>
                    </div>
                    
                    <div class="button-group">
                        <button type="submit" name="simpan" class="btn-modern btn-success-modern">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                        <a href="?halaman=data_user" class="btn-modern btn-primary-modern">
                            <i class="fa fa-arrow-left"></i> Lihat Data Akun
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Form Tambah Akun Perusahaan -->
        <div class="modern-card">
            <div class="card-header-modern perusahaan">
                <div class="card-icon">
                    <i class="fa fa-building"></i>
                </div>
                <h3 class="card-title">Tambah Akun Perusahaan</h3>
            </div>
            
            <div class="card-body-modern">
                <div class="info-box" style="border-left-color: #11998e;">
                    <i class="fa fa-info-circle" style="color: #11998e;"></i>
                    Akun perusahaan akan otomatis terhubung dengan data perusahaan
                </div>
                
                <form method="POST" autocomplete="off">
                    <input type="text" style="display:none">
                    <input type="password" style="display:none">
                    
                    <div class="form-group-modern">
                        <label class="form-label">
                            <i class="fa fa-building"></i> Username Perusahaan
                        </label>
                        <input type="text" 
                               name="username_perusahaan_input" 
                               class="form-control-modern" 
                               placeholder="Masukkan username perusahaan"
                               readonly
                               onfocus="this.removeAttribute('readonly');" 
                               required>
                    </div>
                    
                    <div class="form-group-modern">
                        <label class="form-label">
                            <i class="fa fa-lock"></i> Password
                        </label>
                        <input type="password" 
                               name="password_perusahaan_input" 
                               class="form-control-modern" 
                               placeholder="Masukkan password"
                               readonly
                               onfocus="this.removeAttribute('readonly');" 
                               required>
                    </div>
                    
                    <div class="button-group">
                        <button type="submit" name="simpan_perusahaan" class="btn-modern btn-success-modern">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                        <a href="?halaman=data_user" class="btn-modern btn-primary-modern">
                            <i class="fa fa-arrow-left"></i> Lihat Data Akun
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>