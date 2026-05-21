<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
include_once("koneksi.php");

// ==================== HANDLE UPDATE STATUS ====================
if (isset($_POST['update']) && isset($_GET['id'])) {
  $id_lamaran = mysqli_real_escape_string($con, $_GET['id']);
  $status_baru = mysqli_real_escape_string($con, $_POST['status']);

  $update_query = mysqli_query($con, "UPDATE tb_lamaran SET status = '$status_baru' WHERE id_lamaran = '$id_lamaran'");

  if ($update_query) {
    header("Location: ?halaman=pendaftar_tampil&updated=1");
    exit();
  } else {
    echo "<script>alert('Gagal update status: " . mysqli_error($con) . "');</script>";
  }
}

// ==================== AJAX HANDLER: GET PROFILE DATA ====================
if (isset($_GET['halaman']) && $_GET['halaman'] == 'get_profile_data' && isset($_GET['id_siswa'])) {
  header('Content-Type: application/json');

  $id_siswa = mysqli_real_escape_string($con, $_GET['id_siswa']);
  $response = ['success' => false, 'data' => [], 'message' => ''];

  $sql_siswa = mysqli_query($con, "SELECT * FROM tb_siswa WHERE id_siswa = '$id_siswa' LIMIT 1");

  if (!$sql_siswa) {
    echo json_encode(['success' => false, 'message' => mysqli_error($con)]);
    exit;
  }

  if ($sql_siswa && mysqli_num_rows($sql_siswa) > 0) {
    $response['data']['siswa'] = mysqli_fetch_assoc($sql_siswa);

    $sql_pendidikan = mysqli_query($con, "SELECT * FROM tb_pendidikan WHERE id_siswa = '$id_siswa' ORDER BY FIELD(tingkat, 'SD', 'SMP', 'SMA/SMK', 'Lainnya')");
    $response['data']['pendidikan'] = [];
    while ($row = mysqli_fetch_assoc($sql_pendidikan)) {
      $response['data']['pendidikan'][] = $row;
    }

    $sql_pengalaman = mysqli_query($con, "SELECT * FROM tb_pengalaman WHERE id_siswa = '$id_siswa' ORDER BY tanggal_mulai DESC");
    $response['data']['pengalaman'] = [];
    while ($row = mysqli_fetch_assoc($sql_pengalaman)) {
      $response['data']['pengalaman'][] = $row;
    }

    $sql_sertifikasi = mysqli_query($con, "SELECT * FROM tb_sertifikasi WHERE id_siswa = '$id_siswa' ORDER BY tahun_sertifikat DESC");
    $response['data']['sertifikasi'] = [];
    while ($row = mysqli_fetch_assoc($sql_sertifikasi)) {
      $response['data']['sertifikasi'][] = $row;
    }

    $sql_organisasi = mysqli_query($con, "SELECT * FROM tb_organisasi WHERE id_siswa = '$id_siswa' ORDER BY tahun_mulai DESC");
    $response['data']['organisasi'] = [];
    while ($row = mysqli_fetch_assoc($sql_organisasi)) {
      $response['data']['organisasi'][] = $row;
    }

    $id_user = $response['data']['siswa']['id_user'] ?? null;
    $response['data']['sosial_media'] = [];
    if ($id_user) {
      $sql_sosmed = mysqli_query($con, "SELECT * FROM tb_sosial_media WHERE id_user = '$id_user'");
      while ($row = mysqli_fetch_assoc($sql_sosmed)) {
        $response['data']['sosial_media'][] = $row;
      }
    }

    $sql_keluarga = mysqli_query($con, "SELECT * FROM tb_keluarga WHERE id_siswa = '$id_siswa'");
    $response['data']['keluarga'] = [];
    while ($row = mysqli_fetch_assoc($sql_keluarga)) {
      $response['data']['keluarga'][] = $row;
    }

    $sql_dokumen = mysqli_query($con, "SELECT * FROM tb_dokumen WHERE id_siswa = '$id_siswa' LIMIT 1");
    $response['data']['dokumen'] = ($sql_dokumen && mysqli_num_rows($sql_dokumen) > 0) ? mysqli_fetch_assoc($sql_dokumen) : null;

    $response['success'] = true;
    $response['message'] = 'Data berhasil diambil';
  } else {
    $response['message'] = 'Data siswa tidak ditemukan';
  }

  echo json_encode($response, JSON_UNESCAPED_UNICODE);
  exit;
}

$data_status = isset($_SESSION['ses_level']) ? $_SESSION['ses_level'] : '';
$id_perusahaan = '';
$nama_perusahaan_session = '';

if ($data_status == "perusahaan") {
  if (isset($_SESSION['id_user'])) {
    $id_user = mysqli_real_escape_string($con, $_SESSION['id_user']);
    $q = mysqli_query($con, "SELECT id_perusahaan, nama_perusahaan FROM tb_perusahaan WHERE id_user='$id_user'");
    if ($q && mysqli_num_rows($q) > 0) {
      $r = mysqli_fetch_assoc($q);
      $id_perusahaan = $r['id_perusahaan'];
      $nama_perusahaan_session = $r['nama_perusahaan'];
    }
  } elseif (isset($_SESSION['ses_id_perusahaan'])) {
    $id_perusahaan = $_SESSION['ses_id_perusahaan'];
    $nama_perusahaan_session = $_SESSION['ses_nama'] ?? '';
  }
}
?>

