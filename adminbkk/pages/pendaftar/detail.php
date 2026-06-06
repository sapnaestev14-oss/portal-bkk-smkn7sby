<?php
include_once("koneksi.php");

$tampil = [];

if(isset($_GET['kode'])){

    $kode = $_GET['kode'];

    $sql = $con->query("
        SELECT
            tb_lamaran.*,
            tb_siswa.nama,
            tb_siswa.nisn,
            tb_lowongan.judul_lowongan
        FROM tb_lamaran
        INNER JOIN tb_siswa
            ON tb_lamaran.nisn = tb_siswa.nisn
        INNER JOIN tb_lowongan
            ON tb_lamaran.id_lowongan = tb_lowongan.id_lowongan
        WHERE tb_lamaran.id_lamaran = '$kode'
    ");

    $tampil = $sql->fetch_assoc();
}
?>

<br>

<div class="card mb-3">

    <div class="card-header">
        <i class="fa fa-user"></i>
        <b>Data Lengkap Pendaftar :
            <?php echo $tampil['nama']; ?>
        </b>
    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-striped">

                <tbody>

                    <tr>
                        <td width="20%">ID Lamaran</td>
                        <td>: <?php echo $tampil['id_lamaran']; ?></td>
                    </tr>

                    <tr>
                        <td>NISN</td>
                        <td>: <?php echo $tampil['nisn']; ?></td>
                    </tr>

                    <tr>
                        <td>Nama Pendaftar</td>
                        <td>: <?php echo $tampil['nama']; ?></td>
                    </tr>

                    <tr>
                        <td>Lowongan</td>
                        <td>: <?php echo $tampil['judul_lowongan']; ?></td>
                    </tr>

                    <tr>
                        <td>Berkas</td>
                        <td>
                            <?php echo $tampil['berkas']; ?>

                            <?php
                            if(!empty($tampil['berkas'])){
                            ?>
                                <a href="pages/pendaftar/download.php?filename=<?php echo $tampil['berkas']; ?>" class="btn btn-sm btn-success ml-2">
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