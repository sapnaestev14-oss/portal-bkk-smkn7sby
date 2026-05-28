<?php
include_once("../koneksi.php");
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    * {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .modern-schedule-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 30px 20px;
        min-height: calc(100vh - 100px);
        margin-top: -20px;
    }

    .modern-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .modern-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 35px 30px;
        position: relative;
        overflow: hidden;
    }

    .modern-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: pulse 4s ease-in-out infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
            opacity: 0.5;
        }

        50% {
            transform: scale(1.1);
            opacity: 0.8;
        }
    }

    .header-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .header-icon {
        width: 70px;
        height: 70px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .header-title h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -0.5px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .header-title p {
        margin: 8px 0 0 0;
        font-size: 15px;
        opacity: 0.95;
        font-weight: 400;
    }

    .modern-body {
        padding: 35px;
    }

    .schedule-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .schedule-table thead th {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8ecf1 100%);
        color: #2d3748;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 20px 15px;
        border: none;
        text-align: left;
        position: sticky;
        top: 0;
    }

    .schedule-table thead th:first-child {
        border-radius: 16px 0 0 0;
        padding-left: 25px;
    }

    .schedule-table thead th:last-child {
        border-radius: 0 16px 0 0;
        padding-right: 25px;
    }

    .schedule-table tbody tr {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
    }

    .schedule-table tbody tr:hover {
        background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
    }

    .schedule-table tbody td {
        padding: 22px 15px;
        border-bottom: 1px solid #e8ecf1;
        color: #555;
        font-size: 14px;
        vertical-align: middle;
    }

    .schedule-table tbody tr:last-child td {
        border-bottom: none;
    }

    .schedule-table tbody td:first-child {
        padding-left: 25px;
        font-weight: 700;
        color: #667eea;
    }

    .schedule-table tbody td:last-child {
        padding-right: 25px;
    }

    .code-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 13px;
        display: inline-block;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .company-name {
        font-weight: 700;
        color: #2d3748;
        font-size: 15px;
    }

    .job-title {
        color: #667eea;
        font-weight: 600;
        font-size: 14px;
    }

    .date-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        box-shadow: 0 4px 12px rgba(79, 172, 254, 0.3);
    }

    .location-text {
        color: #555;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .time-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
        border-radius: 10px;
        font-weight: 700;
        font-size: 13px;
        box-shadow: 0 4px 12px rgba(250, 112, 154, 0.3);
    }

    .empty-state {
        text-align: center;
        padding: 100px 20px;
        background: linear-gradient(135deg, #f8f9ff 0%, #e8ecf1 100%);
        border-radius: 20px;
        margin: 20px 0;
    }

    .empty-state-icon {
        width: 140px;
        height: 140px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
        font-size: 60px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .empty-state h3 {
        color: #2d3748;
        margin: 0 0 12px 0;
        font-size: 24px;
        font-weight: 700;
    }

    .empty-state p {
        color: #777;
        font-size: 15px;
        margin: 0;
    }

    .dataTables_wrapper {
        margin-top: 25px;
    }

    .dataTables_filter input {
        border: 2px solid #e8ecf1;
        border-radius: 12px;
        padding: 10px 18px;
        margin-left: 10px;
        transition: all 0.3s;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .dataTables_filter input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .dataTables_length select {
        border: 2px solid #e8ecf1;
        border-radius: 12px;
        padding: 10px 18px;
        margin-right: 10px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .dataTables_info {
        color: #777;
        font-size: 13px;
        padding-top: 20px;
    }

    .pagination .page-link {
        border: none;
        color: #667eea;
        padding: 10px 16px;
        margin: 0 3px;
        border-radius: 10px;
        transition: all 0.3s;
        font-weight: 600;
    }

    .pagination .page-link:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    @media (max-width: 768px) {
        .modern-header {
            padding: 25px 20px;
        }

        .header-content {
            flex-direction: column;
            text-align: center;
        }

        .modern-body {
            padding: 20px;
            overflow-x: auto;
        }

        .schedule-table {
            min-width: 900px;
        }

        .header-title h2 {
            font-size: 22px;
        }
    }

    .table-responsive {
        border-radius: 16px;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    
</style>

<div class="modern-schedule-container">
    <div class="modern-card">
        <div class="modern-header">
            <div class="header-content">
                <div class="header-icon">📅</div>
                <div class="header-title">
                    <h2>Jadwal Tes Seleksi</h2>
                    <p>Informasi jadwal tes dari perusahaan yang Anda lamar</p>
                </div>
            </div>
        </div>

        <div class="modern-body">
            <div class="table-responsive">
                <table id="example1" class="schedule-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode Jadwal</th>
                            <th>Nama Perusahaan</th>
                            <th>Lowongan</th>
                            <th>Tanggal</th>
                            <th>Tempat</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // PERBAIKAN QUERY SESUAI DATABASE BARU
                       // ambil username login
$username = $_SESSION['ses_username'] ?? '';

// ambil data siswa
$get_siswa = mysqli_query($con, "
    SELECT id_siswa 
    FROM tb_siswa 
    WHERE nisn='$username'
");

$data_siswa = mysqli_fetch_assoc($get_siswa);

$id_siswa = $data_siswa['id_siswa'] ?? 0;

// query jadwal khusus lowongan yang dilamar siswa
$sql_tampil = "
SELECT 
    tb_jadwal.id_jadwal,
    tb_perusahaan.nama_perusahaan,
    tb_lowongan.judul_lowongan,
    tb_jadwal.tanggal,
    tb_jadwal.lokasi,
    tb_jadwal.waktu

FROM tb_jadwal

INNER JOIN tb_lowongan 
ON tb_jadwal.id_lowongan = tb_lowongan.id_lowongan

INNER JOIN tb_perusahaan 
ON tb_jadwal.id_perusahaan = tb_perusahaan.id_perusahaan

INNER JOIN tb_lamaran 
ON tb_lowongan.id_lowongan = tb_lamaran.id_lowongan

WHERE tb_lamaran.id_siswa = '$id_siswa'
AND tb_jadwal.status != 'dibatalkan'

ORDER BY tb_jadwal.tanggal ASC
";

                        $query_tampil = mysqli_query($con, $sql_tampil);

                        if ($query_tampil && mysqli_num_rows($query_tampil) > 0) {
                            $no = 1;
                            while ($data = mysqli_fetch_array($query_tampil, MYSQLI_BOTH)) {
                                ?>
                                <tr>
                                    <td><strong><?= $no++; ?></strong></td>
                                    <td><span class="code-badge">#<?= htmlspecialchars($data['id_jadwal']); ?></span></td>
                                    <td><span class="company-name">🏢 <?= htmlspecialchars($data['nama_perusahaan']); ?></span>
                                    </td>
                                    <td><span class="job-title">💼 <?= htmlspecialchars($data['judul_lowongan']); ?></span></td>
                                    <td>
                                        <span class="date-badge">
                                            <i class="fa fa-calendar"></i>
                                            <?= date('d F Y', strtotime($data['tanggal'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="location-text">
                                            <i class="fa fa-map-marker" style="color: #667eea;"></i>
                                            <?= htmlspecialchars($data['lokasi']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="time-badge">
                                            <i class="fa fa-clock-o"></i>
                                            <?= date('H:i', strtotime($data['waktu'])); ?> WIB
                                        </span>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">📭</div>
                                        <h3>Belum Ada Jadwal</h3>
                                        <p>Belum ada jadwal tes seleksi yang tersedia saat ini.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        if ($.fn.DataTable.isDataTable('#example1')) {
            $('#example1').DataTable().destroy();
        }

        $('#example1').DataTable({
            'paging': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'responsive': true,
            'pageLength': 10,
            'order': [[4, 'asc']], // Sort by date ascending
            'language': {
                'search': '🔍 Cari:',
                'lengthMenu': 'Tampilkan _MENU_ data',
                'zeroRecords': '😕 Tidak ada jadwal ditemukan',
                'emptyTable': '📭 Tidak ada data tersedia',
                'info': 'Menampilkan _START_ - _END_ dari _TOTAL_ jadwal',
                'infoEmpty': 'Menampilkan 0 jadwal',
                'infoFiltered': '(difilter dari _MAX_ total jadwal)',
                'paginate': {
                    'first': '⏮️',
                    'last': '⏭️',
                    'next': '▶️',
                    'previous': '◀️'
                }
            }
        });
    });
</script>