<?php
// ==================== BAGIAN ADMIN / KA. BKK ====================
if ($data_status == "admin" || $data_status == "Ka. BKK" || $data_status == "") {
  ?>
  <style>

*{
  margin:0;
  padding:0;
  box-sizing:border-box;
}

html,
body{
  width:100%;
  overflow-x:hidden;
  font-family:'Segoe UI',sans-serif;
  background:#f4f6f9;
}

/* WRAPPER */
.form-group{
  width:100%;
  padding:8px;
}

/* CARD */
.card{
  width:100%;
  max-width:1000%;
  border:none !important;
  border-radius:20px !important;
  box-shadow:0 5px 20px rgba(0,0,0,0.08) !important;
  background:#fff !important;
  overflow:hidden;
  margin:-50px auto 0 auto !important;
}

/* HEADER */
.card-header{
  display:flex;
  flex-wrap:wrap;
  gap:10px;
  align-items:center;
  justify-content:flex-start;
  background:linear-gradient(135deg,#667eea 0%,#764ba2 100%) !important;
  padding:20px !important;
  border:none !important;
}

/* BUTTON */
.btn{
  border-radius:10px !important;
  font-weight:600 !important;
  transition:0.3s ease;
}

.btn-primary{
  background:linear-gradient(135deg,#667eea 0%,#764ba2 100%) !important;
  border:none !important;
  padding:10px 18px !important;
}

.btn-primary:hover{
  transform:translateY(-2px);
}

.btn-sm{
  margin:2px;
}

/* BOX */
.box{
  border:none !important;
  box-shadow:none !important;
  background:transparent !important;
}

/* BOX HEADER */
.box-header{
  padding:20px;
  border-bottom:1px solid #eee;
}

.box-title{
  font-size:22px;
  font-weight:700;
  color:#1e293b;
}

/* TABLE WRAPPER */
.box-body{
  width:100%;
  overflow-x:auto;
  padding:15px;
}

/* TABLE */
.table{
  width:100% !important;
  min-width:900px;
  border-collapse:collapse !important;
  margin-bottom:0 !important;
}

/* TABLE HEADER */
.table thead th{
  background:#eef2ff !important;
  color:#1e293b !important;
  font-weight:700 !important;
  border:none !important;
  padding:14px !important;
  text-align:center;
  white-space:nowrap;
}

/* TABLE BODY */
.table tbody td{
  padding:14px !important;
  vertical-align:middle !important;
  border-color:#e5e7eb !important;
  font-size:14px;
}

.table tbody tr:hover{
  background:#f8fafc !important;
}

/* MODAL */
.modal-content{
  border:none !important;
  border-radius:18px !important;
  overflow:hidden;
}

.modal-header{
  background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
  color:white;
  border:none !important;
}

.modal-title{
  font-weight:700;
}

/* FORM */
.form-control{
  border-radius:10px !important;
  height:45px !important;
  border:1px solid #d1d5db !important;
  box-shadow:none !important;
}

.form-control:focus{
  border-color:#667eea !important;
  box-shadow:0 0 0 0.15rem rgba(102,126,234,.2) !important;
}

/* ACTION BUTTON */
td .btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
}

/* MOBILE */
@media(max-width:768px){

  .form-group{
    padding:8px;
  }

  /* CARD */
.card{
  width:100%;
  max-width:100%;
  border:none !important;
  border-radius:20px !important;
  box-shadow:0 5px 20px rgba(0,0,0,0.08) !important;
  background:#fff !important;
  overflow:hidden;
  margin:-35px auto 0 auto !important;
}

  .card-header{
    flex-direction:column;
    align-items:stretch;
  }

  .card-header .btn{
    width:100%;
  }

  .box-title{
    font-size:18px;
  }

  .table{
    min-width:800px;
  }

  .modal-dialog{
    width:95%;
    margin:20px auto;
  }

}

</style>

  <div class="form-group">
    <br>
    <div class="card mb-3">
      <div class="card-header">
        <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#cetak1">
          <i class="fa fa-book"></i> Lowongan Kerja
        </button>
        <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#cetak">
          <i class="fa fa-book"></i> Cetak Daftar
        </button>
      </div>
      <br>
      <div class="modal fade" id="cetak" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="myModalLabel">Lowongan</h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="post" action="./pages/pendaftar/cetak_daftar.php"
                enctype="multipart/form-data" target="_blank">
                <fieldset>
                  <div class="form-group">
                    <label for="inputnim" class="col-lg-2 control-label">Lowongan</label>
                    <div class="col-lg-10">
                      <select class="form-control" name="txttahun" id="txttahun">
                        <option value="">- Pilih -</option>
                        <?php
                        $sql_loker = mysqli_query($con, "SELECT DISTINCT lw.id_lowongan, p.nama_perusahaan, lw.judul_lowongan FROM tb_lowongan lw LEFT JOIN tb_perusahaan p ON lw.id_perusahaan = p.id_perusahaan ORDER BY lw.judul_lowongan ASC");
                        while ($data_loker = mysqli_fetch_array($sql_loker)) {
                          echo '<option value="' . htmlspecialchars($data_loker['id_lowongan']) . '">' . htmlspecialchars($data_loker['judul_lowongan']) . ' - ' . htmlspecialchars($data_loker['nama_perusahaan']) . '</option>';
                        } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-2">
                      <button type="reset" class="btn btn-default" data-dismiss="modal">Batal</button>
                      <button type="submit" id="cetak" name="cetak" class="btn btn-primary">Cetak</button>
                    </div>
                  </div>
                </fieldset>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="cetak1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="myModalLabel">Lowongan</h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                <fieldset>
                  <div class="form-group">
                    <label for="inputnim" class="col-lg-2 control-label">Lowongan</label>
                    <div class="col-lg-10">
                      <select class="form-control" name="txttahun" id="txttahun">
                        <option value="">- Pilih -</option>
                        <?php
                        $sql_tahun = mysqli_query($con, "SELECT DISTINCT lw.id_lowongan, p.nama_perusahaan, lw.judul_lowongan FROM tb_lowongan lw LEFT JOIN tb_perusahaan p ON lw.id_perusahaan = p.id_perusahaan ORDER BY lw.judul_lowongan ASC");
                        while ($data_tahun = mysqli_fetch_array($sql_tahun)) {
                          echo '<option value="' . htmlspecialchars($data_tahun['id_lowongan']) . '">' . htmlspecialchars($data_tahun['judul_lowongan']) . ' - ' . htmlspecialchars($data_tahun['nama_perusahaan']) . '</option>';
                        } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-2">
                      <button type="reset" class="btn btn-default" data-dismiss="modal">Batal</button>
                      <button type="submit" value="Pilih" class="btn btn-primary">Lihat</button>
                    </div>
                  </div>
                </fieldset>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Data Lamaran</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div class="table-responsive">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>No</th>
                <th>No. Pendaftaran</th>
                <th>NISN</th>
                <th>Nama</th>
                <th>Perusahaan</th>
                <th>Loker</th>
                <th>Berkas</th>
                <!-- <th>Pilihan</th> -->
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              $query = isset($_POST['txttahun']) ? mysqli_real_escape_string($con, $_POST['txttahun']) : '';
              if ($query != '') {
                $sql_tampil = mysqli_query($con, "SELECT l.id_lamaran, s.id_siswa, s.nisn, s.nama, lw.judul_lowongan, p.nama_perusahaan, l.cv, l.status FROM tb_lamaran l JOIN tb_siswa s ON l.id_siswa = s.id_siswa JOIN tb_lowongan lw ON l.id_lowongan = lw.id_lowongan JOIN tb_perusahaan p ON lw.id_perusahaan = p.id_perusahaan WHERE lw.id_lowongan = '$query' ORDER BY l.id_lamaran ASC");
              } else {
                $sql_tampil = mysqli_query($con, "SELECT l.id_lamaran, s.id_siswa, s.nisn, s.nama, lw.judul_lowongan, p.nama_perusahaan, l.cv, l.status FROM tb_lamaran l JOIN tb_siswa s ON l.id_siswa = s.id_siswa JOIN tb_lowongan lw ON l.id_lowongan = lw.id_lowongan JOIN tb_perusahaan p ON lw.id_perusahaan = p.id_perusahaan ORDER BY l.id_lamaran DESC");
              }
              if (!$sql_tampil) {
                echo "<tr><td colspan='8' class='text-danger'>Query Error: " . mysqli_error($con) . "</td></tr>";
              } elseif (mysqli_num_rows($sql_tampil) == 0) {
                echo "<tr><td colspan='8' align='center'>Data tidak ada</td></tr>";
              } else {
                while ($data = mysqli_fetch_array($sql_tampil)) {
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($data['id_lamaran']); ?></td>
                    <td><?php echo isset($data['nisn']) ? htmlspecialchars($data['nisn']) : '-'; ?></td>
                    <td><?php echo htmlspecialchars($data['nama']); ?></td>
                    <td><?php echo htmlspecialchars($data['nama_perusahaan']); ?></td>
                    <td><?php echo htmlspecialchars($data['judul_lowongan']); ?></td>
                    <td><?php if (!empty($data['cv'])): ?><a
                          href="pages/pendaftar/download.php?filename=<?= urlencode($data['cv']); ?>" target="_blank"
                          class="btn btn-info btn-sm"><i class="fa fa-download"></i> Download</a><?php else: ?> -
                      <?php endif; ?>
                    </td>
                    <!-- <td>
                      <a href="?halaman=pendaftar_detail&kode=<?php echo $data['id_lamaran']; ?>"
                        class='btn btn-warning btn-sm'><i class="fa fa-link"></i></a>
                      <a href="?halaman=pendaftar_ubah&kode=<?php echo $data['id_lamaran']; ?>"
                        class='btn btn-warning btn-sm'><i class="fa fa-edit"></i></a>
                      <a href="?halaman=pendaftar_aksi&aksi=hapus&kode=<?php echo $data['id_lamaran']; ?>"
                        onclick="return confirm('Apakah anda yakin hapus data ini ?')" class='btn btn-danger btn-sm'><i
                          class="fa fa-trash"></i></a>
                    </td> -->
                  </tr>
                  <?php
                }
              }
              ?>
            </tbody>
          </table>
          </div>
        </div>
      </div>
    </div>
  </div>









<?php } elseif ($data_status == "perusahaan") { ?>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    * {
      font-family: 'Plus Jakarta Sans', sans-serif;
      box-sizing: border-box;
    }

    :root {
      --primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      --secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      --success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      --warning: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
      --glass: rgba(255, 255, 255, 0.95);
      --glass-border: rgba(255, 255, 255, 0.2);
      --shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
      min-height: 100vh;
      margin: 0;
      padding: 0;
    }

    .modern-container {
      max-width: 1600px;
      margin: -55px auto 0;
      padding: 30px 20px;
    }

    .header-section {
      background: var(--glass);
      backdrop-filter: blur(20px);
      border-radius: 24px;
      padding: 40px;
      margin-bottom: 30px;
      box-shadow: var(--shadow);
      border: 1px solid var(--glass-border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 30px;
      animation: slideDown 0.6s ease;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .header-title h1 {
      margin: 0;
      font-size: 36px;
      font-weight: 800;
      background: var(--primary);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      letter-spacing: -1px;
    }

    .header-title p {
      margin: 8px 0 0;
      color: #718096;
      font-size: 15px;
      font-weight: 500;
    }

    .search-filter-wrapper {
      display: flex;
      gap: 15px;
      flex: 1;
      max-width: 600px;
    }

    .search-box {
      position: relative;
      flex: 1;
    }

    .search-box input {
      width: 100%;
      padding: 16px 20px 16px 50px;
      border: 2px solid #e2e8f0;
      border-radius: 16px;
      font-size: 14px;
      transition: all 0.3s;
      background: white;
    }

    .search-box input:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 5px rgba(102, 126, 234, 0.1);
    }

    .search-box i {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: #a0aec0;
    }

    .filter-select {
      padding: 16px 45px 16px 20px;
      border: 2px solid #e2e8f0;
      border-radius: 16px;
      background: white;
      font-weight: 600;
      cursor: pointer;
      appearance: none;
      background-image: url("image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%23667eea' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 15px center;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-card {
      background: var(--glass);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 25px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      border: 1px solid var(--glass-border);
      transition: all 0.3s;
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: var(--primary);
    }

    .stat-card:nth-child(2)::before {
      background: var(--secondary);
    }

    .stat-card:nth-child(3)::before {
      background: var(--success);
    }

    .stat-card:nth-child(4)::before {
      background: var(--warning);
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .stat-number {
      font-size: 36px;
      font-weight: 800;
      background: var(--primary);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 5px;
    }

    .stat-label {
      color: #718096;
      font-size: 14px;
      font-weight: 600;
    }

    .main-layout {
      display: grid;
      grid-template-columns: 600px 1fr;
      gap: 30px;
      align-items: start;
    }

    .applicants-container {
      display: flex;
      flex-direction: column;
      gap: 20px;

      max-height: calc(100vh - 200px);
      overflow-y: auto;
      padding-right: 10px;
    }

    .applicant-card {
      background: var(--glass);
      backdrop-filter: blur(15px);
      border-radius: 24px;
      padding: 25px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      border: 2px solid transparent;
      transition: all 0.4s;
      position: relative;
      overflow: hidden;
      animation: fadeInUp 0.6s ease forwards;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .applicant-card::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--primary);
      transform: scaleX(0);
      transition: transform 0.4s;
    }

    .applicant-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 50px rgba(102, 126, 234, 0.2);
      border-color: rgba(102, 126, 234, 0.3);
    }

    .applicant-card:hover::after {
      transform: scaleX(1);
    }

    .applicant-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 18px;
      gap: 15px;
    }

    .applicant-name {
      font-size: 20px;
      font-weight: 700;
      color: #2d3748;
      margin: 0 0 10px 0;
    }

    .applicant-meta {
      display: flex;
      gap: 10px;

    }

    .meta-tag {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      background: rgba(102, 126, 234, 0.1);
      border-radius: 10px;
      font-size: 12px;
      font-weight: 600;
      color: #4a5568;
    }

    .meta-tag i {
      color: #667eea;
      font-size: 14px;
    }

    .status-badge {
      padding: 8px 20px;
      border-radius: 50px;
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      white-space: nowrap;
    }

    .status-diproses {
      background: linear-gradient(135deg, #f093fb, #f5576c);
      color: white;
    }

    .status-panggilan {
      background: linear-gradient(135deg, #4facfe, #00f2fe);
      color: white;
    }

    .status-wawancara {
      background: linear-gradient(135deg, #43e97b, #38f9d7);
      color: white;
    }

    .status-diterima {
      background: linear-gradient(135deg, #fa709a, #fee140);
      color: white;
    }

    .status-ditolak {
      background: linear-gradient(135deg, #a8edea, #fed6e3);
      color: #2d3748;
    }

    .action-buttons {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 2px solid #f0f0f0;
    }

    .btn-action {
      padding: 12px 16px;
      border: none;
      border-radius: 12px;
      font-weight: 700;
      font-size: 12px;
      cursor: pointer;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .btn-cv {
      background: var(--primary);
      color: white;
    }

    .btn-profil {
      background: var(--secondary);
      color: white;
    }

    .btn-ubah {
      background: var(--success);
      color: white;
    }

    .btn-hapus {
      background: var(--warning);
      color: white;
    }

    .detail-panel {
      background: var(--glass);
      backdrop-filter: blur(20px);
      border-radius: 24px;
      box-shadow: var(--shadow);
      border: 1px solid var(--glass-border);
      position: sticky;
      top: 30px;
      max-height: calc(100vh - 60px);
      overflow-y: auto;
      animation: slideInRight 0.6s ease;
    }

    @keyframes slideInRight {
      from {
        opacity: 0;
        transform: translateX(30px);
      }

      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .detail-panel::-webkit-scrollbar {
      width: 8px;
    }

    .detail-panel::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }

    .detail-panel::-webkit-scrollbar-thumb {
      background: var(--primary);
      border-radius: 10px;
    }

    .panel-header {
      background: var(--primary);
      color: white;
      padding: 25px 30px;
      border-radius: 24px 24px 0 0;
      position: relative;
      overflow: hidden;
    }

    .panel-header::before {
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

    .panel-title {
      margin: 0;
      font-size: 22px;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 10px;
      position: relative;
      z-index: 1;
    }

    .panel-content {
      padding: 25px 30px;
    }

    .profile-header {
      text-align: center;
      padding: 25px;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
      border-radius: 16px;
      margin-bottom: 25px;
      border: 2px solid rgba(102, 126, 234, 0.2);
    }

    .profile-avatar {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #fff;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      display: block;
      margin: 0 auto 15px;
    }

    .profile-avatar-placeholder {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px;
      font-size: 40px;
      color: white;
      border: 4px solid white;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .profile-name-detail {
      font-size: 20px;
      font-weight: 700;
      color: #2d3748;
      margin: 0 0 6px 0;
    }

    .profile-nisn {
      color: #718096;
      font-size: 13px;
      font-weight: 600;
    }

    .section {
      margin-bottom: 25px;
      background: white;
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .section-title {
      font-size: 15px;
      font-weight: 700;
      color: #667eea;
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      gap: 8px;
      padding-bottom: 12px;
      border-bottom: 2px solid #f0f0f0;
    }

    .section-title i {
      font-size: 18px;
      width: 28px;
      height: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(102, 126, 234, 0.1);
      border-radius: 8px;
    }

    .info-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 12px;
    }

    .info-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      padding: 10px 0;
      border-bottom: 1px solid #f0f0f0;
    }

    .info-row:last-child {
      border-bottom: none;
    }

    .info-label {
      font-weight: 600;
      color: #718096;
      font-size: 12px;
      flex: 0 0 110px;
    }

    .info-value {
      color: #2d3748;
      font-size: 13px;
      font-weight: 500;
      flex: 1;
      text-align: right;
      word-break: break-word;
    }

    .data-card {
      background: linear-gradient(135deg, #f5f7fa, #e4e8ec);
      border-radius: 12px;
      padding: 16px;
      margin-bottom: 12px;
      border-left: 4px solid #667eea;
      transition: all 0.3s;
    }

    .data-card:hover {
      transform: translateX(4px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .data-card-title {
      font-weight: 700;
      color: #2d3748;
      margin-bottom: 8px;
      font-size: 14px;
    }

    .data-card-content {
      font-size: 12px;
      color: #4a5568;
      line-height: 1.5;
    }

    .data-card-content strong {
      color: #667eea;
    }

    .tabs {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 12px;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 2px solid #e2e8f0;
    }

    .tab {
      width: 100%;
      min-height: 48px;
      border: none;
      background: #f8fafc;
      font-weight: 700;
      color: #64748b;
      cursor: pointer;
      border-radius: 12px;
      transition: all 0.3s ease;
      font-size: 13px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 10px;
    }

    .tab.active {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: #fff;
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .tab:hover:not(.active) {
      background: rgba(102, 126, 234, 0.1);
      color: #667eea;
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
      animation: fadeIn 0.4s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    .documents-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
    }

    .document-item {
      background: linear-gradient(135deg, #f8f9fa, #e9ecef);
      border: 2px solid #dee2e6;
      border-radius: 14px;
      padding: 20px;
      text-align: center;
      transition: all 0.3s;
      position: relative;
      overflow: hidden;
    }

    .document-item.available {
      border-color: #667eea;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
    }

    .document-item:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      border-color: #667eea;
    }

    .document-icon {
      width: 60px;
      height: 60px;
      margin: 0 auto 12px;
      background: var(--primary);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      color: white;
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .document-item.available .document-icon {
      background: linear-gradient(135deg, #43e97b, #38f9d7);
    }

    .document-name {
      font-weight: 700;
      color: #2d3748;
      font-size: 14px;
      margin-bottom: 6px;
    }

    .document-status {
      font-size: 11px;
      color: #718096;
      margin-bottom: 12px;
      font-weight: 600;
    }

    .document-status.available {
      color: #43e97b;
    }

    .btn-download-doc {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 16px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.3s;
      width: 100%;
      justify-content: center;
    }

    .btn-download-doc:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-download-doc.disabled {
      background: #cbd5e0;
      cursor: not-allowed;
    }

    .document-item.not-available {
      opacity: 0.6;
    }

    .document-item.not-available .document-icon {
      background: #cbd5e0;
    }

    .empty-state {
      text-align: center;
      padding: 50px 25px;
      color: #a0aec0;
    }

    .empty-state i {
      font-size: 70px;
      margin-bottom: 18px;
      opacity: 0.4;
    }

    .empty-state p {
      font-size: 15px;
      font-weight: 500;
    }

    .loading {
      text-align: center;
      padding: 50px 25px;
    }

    .loading i {
      font-size: 45px;
      color: #667eea;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      100% {
        transform: rotate(360deg);
      }
    }

    .form-group {
      margin-bottom: 18px;
    }

    .form-label {
      display: block;
      color: #718096;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 6px;
    }

    .form-control,
    select.form-control {
      width: 100%;
      padding: 2px 16px;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      font-size: 14px;
      font-weight: 600;
      transition: all 0.3s;
      background: white !important;
      color: #2d3748 !important;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
    }

    .form-control:focus,
    select.form-control:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    select.form-control option {
      color: #2d3748 !important;
      background: white !important;
      padding: 10px;
    }

    .btn-submit {
      width: 100%;
      padding: 14px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 12px;
      font-weight: 700;
      font-size: 15px;
      cursor: pointer;
      transition: all 0.3s;
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 5px 10px;
      background: rgba(102, 126, 234, 0.1);
      color: #667eea;
      border-radius: 18px;
      font-size: 11px;
      font-weight: 600;
      margin: 3px;
    }

    .sosmed-list {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .sosmed-item {
      background: linear-gradient(135deg, #f8f9fa, #e9ecef);
      border-radius: 12px;
      padding: 15px;
      min-width: 180px;
      border: 1px solid #dee2e6;
      transition: all 0.3s;
    }

    .sosmed-item:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
      border-color: #667eea;
    }

    .sosmed-platform {
      font-weight: 700;
      color: #2d3748;
      font-size: 14px;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .sosmed-platform i {
      color: #667eea;
      font-size: 18px;
    }

    .sosmed-username {
      font-size: 12px;
      color: #718096;
      margin-bottom: 5px;
    }

    .sosmed-link {
      font-size: 11px;
      color: #667eea;
      word-break: break-all;
    }

    .sosmed-link a {
      color: #667eea;
      text-decoration: none;
    }

    .sosmed-link a:hover {
      text-decoration: underline;
    }

    @media (max-width: 1200px) {
      .main-layout {
        grid-template-columns: 1fr;
      }

      .detail-panel {
        position: static;
        max-height: none;
      }
    }

    @media (max-width: 768px) {
      .header-section {
        flex-direction: column;
        align-items: stretch;
        padding: 25px;
      }

      .search-filter-wrapper {
        max-width: 100%;
        flex-direction: column;
      }

      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .action-buttons {
        grid-template-columns: 1fr;
      }

      .applicant-header {
        flex-direction: column;
      }

      .main-layout {
        grid-template-columns: 1fr !important;
      }

      .documents-grid {
        grid-template-columns: 1fr;
      }

      .tabs {
        flex-wrap: wrap;
      }

      .tab {
        font-size: 11px;
        padding: 6px 12px;
      }
    }

    .form-control,
    select.form-control {
      color: #2d3748 !important;
      background: white !important;
    }

    select.form-control option {
      color: #2d3748 !important;
      background: white !important;
      font-weight: 900;
    }
  </style>

  <div class="modern-container">
    <div class="header-section">
      <div class="header-title">
        <h1>📋 Daftar Pelamar</h1>
        <p>Kelola dan pantau status pelamar lowongan Anda dengan mudah</p>
      </div>
      <div class="search-filter-wrapper">
        <div class="search-box"><input type="text" id="searchInput" placeholder="Cari nama atau NISN..."><i
            class="fa fa-search"></i></div>
        <select id="statusFilter" class="filter-select">
          <option value="">Semua Status</option>
          <option value="Diproses">Diproses</option>
          <option value="Panggilan Wawancara">Panggilan Wawancara</option>
          <option value="Diterima">Diterima</option>
          <option value="Ditolak">Ditolak</option>
        </select>
      </div>
    </div>
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-number" id="totalPelamar">0</div>
        <div class="stat-label">Total Pelamar</div>
      </div>
      <div class="stat-card">
        <div class="stat-number" id="totalDiproses">0</div>
        <div class="stat-label">Sedang Diproses</div>
      </div>
      <div class="stat-card">
        <div class="stat-number" id="totalDiterima">0</div>
        <div class="stat-label">Diterima</div>
      </div>
      <div class="stat-card">
        <div class="stat-number" id="totalBaru">0</div>
        <div class="stat-label">Pelamar Baru</div>
      </div>
    </div>
    <div class="main-layout">
      <div class="applicants-container">
        <?php
        if (empty($id_perusahaan)) {
          echo "<div style='background:white;border-radius:24px;padding:50px;text-align:center;box-shadow:var(--shadow);'><i class='fa fa-exclamation-circle' style='font-size:60px;color:#fc8181;margin-bottom:15px;'></i><h3 style='color:#2d3748;margin:0 0 8px 0;'>ID Perusahaan Tidak Ditemukan</h3><p style='color:#718096;margin:0;'>Silakan login ulang untuk melanjutkan.</p></div>";
        } else {
          $sql = mysqli_query($con, "SELECT l.id_lamaran, l.id_siswa, s.nama, s.nisn, lw.id_lowongan, lw.judul_lowongan, l.cv, l.status, l.tanggal_lamaran FROM tb_lamaran l INNER JOIN tb_siswa s ON l.id_siswa = s.id_siswa INNER JOIN tb_lowongan lw ON l.id_lowongan = lw.id_lowongan WHERE lw.id_perusahaan = '$id_perusahaan' ORDER BY l.id_lamaran DESC");
          if (!$sql || mysqli_num_rows($sql) == 0) {
            echo "<div style='background:white;border-radius:24px;padding:70px 35px;text-align:center;box-shadow:var(--shadow);'><i class='fa fa-inbox' style='font-size:75px;color:#e2e8f0;margin-bottom:20px;'></i><h3 style='color:#2d3748;margin:0 0 12px 0;font-size:26px;'>Belum Ada Pelamar</h3><p style='color:#718096;margin:0;'>Silakan buat lowongan terlebih dahulu.</p></div>";
          } else {
            $stats = ['total' => 0, 'diproses' => 0, 'diterima' => 0, 'baru' => 0];
            while ($data = mysqli_fetch_array($sql)) {
              $stats['total']++;
              if ($data['status'] == 'Diproses')
                $stats['diproses']++;
              if ($data['status'] == 'Diterima')
                $stats['diterima']++;
              $stats['baru']++;
              $statusClass = "";
              $statusText = "";
              switch ($data['status']) {
                case "Diproses":
                  $statusClass = "status-diproses";
                  $statusText = "Diproses";
                  break;
                case "Panggilan Wawancara":
                  $statusClass = "status-panggilan";
                  $statusText = "Panggilan Wawancara";
                  break;
                case "Diterima":
                  $statusClass = "status-diterima";
                  $statusText = "Diterima";
                  break;
                case "Ditolak":
                  $statusClass = "status-ditolak";
                  $statusText = "Ditolak";
                  break;
                default:
                  $statusClass = "status-diproses";
                  $statusText = "Diproses";
              }
              ?>
              <div class="applicant-card" data-nama="<?php echo strtolower($data['nama']); ?>"
                data-nisn="<?php echo $data['nisn'] ?? ''; ?>" data-status="<?php echo $data['status']; ?>"
                id="card-<?php echo $data['id_lamaran']; ?>">
                <div class="applicant-header">
                  <div>
                    <h3 class="applicant-name"><?php echo htmlspecialchars($data['nama']); ?></h3>
                    <br>
                    <div class="applicant-meta">
                      <span class="meta-tag"><i class="fa fa-id-card"></i>
                        <?php echo htmlspecialchars($data['nisn'] ?? '-'); ?></span>
                      <span class="meta-tag"><i class="fa fa-briefcase"></i>
                        <?php echo htmlspecialchars($data['judul_lowongan']); ?></span>
                    </div>
                  </div>
                  <span class="status-badge <?php echo $statusClass; ?>"
                    id="badge-<?php echo $data['id_lamaran']; ?>"><?php echo htmlspecialchars($statusText); ?></span>
                </div>
                <div class="action-buttons">
                  <?php if (!empty($data['cv'])): ?><a
                      href="pages/pendaftar/download.php?filename=<?= urlencode($data['cv']); ?>" target="_blank"
                      class="btn-action btn-cv"><i class="fa fa-download"></i> CV</a><?php endif; ?>
                  <button class="btn-action btn-profil"
                    onclick="showProfile('<?php echo $data['id_siswa']; ?>','<?php echo htmlspecialchars($data['nama']); ?>')"><i
                      class="fa fa-user"></i> Profil</button>
                  <button class="btn-action btn-ubah"
                    onclick="showStatus('<?php echo $data['id_lamaran']; ?>','<?php echo htmlspecialchars($data['nama']); ?>','<?php echo htmlspecialchars($data['status']); ?>')"><i
                      class="fa fa-edit"></i> Status</button>
                  <button class="btn-action btn-hapus" onclick="confirmDelete('<?php echo $data['id_lamaran']; ?>')"><i
                      class="fa fa-trash"></i> Hapus</button>
                </div>
              </div>
              <?php
            }
            echo "<script>document.getElementById('totalPelamar').textContent={$stats['total']};document.getElementById('totalDiproses').textContent={$stats['diproses']};document.getElementById('totalDiterima').textContent={$stats['diterima']};document.getElementById('totalBaru').textContent={$stats['baru']};</script>";
          }
        }
        ?>
      </div>
      <div class="detail-panel" id="dynamicPanel">
        <div class="panel-header">
          <h3 class="panel-title"><i class="fa fa-user-circle"></i> Detail Panel</h3>
        </div>
        <div class="panel-content">
          <div class="empty-state"><i class="fa fa-mouse-pointer"></i>
            <p>Klik tombol <strong>Profil</strong> atau <strong>Status</strong> untuk melihat detail pelamar.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function showProfile(idSiswa, nama) {
      document.getElementById("dynamicPanel").innerHTML = `<div class="panel-header"><h3 class="panel-title"><i class="fa fa-user"></i> Profil Pelamar</h3></div><div class="loading"><i class="fa fa-spinner"></i><p style="margin-top:12px;color:#718096;">Memuat data...</p></div>`;
      fetch('pages/pendaftar/get_profile.php?id_siswa=' + idSiswa)
        .then(res => res.json()).then(data => { if (data.success) displayFullProfile(data.data, nama); else showErrorPanel(); })
        .catch(err => { console.error(err); showErrorPanel(); });
    }
    function showErrorPanel() {
      document.getElementById("dynamicPanel").innerHTML = `<div class="panel-header"><h3 class="panel-title"><i class="fa fa-exclamation-triangle"></i> Error</h3></div><div class="panel-content"><div style="text-align:center;padding:35px;color:#fc8181;"><i class="fa fa-times-circle" style="font-size:45px;margin-bottom:12px;"></i><p>Gagal memuat data profil</p></div></div>`;
    }

    function displayFullProfile(data, nama) {
      const s = data.siswa || {}, docs = data.dokumen || {};

      function createDocItem(name, icon, file) {
        const hasFile = file && file !== '' && file !== 'NULL';
        return `<div class="document-item ${hasFile ? 'available' : 'not-available'}"><div class="document-icon"><i class="fa ${icon}"></i></div><div class="document-name">${name}</div><div class="document-status ${hasFile ? 'available' : ''}">${hasFile ? '<i class="fa fa-check-circle"></i> Tersedia' : '<i class="fa fa-times-circle"></i> Belum diunggah'}</div>${hasFile ? `<a href="pages/pendaftar/download_dokumen.php?file=${encodeURIComponent(file)}&id_siswa=${s.id_siswa}" target="_blank" class="btn-download-doc"><i class="fa fa-download"></i> Unduh</a>` : `<button class="btn-download-doc disabled" disabled><i class="fa fa-ban"></i> Tidak Tersedia</button>`}</div>`;
      }

      function getSosmedIcon(platform) {
        const p = (platform || '').toLowerCase();
        if (p.includes('facebook')) return 'fa-facebook';
        if (p.includes('instagram')) return 'fa-instagram';
        if (p.includes('twitter') || p.includes('x.com')) return 'fa-twitter';
        if (p.includes('linkedin')) return 'fa-linkedin';
        if (p.includes('tiktok')) return 'fa-music';
        if (p.includes('youtube')) return 'fa-youtube';
        if (p.includes('whatsapp')) return 'fa-whatsapp';
        return 'fa-link';
      }

      let html = `<div class="panel-header"><h3 class="panel-title"><i class="fa fa-user"></i> ${s.nama || nama}</h3></div><div class="panel-content">
    <div class="profile-header">
  ${s.foto && s.foto !== 'NULL' && s.foto !== '' ?
        `<img src="/adminbkk/peserta/foto/${s.foto}" class="profile-avatar" alt="Foto" 
          onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
     <div class="profile-avatar-placeholder" style="display:none"><i class="fa fa-user"></i></div>`
        :
        `<div class="profile-avatar-placeholder"><i class="fa fa-user"></i></div>`
      }
  <h2 class="profile-name-detail">${s.nama || '-'}</h2>
  <p class="profile-nisn">NISN: ${s.nisn || '-'}</p>
</div>
    <div class="tabs">
      <button class="tab active" onclick="switchTab(event,'personal')">👤 Pribadi</button>
      <button class="tab" onclick="switchTab(event,'keluarga')">🏠 Keluarga</button>
      <button class="tab" onclick="switchTab(event,'pendidikan')">🎓 Pendidikan</button>
      <button class="tab" onclick="switchTab(event,'pengalaman')">💼 Pengalaman</button>
      <button class="tab" onclick="switchTab(event,'sertifikasi')">📜 Sertifikasi</button>
      <button class="tab" onclick="switchTab(event,'organisasi')">🤝 Organisasi</button>
      <button class="tab" onclick="switchTab(event,'sosmed')">🌐 Sosial Media</button>
      <button class="tab" onclick="switchTab(event,'dokumen')">📄 Dokumen</button>
    </div>`;

      html += `<div id="personal" class="tab-content active"><div class="section"><div class="section-title"><i class="fa fa-user"></i> Informasi Pribadi</div><div class="info-grid">
    <div class="info-row"><span class="info-label">Nama</span><span class="info-value">${s.nama || '-'}</span></div>
    <div class="info-row"><span class="info-label">NISN</span><span class="info-value">${s.nisn || '-'}</span></div>
    <div class="info-row"><span class="info-label">Jenis Kelamin</span><span class="info-value">${s.jekel || '-'}</span></div>
    <div class="info-row"><span class="info-label">Tempat, Tgl Lahir</span><span class="info-value">${s.tempat_lahir || '-'}, ${formatDate(s.tanggal_lahir)}</span></div>
    <div class="info-row"><span class="info-label">NIK</span><span class="info-value">${s.nik || '-'}</span></div>
    <div class="info-row"><span class="info-label">Agama</span><span class="info-value">${s.agama || '-'}</span></div>
    <div class="info-row"><span class="info-label">Kewarganegaraan</span><span class="info-value">${s.kewarganegaraan || '-'}</span></div>
    <div class="info-row"><span class="info-label">Status Perkawinan</span><span class="info-value">${s.status_perkawinan || '-'}</span></div>
    <div class="info-row"><span class="info-label">Alamat</span><span class="info-value">${s.alamat || '-'}</span></div>
    <div class="info-row"><span class="info-label">No. HP</span><span class="info-value">${s.no_hp || '-'}</span></div>
    <div class="info-row"><span class="info-label">Email</span><span class="info-value">${s.email || '-'}</span></div>
    <div class="info-row"><span class="info-label">Tinggi/Berat</span><span class="info-value">${s.tinggi_badan || '-'} cm / ${s.berat_badan || '-'} kg</span></div>
  </div></div></div>`;

      html += `<div id="keluarga" class="tab-content"><div class="section"><div class="section-title"><i class="fa fa-home"></i> Data Keluarga</div>`;
      if (data.keluarga && data.keluarga.length > 0) {
        data.keluarga.forEach(k => {
          html += `<div class="data-card"><div class="data-card-title">${k.nama_lengkap || '-'}</div><div class="data-card-content">
        <strong>Hubungan:</strong> ${k.status || '-'}<br><strong>Pekerjaan:</strong> ${k.pekerjaan || '-'}<br><strong>No. HP:</strong> ${k.no_hp || '-'}</div></div>`;
        });
      } else { html += '<p style="text-align:center;color:#a0aec0;padding:20px;">Belum ada data keluarga</p>'; }
      html += `</div></div>`;

      html += `<div id="pendidikan" class="tab-content"><div class="section"><div class="section-title"><i class="fa fa-graduation-cap"></i> Riwayat Pendidikan</div>`;
      if (data.pendidikan && data.pendidikan.length > 0) {
        data.pendidikan.forEach(p => {
          html += `<div class="data-card"><div class="data-card-title">${p.tingkat || '-'} - ${p.sekolah || '-'}</div><div class="data-card-content">
        ${p.jurusan ? `<strong>Jurusan:</strong> ${p.jurusan}<br>` : ''}${p.ipk ? `<strong>IPK:</strong> ${p.ipk}<br>` : ''}${p.akreditasi ? `<strong>Akreditasi:</strong> ${p.akreditasi}<br>` : ''}
        ${p.tgl_mulai && p.tgl_selesai ? `<strong>Periode:</strong> ${p.tgl_mulai} - ${p.tgl_selesai}` : ''}</div></div>`;
        });
      } else { html += '<p style="text-align:center;color:#a0aec0;padding:20px;">Belum ada data pendidikan</p>'; }
      html += `</div></div>`;

      html += `<div id="pengalaman" class="tab-content"><div class="section"><div class="section-title"><i class="fa fa-briefcase"></i> Pengalaman Kerja</div>`;
      if (data.pengalaman && data.pengalaman.length > 0) {
        data.pengalaman.forEach(p => {
          html += `<div class="data-card"><div class="data-card-title">${p.posisi || '-'} - ${p.nama_perusahaan || '-'}</div><div class="data-card-content">
        ${p.level_jabatan ? `<strong>Jabatan:</strong> ${p.level_jabatan}<br>` : ''}${p.status_pegawai ? `<strong>Status:</strong> ${p.status_pegawai}<br>` : ''}
        ${p.tanggal_mulai ? `<strong>Periode:</strong> ${formatDate(p.tanggal_mulai)} - ${p.saat_ini === 'Ya' ? 'Sekarang' : formatDate(p.tanggal_selesai)}<br>` : ''}
        ${p.deskripsi ? `<strong>Deskripsi:</strong> ${p.deskripsi}` : ''}</div></div>`;
        });
      } else { html += '<p style="text-align:center;color:#a0aec0;padding:20px;">Belum ada pengalaman kerja</p>'; }
      html += `</div></div>`;

      html += `<div id="sertifikasi" class="tab-content"><div class="section"><div class="section-title"><i class="fa fa-certificate"></i> Sertifikasi</div>`;
      if (data.sertifikasi && data.sertifikasi.length > 0) {
        data.sertifikasi.forEach(cert => {
          html += `<div class="data-card"><div class="data-card-title">${cert.nama_sertifikat || '-'}</div><div class="data-card-content">
        <strong>Lembaga:</strong> ${cert.lembaga || '-'}<br><strong>Tahun:</strong> ${cert.tahun_sertifikat || '-'}
        ${cert.tahun_berlaku ? `<br><strong>Berlaku sampai:</strong> ${cert.tahun_berlaku}` : ''}${cert.skor ? `<br><strong>Skor:</strong> ${cert.skor}` : ''}</div></div>`;
        });
      } else { html += '<p style="text-align:center;color:#a0aec0;padding:20px;">Belum ada sertifikasi</p>'; }
      html += `</div></div>`;

      html += `<div id="organisasi" class="tab-content"><div class="section"><div class="section-title"><i class="fa fa-users"></i> Pengalaman Organisasi</div>`;
      if (data.organisasi && data.organisasi.length > 0) {
        data.organisasi.forEach(org => {
          html += `<div class="data-card"><div class="data-card-title">${org.posisi || '-'} - ${org.nama_organisasi || '-'}</div><div class="data-card-content">
        ${org.lokasi ? `<strong>Lokasi:</strong> ${org.lokasi}<br>` : ''}
        <strong>Periode:</strong> ${org.tahun_mulai || '-'} - ${org.tahun_selesai || 'Sekarang'}<br>
        ${org.keterangan ? `<strong>Keterangan:</strong> ${org.keterangan}` : ''}</div></div>`;
        });
      } else { html += '<p style="text-align:center;color:#a0aec0;padding:20px;">Belum ada pengalaman organisasi</p>'; }
      html += `</div></div>`;

      html += `<div id="sosmed" class="tab-content"><div class="section"><div class="section-title"><i class="fa fa-share-alt"></i> Sosial Media</div>`;
      if (data.sosial_media && data.sosial_media.length > 0) {
        html += `<div class="sosmed-list">`;
        data.sosial_media.forEach(sm => {
          const icon = getSosmedIcon(sm.nama_platform);
          html += `<div class="sosmed-item">
        <div class="sosmed-platform"><i class="fa ${icon}"></i> ${sm.nama_platform || '-'}</div>
        ${sm.username ? `<div class="sosmed-username"><strong>Username:</strong> ${sm.username}</div>` : ''}
        ${sm.link ? `<div class="sosmed-link"><a href="${sm.link}" target="_blank"><i class="fa fa-external-link"></i> Kunjungi Profil</a></div>` : ''}
      </div>`;
        });
        html += `</div>`;
      } else { html += '<p style="text-align:center;color:#a0aec0;padding:20px;">Belum ada data sosial media</p>'; }
      html += `</div></div>`;

      html += `<div id="dokumen" class="tab-content"><div class="section"><div class="section-title"><i class="fa fa-folder-open"></i> Dokumen Pelamar</div><div class="documents-grid">
    ${createDocItem('KTP', 'fa-id-card', docs.ktp_file)}
    ${createDocItem('Ijazah', 'fa-certificate', docs.ijazah)}
    ${createDocItem('Transkrip Nilai', 'fa-file-text', docs.transkrip)}
    ${createDocItem('Dokumen Lainnya', 'fa-file', docs.dokumen_lain)}
  </div><div style="margin-top:20px;padding:15px;background:linear-gradient(135deg,rgba(102,126,234,0.05),rgba(118,75,162,0.05));border-radius:12px;border-left:4px solid #667eea;">
    <p style="margin:0;font-size:12px;color:#718096;"><i class="fa fa-info-circle" style="color:#667eea;margin-right:6px;"></i> Klik tombol <strong>Unduh</strong> untuk mengunduh dokumen yang tersedia.</p></div></div></div>`;

      html += `</div>`;
      document.getElementById("dynamicPanel").innerHTML = html;
    }

    function switchTab(e, name) {
      document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
      e.target.classList.add('active');
      document.getElementById(name).classList.add('active');
    }

    function showStatus(id, nama, currentStatus) {
      const status = (currentStatus || '').trim();
      let html = `<div class="panel-header"><h3 class="panel-title"><i class="fa fa-edit"></i> Ubah Status</h3></div><div class="panel-content">
    <div style="background:linear-gradient(135deg,#f5f7fa,#e4e8ec);border-radius:18px;padding:25px;">
      <div style="margin-bottom:25px;"><div class="form-label">Nama Pelamar</div><p style="margin:0;font-size:18px;font-weight:700;color:#2d3748;">${nama}</p></div>
      <form id="formUpdateStatus" onsubmit="updateStatusAjax(event, '${id}')">
        <div class="form-group"><div class="form-label">Status Baru</div>
          <select name="status" id="selectStatus" class="form-control" required style="color: #2d3748 !important; background: white !important;">
            <option value="Diproses" ${status === 'Diproses' ? 'selected' : ''} style="color: #2d3748; background: white;">Diproses</option>
            <option value="Panggilan Wawancara" ${status === 'Panggilan Wawancara' ? 'selected' : ''} style="color: #2d3748; background: white;">Panggilan Wawancara</option>
            <option value="Diterima" ${status === 'Diterima' ? 'selected' : ''} style="color: #2d3748; background: white;">Diterima</option>
            <option value="Ditolak" ${status === 'Ditolak' ? 'selected' : ''} style="color: #2d3748; background: white;">Ditolak</option>
          </select>
        </div>
        <button type="submit" name="update" class="btn-submit"><i class="fa fa-save"></i> Update Status</button>
      </form>
    </div></div>`;
      document.getElementById("dynamicPanel").innerHTML = html;
    }

    async function updateStatusAjax(event, idLamaran) {
      event.preventDefault();
      const form = event.target;
      const statusBaru = document.getElementById('selectStatus').value;
      const submitBtn = form.querySelector('.btn-submit');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';
      submitBtn.disabled = true;
      try {
        const formData = new FormData();
        formData.append('update', '1');
        formData.append('status', statusBaru);
        await fetch(`?halaman=ubah_status&id=${idLamaran}`, { method: 'POST', body: formData });
        window.location.reload();
      } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat update status');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    }

    function confirmDelete(id) { if (confirm("Apakah anda yakin ingin menghapus pendaftar ini?")) { window.location = "?halaman=pendaftar_aksi&kode=" + id; } }
    function formatDate(dateStr) { if (!dateStr || dateStr === '0000-00-00' || dateStr === 'NULL') return '-'; return new Date(dateStr).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }); }

    document.addEventListener("DOMContentLoaded", function () {
      const search = document.getElementById("searchInput"), filter = document.getElementById("statusFilter");
      if (!search || !filter) return;
      function filterData() {
        const kw = search.value.toLowerCase(), st = filter.value;
        document.querySelectorAll(".applicant-card").forEach((card, i) => {
          const match = (card.dataset.nama.includes(kw) || card.dataset.nisn.includes(kw)) && (!st || card.dataset.status === st);
          card.style.display = match ? "" : "none";
          if (match) card.style.animation = `fadeInUp 0.6s ease ${i * 0.05}s forwards`;
        });
      }
      search.addEventListener("keyup", filterData); filter.addEventListener("change", filterData);
    });
  </script>

<?php } ?>