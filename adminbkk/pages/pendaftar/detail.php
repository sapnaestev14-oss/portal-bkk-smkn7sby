<?php
include_once("koneksi.php");

// cek parameter kode
if (!isset($_GET['kode']) || empty($_GET['kode'])) {
    echo "<script>
        alert('Data pendaftar tidak ditemukan!');
        window.location='?halaman=pendaftar_tampil';
    </script>";
    exit;
}

$id_pendaftaran = mysqli_real_escape_string($con, $_GET['kode']);

// query data pendaftar
$sql = mysqli_query($con, "
    SELECT 
        l.id_lamaran,
        s.nisn,
        s.nama,
        p.nama_perusahaan,
        lw.judul_lowongan,
        l.berkas
    FROM tb_lamaran l
    JOIN tb_siswa s 
        ON l.id_siswa = s.id_siswa
    JOIN tb_lowongan lw 
        ON l.id_lowongan = lw.id_lowongan
    JOIN tb_perusahaan p 
        ON lw.id_perusahaan = p.id_perusahaan
    WHERE l.id_lamaran = '$id_pendaftaran'
");

$tampil = mysqli_fetch_assoc($sql);

// validasi data
if (!$tampil) {
    echo "<script>
        alert('Data tidak ditemukan!');
        window.location='?halaman=pendaftar_tampil';
    </script>";
    exit;
}
?>

<br>

<div class="card mb-3">

    <div class="card-header">
        <i class="fa fa-table"></i>
        <b>Data Lengkap Pelamar : <?php echo htmlspecialchars($tampil['nama']); ?></b>
    </div>

    <div class="card-body">

        <div class="panel-body">

            <div class="table-responsive">

                <table class="table table-striped">

                    <tbody>

                        <tr>
                            <td>ID Lamaran</td>
                            <td width="80%">
                                : <?php echo htmlspecialchars($tampil['id_lamaran']); ?>
                            </td>
                        </tr>

                        <tr>
                            <td>NISN</td>
                            <td>
                                : <?php echo htmlspecialchars($tampil['nisn']); ?>
                            </td>
                        </tr>

                        <tr>
                            <td>Nama Pelamar</td>
                            <td>
                                : <?php echo htmlspecialchars($tampil['nama']); ?>
                            </td>
                        </tr>

                        <tr>
                            <td>Perusahaan</td>
                            <td>
                                : <?php echo htmlspecialchars($tampil['nama_perusahaan']); ?>
                            </td>
                        </tr>

                        <tr>
                            <td>Lowongan</td>
                            <td>
                                : <?php echo htmlspecialchars($tampil['judul_lowongan']); ?>
                            </td>
                        </tr>

                        <tr>
                            <td>Berkas</td>
                            <td>
                                : <?php echo htmlspecialchars($tampil['berkas']); ?>

                                <?php if (!empty($tampil['berkas'])) { ?>
                                    &nbsp;

                                    <a href="pages/pendaftar/download.php?filename=<?php echo urlencode($tampil['berkas']); ?>">
                                        <i class="fa fa-download"></i> Download
                                    </a>

                                <?php } ?>
                            </td>
                        </tr>

                    </tbody>

                </table>

                <a href="?halaman=pendaftar_tampil" class="btn btn-primary">
                    Kembali
                </a>

            </div>

        </div>

    </div>

</div>