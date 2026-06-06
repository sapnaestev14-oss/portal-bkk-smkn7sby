<?php
include_once("koneksi.php");

$tampil = array();

if (isset($_GET['kode'])) {

    $kode = mysqli_real_escape_string($con, $_GET['kode']);

    $sql = $con->query("
        SELECT
            tb_lamaran.*,
            tb_siswa.nama,
            tb_siswa.nisn,
            tb_lowongan.judul_lowongan
        FROM tb_lamaran
        INNER JOIN tb_siswa
            ON tb_lamaran.id_siswa = tb_siswa.id_siswa
        INNER JOIN tb_lowongan
            ON tb_lamaran.id_lowongan = tb_lowongan.id_lowongan
        WHERE tb_lamaran.id_lamaran = '$kode'
    ");

    if ($sql && $sql->num_rows > 0) {
        $tampil = $sql->fetch_assoc();
    }
}
?>

<br>

<div class="card mb-3">

    <div class="card-header">
        <i class="fa fa-user"></i>
        <b>
            Data Lengkap Pendaftar
            <?php
            if (!empty($tampil)) {
                echo ": " . $tampil['nama'];
            }
            ?>
        </b>
    </div>

    <div class="card-body">

        <?php if (!empty($tampil)) { ?>

            <div class="table-responsive">

                <table class="table table-striped">

                    <tbody>

                        <tr>
                            <td width="25%">ID Lamaran</td>
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
                            <td>Tanggal Lamaran</td>
                            <td>: <?php echo $tampil['tanggal_lamaran']; ?></td>
                        </tr>

                        <tr>
                            <td>Status</td>
                            <td>: <?php echo $tampil['status']; ?></td>
                        </tr>

                        <tr>
                            <td>Catatan</td>
                            <td>: <?php echo !empty($tampil['catatan']) ? $tampil['catatan'] : '-'; ?></td>
                        </tr>

                        <tr>
                            <td>CV</td>
                            <td>
                                <?php
                                if (!empty($tampil['cv'])) {
                                ?>
                                    <a href="../uploads/cv/<?php echo $tampil['cv']; ?>" target="_blank" class="btn btn-success btn-sm">
                                        <i class="fa fa-download"></i> Download CV
                                    </a>
                                <?php
                                } else {
                                    echo "-";
                                }
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>Surat Lamaran</td>
                            <td>
                                <?php
                                if (!empty($tampil['surat_lamaran'])) {
                                ?>
                                    <a href="../uploads/surat_lamaran/<?php echo $tampil['surat_lamaran']; ?>" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="fa fa-download"></i> Download Surat Lamaran
                                    </a>
                                <?php
                                } else {
                                    echo "-";
                                }
                                ?>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        <?php } else { ?>

            <div class="alert alert-warning">
                Data lamaran tidak ditemukan.
            </div>

        <?php } ?>

        <a href="?halaman=pendaftar_tampil" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>

    </div>

</div>