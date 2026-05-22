<?php
include_once("koneksi.php");

// ======================================================
// VALIDASI PARAMETER
// ======================================================

if (!isset($_GET['kode']) || empty($_GET['kode'])) {

    echo "<script>
        alert('❌ Data tidak ditemukan!');
        window.location.href='?halaman=super_tampil';
    </script>";

    exit;
}

$username_cek = mysqli_real_escape_string($con, $_GET['kode']);

// ======================================================
// AMBIL DATA USER
// ======================================================

$sql_cek = mysqli_query($con, "
    SELECT * FROM tb_user 
    WHERE username = '$username_cek'
");

if (!$sql_cek || mysqli_num_rows($sql_cek) == 0) {

    echo "<script>
        alert('❌ User tidak ditemukan!');
        window.location.href='?halaman=super_tampil';
    </script>";

    exit;
}

$data_cek = mysqli_fetch_assoc($sql_cek);

// ======================================================
// AMANKAN DATA NULL UNTUK PHP 8 / RAILWAY
// ======================================================

$username = htmlspecialchars($data_cek['username'] ?? '');
$nama     = htmlspecialchars($data_cek['nama'] ?? '');
$email    = htmlspecialchars($data_cek['email'] ?? '');
$role     = $data_cek['role'] ?? '';

?>

<div class="form-group">

    <br>

    <div class="card mb-3">

        <div class="box box-primary">

            <div class="box-header with-border">
                <h3 class="box-title">
                    ✏️ Ubah Data User
                </h3>
            </div>

            <div class="box-body">

                <form action="?halaman=super_aksi" method="post">

                    <!-- USERNAME -->
                    <div class="form-group">

                        <label>
                            Username (NIP/NISN)
                        </label>

                        <input 
                            type="text"
                            class="form-control"
                            name="txtusername"
                            value="<?= $username; ?>"
                            readonly
                            style="background:#f8f9fa; cursor:not-allowed;"
                        >

                        <small class="text-muted">
                            Username tidak dapat diubah.
                        </small>

                    </div>

                    <!-- NAMA -->
                    <div class="form-group">

                        <label>
                            Nama Lengkap 
                            <span class="text-danger">*</span>
                        </label>

                        <input 
                            type="text"
                            class="form-control"
                            name="txtnama"
                            value="<?= $nama; ?>"
                            required
                        >

                    </div>

                    <!-- PASSWORD -->
                    <div class="form-group">

                        <label>Password Baru</label>

                        <input 
                            type="password"
                            class="form-control"
                            name="txtpassword"
                            placeholder="Kosongkan jika tidak ingin mengubah password"
                        >

                        <small class="text-muted">
                            Biarkan kosong jika tidak ingin mengganti password.
                        </small>

                    </div>

                    <!-- EMAIL -->
                    <div class="form-group">

                        <label>
                            Email 
                            <span class="text-danger">*</span>
                        </label>

                        <input 
                            type="email"
                            class="form-control"
                            name="txtemail"
                            value="<?= $email; ?>"
                            required
                        >

                    </div>

                    <!-- ROLE -->
                    <div class="form-group">

                        <label>
                            Role Pengguna 
                            <span class="text-danger">*</span>
                        </label>

                        <select 
                            name="rbstatus"
                            class="form-control"
                            required
                        >

                            <option value="admin"
                                <?= ($role == 'admin') ? 'selected' : ''; ?>>
                                Admin / Ka. BKK
                            </option>

                            <option value="perusahaan"
                                <?= ($role == 'perusahaan') ? 'selected' : ''; ?>>
                                Perusahaan / CV
                            </option>

                            <option value="siswa"
                                <?= ($role == 'siswa') ? 'selected' : ''; ?>>
                                Siswa / Alumni
                            </option>

                        </select>

                    </div>

                    <!-- BUTTON -->
                    <div class="form-group">

                        <button 
                            type="submit"
                            class="btn btn-warning btn-sm"
                            name="btnUBAH"
                        >
                            <i class="fa fa-save"></i>
                            Simpan Perubahan
                        </button>

                        <a 
                            href="?halaman=super_tampil"
                            class="btn btn-secondary btn-sm"
                        >
                            <i class="fa fa-times"></i>
                            Batal
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>