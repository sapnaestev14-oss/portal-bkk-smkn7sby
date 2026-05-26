<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once "koneksi.php";

// cek login
$data_username = $_SESSION['ses_username'] ?? '';

if (empty($data_username)) {
    echo "<script>
        alert('Session login habis!');
        window.location='login.php';
    </script>";
    exit;
}

// sanitasi username
$data_username = mysqli_real_escape_string($con, $data_username);

// validasi tipe dokumen
$tipe = $_GET['tipe'] ?? '';

$mapping = [
    'nib'  => 'file_nib',
    'npwp' => 'file_npwp',
    'mou'  => 'file_mou'
];

if (!array_key_exists($tipe, $mapping)) {
    echo "<script>
        alert('Tipe dokumen tidak valid!');
        window.history.back();
    </script>";
    exit;
}

$kolom = $mapping[$tipe];

/* ======================================================
   AMBIL DATA USER
====================================================== */

$getUser = mysqli_query(
    $con,
    "SELECT id_user 
     FROM tb_user 
     WHERE username='$data_username' 
     LIMIT 1"
);

if (!$getUser || mysqli_num_rows($getUser) == 0) {
    echo "<script>
        alert('User tidak ditemukan!');
        window.history.back();
    </script>";
    exit;
}

$user = mysqli_fetch_assoc($getUser);
$id_user = $user['id_user'];

/* ======================================================
   AMBIL DATA PERUSAHAAN
====================================================== */

$getPerusahaan = mysqli_query(
    $con,
    "SELECT id_perusahaan 
     FROM tb_perusahaan 
     WHERE id_user='$id_user' 
     LIMIT 1"
);

if (!$getPerusahaan || mysqli_num_rows($getPerusahaan) == 0) {
    echo "<script>
        alert('Data perusahaan tidak ditemukan!');
        window.history.back();
    </script>";
    exit;
}

$perusahaan = mysqli_fetch_assoc($getPerusahaan);
$id_perusahaan = $perusahaan['id_perusahaan'];

/* ======================================================
   AMBIL DATA DOKUMEN
====================================================== */

$getDok = mysqli_query(
    $con,
    "SELECT * 
     FROM tb_dokumen_perusahaan 
     WHERE id_perusahaan='$id_perusahaan'"
);

$dok = mysqli_fetch_assoc($getDok);

$file_lama = $dok[$kolom] ?? '';
?>

<style>

