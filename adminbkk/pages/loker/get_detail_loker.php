<?php
include_once(__DIR__ . "/../../koneksi.php");

if (!isset($_GET['id'])) {
    echo "<p style='color:red;'>ID lowongan tidak ditemukan.</p>";
    exit;
}

$id = mysqli_real_escape_string($con, $_GET['id']);

// ✅ PERBAIKAN: Gunakan tabel tb_lowongan dengan JOIN tb_perusahaan
$sql = mysqli_query($con, "
    SELECT l.*, p.nama_perusahaan 
    FROM tb_lowongan l
    LEFT JOIN tb_perusahaan p ON l.id_perusahaan = p.id_perusahaan
    WHERE l.id_lowongan='$id'
");
$data = mysqli_fetch_array($sql, MYSQLI_BOTH);

if (!$data) {
    echo "<p style='color:red;'>Data tidak ditemukan.</p>";
    exit;
}

function bulletList($text)
{
    $items = preg_split('/\r\n|\r|\n/', trim($text));

    $html = "<ul style='padding-left:20px;margin:0;'>";

    foreach ($items as $item) {

        $item = trim($item);

        if ($item != '') {
            $item = ltrim($item, "•-* ");
            $html .= "<li>" . htmlspecialchars($item) . "</li>";
        }
    }

    $html .= "</ul>";

    return $html;
}
?>

<h3 style="margin-top:0;"><b><?php echo htmlspecialchars($data['judul_lowongan']); ?></b></h3>

<p style="font-size:14px; color:#444;">
    <i class="fa fa-building"></i>
    <?php echo htmlspecialchars($data['nama_perusahaan'] ?? 'Perusahaan tidak tersedia'); ?>
</p>

<p style="font-size:13px;">
    <i class="fa fa-male"></i> <b>Jenis Kelamin:</b> <?php echo htmlspecialchars($data['jekel']); ?>
</p>

<p style="font-size:13px;">
    <i class="fa fa-calendar"></i> <b>Tanggal Posting:</b>
    <?php echo isset($data['tanggal_posting']) ? date('d F Y', strtotime($data['tanggal_posting'])) : '-'; ?>
</p>

<p style="font-size:13px;">
    <i class="fa fa-clock-o"></i> <b>Batas Akhir:</b>
    <?php echo isset($data['batas_lamaran']) ? date('d F Y', strtotime($data['batas_lamaran'])) : '-'; ?>
</p>

<p style="font-size:13px;">
    <i class="fa fa-info-circle"></i> <b>Status:</b>
    <span class="label label-<?php echo $data['status'] == 'aktif' ? 'success' : 'danger'; ?>">
        <?php echo htmlspecialchars($data['status']); ?>
    </span>
</p>
<hr>
<div style="margin-top:15px; display:flex; gap:10px; flex-wrap:wrap;">
    <!-- Tombol Aktifkan -->
    <a href="?halaman=loker_konfirm&aksi=aktif&kode=<?php echo $data['id_lowongan']; ?>" 
       class="btn btn-info btn-sm"
       onclick="return confirm('Yakin ingin mengaktifkan lowongan ini?')">
        <i class="fa fa-check-circle"></i> Aktifkan
    </a>

    <!-- Tombol Arsip -->
    <a href="?halaman=loker_arsip&aksi=arsip&kode=<?php echo $data['id_lowongan']; ?>" 
       class="btn btn-warning btn-sm"
       onclick="return confirm('Yakin ingin mengarsipkan lowongan ini?')">
        <i class="fa fa-archive"></i> Arsip
    </a>

    <!-- Tombol Edit -->
    <a href="?halaman=loker_ubah&kode=<?php echo $data['id_lowongan']; ?>" 
       class="btn btn-primary btn-sm">
        <i class="fa fa-edit"></i> Edit
    </a>

    <!-- Tombol Hapus -->
    <a href="?halaman=loker_aksi&aksi=hapus&kode=<?php echo $data['id_lowongan']; ?>"
       onclick="return confirm('⚠️ Apakah anda yakin ingin menghapus lowongan ini secara permanen?')" 
       class="btn btn-danger btn-sm">
        <i class="fa fa-trash"></i> Hapus
    </a>
</div>
<hr>

<?php if (!empty($data['posisi'])) { ?>
    <h4><b>📌 Tentang Posisi</b></h4>
    <p style="text-align:justify;">
        <?php echo nl2br(htmlspecialchars($data['posisi'])); ?>
    </p>
<?php } ?>

<?php if (!empty($data['deskripsi'])) { ?>
    <h4><b>📝 Deskripsi Pekerjaan</b></h4>
    <p style="text-align:justify;">
        <?php echo bulletList(htmlspecialchars($data['deskripsi'])); ?>
    </p>
<?php } ?>

<?php if (!empty($data['kualifikasi'])) { ?>
    <h4><b>✅ Kualifikasi</b></h4>
    <p style="text-align:justify;">
        <?php echo bulletList(htmlspecialchars($data['kualifikasi'])); ?>
    </p>
<?php } ?>

<?php if (!empty($data['lokasi'])) { ?>
    <h4><b>📍 Lokasi</b></h4>
    <p style="text-align:justify;">
        <?php echo nl2br(htmlspecialchars($data['lokasi'])); ?>
    </p>
<?php } ?>

<?php if (!empty($data['jenis_pekerjaan'])) { ?>
    <h4><b>💼 Jenis Pekerjaan</b></h4>
    <p><?php echo htmlspecialchars($data['jenis_pekerjaan']); ?></p>
<?php } ?>

<?php if (!empty($data['gaji'])) { ?>
    <h4><b>💰 Gaji</b></h4>
    <p><?php echo htmlspecialchars($data['gaji']); ?></p>
<?php } ?>



<br>

<a href="?halaman=loker_tampil" class="btn btn-default btn-sm">
    <i class="fa fa-arrow-left"></i> Kembali
</a>