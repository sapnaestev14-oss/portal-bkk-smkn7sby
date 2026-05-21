<?php
include_once("../../koneksi.php");

// ambil id lowongan
$id_lowongan = isset($_POST['txttahun']) 
    ? mysqli_real_escape_string($con, $_POST['txttahun']) 
    : '';

if (empty($id_lowongan)) {
    die("ID lowongan tidak ditemukan");
}

// ======================================================================
// DATA LOWONGAN & PERUSAHAAN
// ======================================================================

$sql_loker = mysqli_query($con, "
    SELECT 
        lw.id_lowongan,
        lw.judul_lowongan,
        p.nama_perusahaan
    FROM tb_lowongan lw
    JOIN tb_perusahaan p 
        ON lw.id_perusahaan = p.id_perusahaan
    WHERE lw.id_lowongan = '$id_lowongan'
");

$dat = mysqli_fetch_assoc($sql_loker);

if (!$dat) {
    die("Data lowongan tidak ditemukan");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <title>Cetak Data Pelamar</title>

    <style>

        body{
            font-family: Arial, sans-serif;
            font-size:12px;
            color:black;
        }

        table{
            border-collapse:collapse;
            width:100%;
        }

        th, td{
            border:1px solid black;
            padding:6px;
        }

        th{
            background:#f2f2f2;
        }

    </style>

</head>

<body>

    <table border="0" style="width:100%; border:none;">
        <tr>

            <td style="width:120px; border:none;">
                <center>
                    <img src="../../img/logo_smkn7.png" width="90">
                </center>
            </td>

            <td style="border:none;">
                <center>

                    <h2 style="margin:0;">
                        BURSA KERJA KHUSUS (BKK)
                    </h2>

                    <h3 style="margin:0;">
                        SMK NEGERI 7 SURABAYA
                    </h3>

                    Jl. Pawiyatan No. 2 Surabaya
                    <br>
                    Jawa Timur

                </center>
            </td>

        </tr>
    </table>

    <hr style="border:2px solid black;">

    <center>

        <h3>
            DAFTAR PELAMAR LOWONGAN KERJA
        </h3>

        <h4>
            <?php echo htmlspecialchars($dat['nama_perusahaan']); ?>
            -
            <?php echo htmlspecialchars($dat['judul_lowongan']); ?>
        </h4>

    </center>

    <br>

    <table>

        <thead>

            <tr>
                <th>No</th>
                <th>ID Lamaran</th>
                <th>NISN</th>
                <th>Nama Pelamar</th>
                <th>Lowongan</th>
                <th>Tanda Tangan</th>
            </tr>

        </thead>

        <tbody>

        <?php

        $no = 1;

        $sql_tampil = mysqli_query($con, "
            SELECT 
                l.id_lamaran,
                s.nisn,
                s.nama,
                lw.judul_lowongan
            FROM tb_lamaran l
            JOIN tb_siswa s 
                ON l.id_siswa = s.id_siswa
            JOIN tb_lowongan lw 
                ON l.id_lowongan = lw.id_lowongan
            WHERE lw.id_lowongan = '$id_lowongan'
            ORDER BY l.id_lamaran ASC
        ");

        while($data = mysqli_fetch_assoc($sql_tampil)) {

        ?>

            <tr>

                <td style="text-align:center;">
                    <?php echo $no++; ?>
                </td>

                <td style="text-align:center;">
                    <?php echo htmlspecialchars($data['id_lamaran']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($data['nisn']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($data['nama']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($data['judul_lowongan']); ?>
                </td>

                <td style="height:40px;"></td>

            </tr>

        <?php } ?>

        </tbody>

    </table>

    <br><br>

    <div style="width:300px; float:right; text-align:center;">

        Surabaya, <?php echo date("d-m-Y"); ?>
        <br><br>

        Kepala BKK SMKN 7 Surabaya

        <br><br><br><br>

        <b><u>Arif Syaifudin, ST</u></b>

    </div>

<script>
window.print();
</script>

</body>
</html>