/* BACKGROUND HALAMAN */
.content-wrapper{
    background: linear-gradient(135deg, #f4f4f4, #ffffff);
    min-height: 100vh;
    padding:40px 20px;
}

/* CONTAINER */
.upload-container{
    max-width:550px;
    margin:auto;
}

/* CARD */
.upload-card{
    background:#ffffff;
    border-radius:20px;
    padding:30px;
    box-shadow:0 20px 50px rgba(0,0,0,0.2);
    text-align:center;
    animation:fadeIn .5s ease;
}

@keyframes fadeIn{
    from{opacity:0; transform:translateY(20px);}
    to{opacity:1; transform:translateY(0);}
}

/* TITLE */
.upload-title{
    font-size:22px;
    font-weight:600;
    margin-bottom:25px;
}

/* BOX */
.upload-box{
    border:2px dashed #28a745;
    border-radius:16px;
    padding:40px 20px;
    cursor:pointer;
    transition:.3s;
    background:#f8fff9;
}

.upload-box:hover{
    background:#eafff0;
    border-color:#20c997;
    transform:scale(1.02);
}

/* ICON */
.upload-icon{
    font-size:60px;
    margin-bottom:10px;
}

/* TEXT */
.main-text{
    font-size:16px;
    font-weight:600;
}

.sub-text{
    font-size:13px;
    color:#777;
}

/* FILE NAME */
.file-name{
    margin-top:15px;
    font-size:14px;
    font-weight:500;
    color:#333;
}

/* BUTTON */
.btn-upload{
    margin-top:25px;
    background:linear-gradient(45deg,#28a745,#20c997);
    color:white;
    border:none;
    padding:14px;
    border-radius:10px;
    width:100%;
    font-size:15px;
    font-weight:600;
    transition:.3s;
}

.btn-upload:hover{
    transform:scale(1.03);
}

/* BACK */
.btn-back{
    display:block;
    margin-top:12px;
    color:#444;
    text-decoration:none;
}

/* RESPONSIVE */
@media(max-width:600px){
    .upload-card{
        padding:20px;
    }
}

</style>

<div class="upload-container">

<div class="upload-card">

<div class="upload-title">
📄 Upload Dokumen <?= strtoupper(htmlspecialchars($tipe)) ?>
</div>

<form method="POST" enctype="multipart/form-data">

<input type="hidden" name="kolom" value="<?= htmlspecialchars($kolom) ?>">

<label class="upload-box">

<div class="upload-icon">📤</div>

<div class="main-text">Klik untuk upload dokumen</div>
<div class="sub-text">Format PDF / JPG / PNG (Max 2MB)</div>

<input 
type="file" 
name="dokumen" 
id="fileInput" 
style="display:none;" 
accept=".pdf,.jpg,.jpeg,.png"
required
>

<div class="file-name" id="fileName">
Belum ada file dipilih
</div>

</label>

<?php if(!empty($file_lama)){ ?>

<br>

<a 
href="dokumen/<?= htmlspecialchars($file_lama) ?>"
target="_blank" 
class="btn btn-primary"
>
Lihat Dokumen Lama
</a>

<?php } ?>

<button type="submit" name="upload" class="btn-upload">
Upload Dokumen
</button>

<a href="?halaman=profile_perusahaan&tab=dokumen#dokumen" class="btn-back">
Kembali
</a>

</form>

</div>
</div>

<script>

const input = document.getElementById("fileInput");
const text = document.getElementById("fileName");

input.addEventListener("change", function(){

    if(this.files.length > 0){
        text.innerText = this.files[0].name;
    }else{
        text.innerText = "Belum ada file dipilih";
    }

});

</script>

<?php

/* ======================================================
   PROSES UPLOAD
====================================================== */

if(isset($_POST['upload'])){

    $kolom = $_POST['kolom'];

    // validasi field database
    if(!in_array($kolom, $mapping)){
        echo "<script>
            alert('Field upload tidak valid!');
            window.history.back();
        </script>";
        exit;
    }

    // cek file upload
    if(!isset($_FILES['dokumen']) || $_FILES['dokumen']['error'] != 0){
        echo "<script>
            alert('Gagal upload file!');
            window.history.back();
        </script>";
        exit;
    }

    $nama = $_FILES['dokumen']['name'];
    $tmp  = $_FILES['dokumen']['tmp_name'];
    $size = $_FILES['dokumen']['size'];

    // validasi ukuran 2MB
    if($size > 2 * 1024 * 1024){
        echo "<script>
            alert('Ukuran file maksimal 2MB!');
            window.history.back();
        </script>";
        exit;
    }

    // validasi ekstensi
    $ext = strtolower(pathinfo($nama, PATHINFO_EXTENSION));

    $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

    if(!in_array($ext, $allowed)){
        echo "<script>
            alert('Format file tidak didukung!');
            window.history.back();
        </script>";
        exit;
    }

    // nama file aman
    $nama_asli = pathinfo($nama, PATHINFO_FILENAME);

$nama_asli = preg_replace("/[^a-zA-Z0-9_-]/", "_", $nama_asli);

$nama_baru = time() . '_' . $nama_asli . '.' . $ext;

    // path absolut railway/linux
   $folder = $_SERVER['DOCUMENT_ROOT'] . "/adminbkk/dokumen/";

    // buat folder jika belum ada
    if(!file_exists($folder)){
        mkdir($folder, 0777, true);
    }

    $path_upload = $folder . $nama_baru;

    // upload file
    if(move_uploaded_file($tmp, $path_upload)){

        // hapus file lama
        if(!empty($file_lama)){

            $old_path = $folder . $file_lama;

            if(file_exists($old_path)){
                unlink($old_path);
            }
        }

        // cek data dokumen
        $cek = mysqli_query(
            $con,
            "SELECT * 
             FROM tb_dokumen_perusahaan 
             WHERE id_perusahaan='$id_perusahaan'"
        );

        if(mysqli_num_rows($cek) > 0){

            $query = mysqli_query($con,"
UPDATE tb_dokumen_perusahaan 
SET `$kolom`='$nama_baru'
WHERE id_perusahaan='$id_perusahaan'
");

if(!$query){
    echo mysqli_error($con);
}

        } else {

            $insert = mysqli_query($con,"
INSERT INTO tb_dokumen_perusahaan
(
    id_perusahaan,
    `$kolom`
) VALUES (
    '$id_perusahaan',
    '$nama_baru'
)
");

if(!$insert){
    die(mysqli_error($con));
}
        }

        echo "<script>
            alert('Dokumen berhasil diupload');
            window.location='?halaman=profile_perusahaan&tab=dokumen#dokumen';
        </script>";

    } else {

        echo "<script>
            alert('Gagal memindahkan file upload!');
            window.history.back();
        </script>";
    }
}
?>