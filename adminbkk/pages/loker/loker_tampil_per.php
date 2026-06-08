<?php
include "koneksi.php";
session_start();

// AMBIL USER LOGIN
$id_user = $_SESSION['id_user'] ?? 0;

// AMBIL DATA PERUSAHAAN
$getUser = mysqli_query($con, "
    SELECT p.id_perusahaan, p.nama_perusahaan 
    FROM tb_perusahaan p
    JOIN tb_user u ON p.id_user = u.id_user
    WHERE u.id_user='$id_user'
");

$dataUser = mysqli_fetch_assoc($getUser);

if (!$dataUser) {
    echo "<script>alert('Data perusahaan tidak ditemukan!');</script>";
    exit;
}

$id_perusahaan = $dataUser['id_perusahaan'];
$data_nama = $dataUser['nama_perusahaan'];
?>

<div class="form-group">
    <br>
    <div class="card mb-3">
        <div class="card-header">
            <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm">Tambah Lowongan</a> 
        </div>
        <br><br>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Lowongan Kerja</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Lowongan</th>
                            <th>Nama Perusahaan</th>
                            <th>Lowongan</th>
                            <th>Keterangan</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // ✅ PERBAIKAN: Gunakan id_perusahaan untuk filter
                        $sql_tampil = "SELECT * FROM tb_lowongan WHERE id_perusahaan='$id_perusahaan' ORDER BY tanggal_posting DESC";
                        $query_tampil = mysqli_query($con, $sql_tampil);
                        
                        if (!$query_tampil) {
                            echo "<tr><td colspan='6' class='text-danger'>Error Query: " . mysqli_error($con) . "</td></tr>";
                        } else {
                            $no = 1;
                            $adaData = false;
                            
                            while ($data = mysqli_fetch_assoc($query_tampil)) {
                                $adaData = true;
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $data['id_lowongan']; ?></td>
                                    <td><?= $data_nama; ?></td>
                                    <td><?= $data['judul_lowongan']; ?></td>
                                    <td><?= isset($data['posisi']) ? $data['posisi'] : '-'; ?></td>
                                    <td>
                                        <a href="?halaman=loker_ubah&kode=<?= $data['id_lowongan']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="?halaman=loker_aksi&kode=<?= $data['id_lowongan']; ?>"
                                           onclick="return confirm('Yakin hapus data?')"
                                           class="btn btn-danger btn-sm">
                                           <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                            }
                            
                            if (!$adaData) {
                                echo "<tr><td colspan='6' class='text-center text-muted'>Belum ada lowongan yang dibuat.</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL -->
    <div id="myModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Tambah Lowongan Baru</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Judul Lowongan</label>
                            <input type="text" class="form-control" name="txtjudul_lowongan" placeholder="Contoh: Staff Administrasi" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="txtjekel" class="form-control" required>
                                <option value="">- Pilih -</option>
                                <option>Pria</option>
                                <option>Wanita</option>
                                <option>Pria / Wanita</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Tentang Posisi</label>
                            <textarea class="form-control" name="txtposisi" rows="3" placeholder="Deskripsi singkat tentang posisi..." required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Deskripsi Pekerjaan</label>
                            <textarea class="form-control" name="txtdeskripsi" rows="4" placeholder="Tugas dan tanggung jawab..." required></textarea>
                            <small style="color:#777;">
                            <i class="fa fa-info-circle"></i>
                            Isi setiap poin pada baris baru (tekan Enter per poin).
                        </small>
                        </div>
                        
                        <div class="form-group">
                            <label>Kualifikasi</label>
                            <textarea class="form-control" name="txtkualifikasi" rows="4" placeholder="Persyaratan kandidat..." required></textarea>
                        <small style="color:#777;">
                            <i class="fa fa-info-circle"></i>
                            Isi setiap poin pada baris baru (tekan Enter per poin).
                        </small>
                        </div>
                        
                        <div class="form-group">
                            <label>Tanggal Posting</label>
                            <input type="date" class="form-control" name="txttanggal_posting" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Batas Lamaran</label>
                            <input type="date" class="form-control" name="txtbatas_lamaran" required>
                        </div>
                        
                        <!-- Hidden fields -->
                        <input type="hidden" name="id_perusahaan" value="<?= $id_perusahaan; ?>">
                        <input type="hidden" name="txtsumber" value="<?= $data_nama; ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" name="btnSimpan" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// ==========================
// PROSES SIMPAN
// ==========================
if (isset($_POST['btnSimpan'])) {
    // CEK VERIFIKASI
    $cekVerif = mysqli_query($con, "
        SELECT p.status_verifikasi 
        FROM tb_perusahaan p
        JOIN tb_user u ON p.id_user = u.id_user
        WHERE u.id_user='$id_user'
    ");
    
    $dataVerif = mysqli_fetch_assoc($cekVerif);
    
    if (!$dataVerif || strtolower($dataVerif['status_verifikasi']) != "terverifikasi") {
        echo "<script>
            alert('Perusahaan belum diverifikasi Admin BKK');
            window.location='?halaman=profile_perusahaan';
        </script>";
        exit;
    }
    
    // =====================
    // LANJUT SIMPAN
    // =====================
    $id_perusahaan = mysqli_real_escape_string($con, $_POST['id_perusahaan']);
    $judul = mysqli_real_escape_string($con, $_POST['txtjudul_lowongan']);
    $jekel = mysqli_real_escape_string($con, $_POST['txtjekel']);
    $posisi = mysqli_real_escape_string($con, $_POST['txtposisi']);
    $deskripsi = mysqli_real_escape_string($con, $_POST['txtdeskripsi']);
    $kualifikasi = mysqli_real_escape_string($con, $_POST['txtkualifikasi']);
    $tanggal = mysqli_real_escape_string($con, $_POST['txttanggal_posting']);
    $batas = mysqli_real_escape_string($con, $_POST['txtbatas_lamaran']);
    $sumber = mysqli_real_escape_string($con, $_POST['txtsumber']);
    
    $simpan = mysqli_query($con, "
        INSERT INTO tb_lowongan
        (id_perusahaan, judul_lowongan, jekel, posisi, deskripsi, kualifikasi, tanggal_posting, batas_lamaran, status, sumber)
        VALUES
        ('$id_perusahaan', '$judul', '$jekel', '$posisi', '$deskripsi', '$kualifikasi', '$tanggal', '$batas', 'aktif', '$sumber')
    ");
    
    if ($simpan) {
        echo "<script>
            alert('Data berhasil ditambahkan');
            window.location='?halaman=loker_tambah_per';
        </script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($con) . "');</script>";
    }
}
?>