<?php
$konek = mysqli_connect(
    $_ENV['MYSQLHOST'],
    $_ENV['MYSQLUSER'],
    $_ENV['MYSQLPASSWORD'],
    $_ENV['MYSQLDATABASE'],
    $_ENV['MYSQLPORT']
);

/* PROSES AJUKAN VERIFIKASI */
if(isset($_POST['ajukan_verifikasi'])){

mysqli_query($konek,"UPDATE tb_perusahaan 
SET status_verifikasi='Belum Diverifikasi'
WHERE id_user='".$tampil['id_user']."'");

echo "<script>alert('Permohonan verifikasi berhasil dikirim');</script>";
echo "<meta http-equiv='refresh' content='0'>";
}

if(isset($data_username)){

$sql2 = $konek->query("
SELECT u.*, p.*, d.*
FROM tb_user u
LEFT JOIN tb_perusahaan p ON u.id_user = p.id_user
LEFT JOIN tb_dokumen_perusahaan d ON p.id_perusahaan = d.id_perusahaan
WHERE u.username='$data_username'
");
$tampil = $sql2->fetch_assoc();

/* =========================
HITUNG PERSENTASE PROFIL
========================= */
$total = 11;
$isi = 0;

if(!empty($tampil['nama_perusahaan'])) $isi++;
if(!empty($tampil['email'])) $isi++;
if(!empty($tampil['alamat'])) $isi++;
if(!empty($tampil['bidang_usaha'])) $isi++;
if(!empty($tampil['jumlah_karyawan'])) $isi++;
if(!empty($tampil['deskripsi'])) $isi++;
if(!empty($tampil['manfaat'])) $isi++;
if(!empty($tampil['logo'])) $isi++;
if(!empty($tampil['file_nib'])) $isi++;
if(!empty($tampil['file_npwp'])) $isi++;
if(!empty($tampil['file_mou'])) $isi++;

$persen = ($isi / $total) * 100;

/* =========================
CEK PROFIL LENGKAP
========================= */
$profil_lengkap = ($persen == 100);
?>

<div class="card mb-3">

<div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">

<div>
<i class="fa fa-table"></i>
<b>Profil Saya : <?php echo $tampil['nama_perusahaan']; ?></b>
</div>



</div>

<div class="card-body">

<center>

<?php
if($tampil['logo']=="" || $tampil['logo']==NULL){
    $foto_user="dist/img/pegawai.png";
}else{

    if($data_status=="perusahaan"){
        $foto_user="dist/img/foto_perusahaan/".$tampil['logo'];
    }else{
        $foto_user="dist/img/foto_user/".$tampil['foto'];
    }
}
?>
<br><br><br><br>
<div style="position:relative;display:inline-block;">
<img src="<?php echo $foto_user; ?>" class="profile-img">

<a href="?halaman=edit_profile_perusahaan" 
style="
position:absolute;
bottom:5px;
right:5px;
background:#007bff;
color:white;
border-radius:50%;
width:35px;
height:35px;
display:flex;
align-items:center;
justify-content:center;
text-decoration:none;
box-shadow:0 5px 15px rgba(0,0,0,0.3);
">
<i class="fa fa-edit"></i>
</a>

</div>
<br><br>

<?php
$status = trim($tampil['status_verifikasi']);

if($status == "Terverifikasi"){
    $class = "badge-success";
    $text = "Perusahaan Terverifikasi BKK";
}
elseif($status == "Ditolak"){
    $class = "badge-danger";
    $text = "Verifikasi Ditolak";
}
else{
    $class = "badge-secondary";
    $text = "Belum Diverifikasi BKK";
}
?>

<span class="badge <?php echo $class; ?>">
<?php echo $text; ?>
</span>

<div style="text-align:center;margin-top:10px;">
    <b>Kelengkapan Profil: <?php echo round($persen); ?>%</b>
</div>

</center>
<style>

/* ===============================
GLOBAL
=============================== */
body{
background:#f4f7fb;
font-family:'Segoe UI',sans-serif;
}

/* ===============================
CARD CONTAINER
=============================== */
.card{
border:none;
border-radius:14px;
box-shadow:0 15px 40px rgba(0,0,0,0.08);
overflow:hidden;
animation:fadeUp .7s ease;
}

/* ===============================
HEADER PROFILE
=============================== */
.card-header{
background:linear-gradient(120deg,#007bff,#00d2ff);
color:white;
font-size:20px;
font-weight:600;
padding:20px;
border:none;
}

/* ===============================
PROFILE IMAGE
=============================== */
.profile-img{
width:150px;
height:150px;
border-radius:50%;
object-fit:cover;
border:6px solid #fff;
box-shadow:0 12px 35px rgba(0,0,0,.2);
transition:.4s;
margin-top:-70px;
background:white;
}

.profile-img:hover{
transform:scale(1.08);
box-shadow:0 20px 45px rgba(0,0,0,.25);
}

/* ===============================
BADGE
=============================== */
.badge{
font-size:13px;
padding:7px 14px;
border-radius:20px;
color:white;
}

.badge-success{
background:#28a745;
}

.badge-secondary{
background:#6c757d;
}

.badge-danger{
background:#dc3545;
}

/* ===============================
TAB NAVIGATION
=============================== */
.tab-nav{
display:flex;
gap:25px;
position:relative;
border-bottom:2px solid #e5e7eb;
margin-top:25px;
overflow-x:auto;
scroll-behavior:smooth;
}

/* hide scrollbar */
.tab-nav::-webkit-scrollbar{
height:5px;
}

.tab-nav::-webkit-scrollbar-thumb{
background:#d1d5db;
border-radius:10px;
}

/* tab item */
.tab-nav a{
display:flex;
align-items:center;
gap:6px;
text-decoration:none;
color:#6b7280;
font-weight:500;
padding:12px 16px;
white-space:nowrap;
position:relative;
transition:.25s;
}

/* icon */
.tab-nav a i{
font-size:14px;
}

/* hover */
.tab-nav a:hover{
color:#2563eb;
}

/* active */
.tab-nav a.active{
color:#2563eb;
font-weight:600;
}

/* slider indicator */
.tab-slider{
position:absolute;
bottom:-2px;
height:3px;
background:#2563eb;
border-radius:10px;
transition:all .35s ease;
width:0;
left:0;
}

/* ===============================
TAB CONTENT
=============================== */
.tab-pane{
display:none;
padding:0;
margin-top:5px;
animation:slideFade .4s ease;
}

/* tab yang dipilih */
.tab-pane:target{
display:block;
}

/* tab default saat halaman dibuka */
.tab-pane:first-of-type{
display:block;
}

/* ===============================
TABLE STYLE
=============================== */
.table{
background:white;
border-radius:10px;
overflow:hidden;
}

.table td{
vertical-align:middle;
}

.table-striped tbody tr:nth-of-type(odd){
background:#f8fbff;
}

/* ===============================
BUTTON STYLE
=============================== */
.btn{
border-radius:8px;
font-weight:600;
padding:9px 20px;
transition:.3s;
}

.btn-success{
background:linear-gradient(120deg,#00c851,#00e676);
border:none;
}

.btn-primary{
background:linear-gradient(120deg,#007bff,#00c6ff);
border:none;
}

.btn:hover{
transform:translateY(-2px);
box-shadow:0 10px 20px rgba(0,0,0,.15);
}

/* ===============================
DATA CARD
=============================== */
.data-card{
background:white;
border:1px solid #e5e7eb;
border-radius:8px;
overflow:hidden;
}

/* header */
.data-header{
display:flex;
justify-content:space-between;
align-items:center;
padding:14px 18px;
border-bottom:1px solid #e5e7eb;
font-weight:600;
font-size:15px;
}

/* edit button */
.btn-edit{
border:1px solid #d1d5db;
background:white;
padding:6px 14px;
border-radius:6px;
font-size:13px;
cursor:pointer;
transition:.2s;
}

.btn-edit:hover{
background:#2563eb;
color:white;
border-color:#2563eb;
}

/* ===============================
DOCUMENT CARD
=============================== */
.doc-container{
display:flex;
flex-wrap:wrap;
gap:20px;
padding:15px;
}

.doc-card{
flex:1 1 220px;
background:white;
border:1px solid #e5e7eb;
border-radius:10px;
padding:20px;
text-align:center;
transition:.3s;
box-shadow:0 2px 5px rgba(0,0,0,.05);
}

.doc-card:hover{
transform:translateY(-4px);
box-shadow:0 10px 20px rgba(0,0,0,.1);
}

.doc-icon{
font-size:40px;
color:#2563eb;
margin-bottom:10px;
}

.doc-title{
font-weight:600;
margin-bottom:10px;
}

.doc-btn{
display:inline-block;
margin-top:8px;
padding:6px 14px;
border-radius:6px;
border:1px solid #2563eb;
color:#2563eb;
text-decoration:none;
font-size:13px;
transition:.2s;
}

.doc-btn:hover{
background:#2563eb;
color:white;
}

/* ===============================
VERIFY BOX
=============================== */
.verify-box{
padding:25px;
text-align:center;
}

.verify-icon{
font-size:50px;
margin-bottom:15px;
}

.verify-wait{
color:#f59e0b;
}

.verify-success{
color:#16a34a;
}

.verify-title{
font-size:20px;
font-weight:600;
margin-bottom:10px;
}

.verify-desc{
color:#6b7280;
margin-bottom:15px;
}

.verify-status{
display:inline-block;
padding:6px 14px;
border-radius:20px;
font-size:13px;
background:#fef3c7;
color:#92400e;
}

/* ===============================
STAT BOX
=============================== */
.stat-box{
background:white;
padding:20px;
border-radius:12px;
box-shadow:0 10px 25px rgba(0,0,0,.05);
text-align:center;
transition:.3s;
}

.stat-box:hover{
transform:translateY(-5px);
}

.stat-title{
font-size:13px;
color:#888;
}

.stat-number{
font-size:28px;
font-weight:700;
color:#007bff;
}

/* ===============================
ANIMATIONS
=============================== */
@keyframes fadeUp{
from{
opacity:0;
transform:translateY(30px);
}
to{
opacity:1;
transform:translateY(0);
}
}

@keyframes slideFade{
from{
opacity:0;
transform:translateX(40px);
}
to{
opacity:1;
transform:translateX(0);
}
}

/* ===============================
SOSIAL MEDIA STYLE
=============================== */

.sosmed-list{
padding:15px;
}

.sosmed-item{
display:flex;
justify-content:space-between;
align-items:center;
padding:12px;
border-bottom:1px solid #eee;
transition:.2s;
}

.sosmed-item:hover{
background:#f9fafb;
}

.sosmed-info{
display:flex;
flex-direction:column;
}

.sosmed-title{
font-weight:600;
font-size:14px;
}

.sosmed-link{
font-size:13px;
color:#666;
}

.sosmed-action{
display:flex;
gap:8px;
}

.btn-icon{
border:1px solid #ddd;
background:white;
padding:6px 10px;
border-radius:6px;
cursor:pointer;
font-size:13px;
transition:.2s;
}

.btn-icon:hover{
background:#2563eb;
color:white;
border-color:#2563eb;
}

.btn-delete:hover{
background:#ef4444;
border-color:#ef4444;
}

/* =========================================
   PERFECT MOBILE RESPONSIVE PROFILE
========================================= */
@media (max-width: 768px){

    html,
    body{
        overflow-x: hidden !important;
    }

    /* ===== CARD ===== */
    .card{
        margin-top: 8px;
        border-radius: 18px !important;
        overflow: hidden !important;
    }

    .card-body{
        padding: 5px !important;
    }

}
</style>
<!-- TAB MENU -->

<div class="tab-nav">

<a href="#perusahaan"><i class="fa fa-building"></i> Data Perusahaan</a>
<a href="#sosial"><i class="fa fa-share-alt"></i> Sosial Media</a>
<a href="#dokumen"><i class="fa fa-file-text"></i> Dokumen Perusahaan</a>
<a href="#verifikasi"><i class="fa fa-check-circle"></i> Verifikasi BKK</a>
<a href="#statistik"><i class="fa fa-bar-chart"></i> Statistik</a>

<div class="tab-slider"></div>

</div>



<!-- DATA PERUSAHAAN -->

<div id="perusahaan" class="tab-pane">

<div class="data-card">

<div class="data-header">
<span>Data Perusahaan</span>

<a href="?halaman=edit_profile_perusahaan" class="btn-edit">
<i class="fa fa-edit"></i> Ubah
</a>
</div>

<div style="padding:15px;">

<table class="table table-striped">

<tr>
<td><b>Username</b></td>
<td>: <?php echo $tampil['username']; ?></td>
</tr>

<tr>
<td><b>Nama</b></td>
<td>: <?php echo $tampil['nama_perusahaan']; ?></td>
</tr>

<tr>
<td><b>Email</b></td>
<td>: <?php echo $tampil['email']; ?></td>
</tr>

<tr>
<td><b>Alamat</b></td>
<td>: <?php echo $tampil['alamat']; ?></td>
</tr>

<tr>
<td><b>Bidang</b></td>
<td>: <?php echo $tampil['bidang_usaha']; ?></td>
</tr>

<tr>
<td><b>Jumlah Karyawan</b></td>
<td>: <?php echo $tampil['jumlah_karyawan']; ?></td>
</tr>

<tr>
<td><b>Deskripsi</b></td>
<td>: <?php echo ($tampil['deskripsi']); ?></td>
</tr>

<tr>
                                    <td style="vertical-align: top;"><b>Manfaat</b></td>
                                    <td style="vertical-align: top;">
                                        <div style="display:flex; align-items:flex-start;">
                                            <span style="margin-right:8px;">:</span>

                                            <?php
                                            if (!empty($tampil['manfaat'])) {
                                                $manfaat = explode(",", $tampil['manfaat']);
                                                echo "<ul style='margin:0; padding-left:20px;'>";
                                                foreach ($manfaat as $m) {
                                                    echo "<li>" . trim($m) . "</li>";
                                                }
                                                echo "</ul>";
                                            } else {
                                                echo "-";
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    </tr>



</table>

</div>
</div>

</div>


<!-- SOSIAL MEDIA -->

<div id="sosial" class="tab-pane">

<div class="data-card">

<div class="data-header">

<span>Akun Sosial Media</span>

<a href="?halaman=tambah_sosial_media&tab=sosial" class="btn-edit">
<i class="fa fa-plus"></i> Tambah
</a>

</div>

<div class="sosmed-list">

<?php
$sosial = mysqli_query($konek, "
SELECT * FROM tb_sosial_media 
WHERE id_user='".$tampil['id_user']."'
");

if(mysqli_num_rows($sosial) > 0){

while($s = mysqli_fetch_assoc($sosial)){
?>

<div class="sosmed-item">

<div class="sosmed-info">

<div class="sosmed-title">
<i class="fa fa-<?php echo strtolower($s['nama_platform']); ?>"></i>
<?php echo ucfirst($s['nama_platform']); ?>
</div>

<div class="sosmed-link">
<?php echo $s['link']; ?>
</div>

</div>

<div class="sosmed-action">

<a href="?halaman=edit_sosial_media&id=<?php echo $s['id_sosial_media']; ?>&tab=sosial" class="btn-icon">
<i class="fa fa-pencil"></i>
</a>

<a href="?halaman=hapus_sosial_media&id=<?php echo $s['id_sosial_media']; ?>&tab=sosial"
onclick="return confirm('Hapus sosial media ini?')"
class="btn-icon btn-delete">
<i class="fa fa-trash"></i>
</a>

</div>

</div>

<?php 
} 
} else { 
?>

<div style="text-align:center;color:#888;padding:30px;">
<i class="fa fa-info-circle"></i> Belum ada data sosial media
</div>

<?php } ?>

</div>

</div>

</div>


                            <!-- DOKUMEN -->

<div id="dokumen" class="tab-pane">

<div class="data-card">

<div class="data-header">
<span>Dokumen Perusahaan</span>

</div>

<div class="doc-container">

<!-- NIB -->
<div class="doc-card">
<div class="doc-icon">
<i class="fa fa-file-text"></i>
</div>

<div class="doc-title">NIB / SIUP</div>

<?php if(!empty($tampil['file_nib'])){ ?>

<a href="dokumen/<?php echo $tampil['file_nib']; ?>" target="_blank" class="doc-btn">
Lihat Dokumen
</a>

<br>

<a href="?halaman=edit_dokumen_perusahaan&tipe=nib&tab=dokumen" class="doc-btn">
<i class="fa fa-edit"></i> Edit
</a>

<a href="?halaman=hapus_dokumen_perusahaan&tipe=nib&tab=dokumen"
onclick="return confirm('Hapus dokumen ini?')"
class="doc-btn"
style="border-color:#dc3545;color:#dc3545;">
<i class="fa fa-trash"></i> Hapus
</a>

<?php } else { ?>

<span style="color:#999;">Belum Upload</span>

<br>

<a href="?halaman=edit_dokumen_perusahaan&tipe=nib&tab=dokumen" class="doc-btn">
Upload
</a>

<?php } ?>

</div>


<!-- NPWP -->
<div class="doc-card">
<div class="doc-icon">
<i class="fa fa-file-text"></i>
</div>

<div class="doc-title">NPWP</div>

<?php if(!empty($tampil['file_npwp'])){ ?>

<a href="dokumen/<?php echo $tampil['file_npwp']; ?>" target="_blank" class="doc-btn">
Lihat Dokumen
</a>

<br>

<a href="?halaman=edit_dokumen_perusahaan&tipe=npwp&tab=dokumen" class="doc-btn">
<i class="fa fa-edit"></i> Edit
</a>

<a href="?halaman=hapus_dokumen_perusahaan&tipe=npwp&tab=dokumen"
onclick="return confirm('Hapus dokumen ini?')"
class="doc-btn"
style="border-color:#dc3545;color:#dc3545;">
<i class="fa fa-trash"></i> Hapus
</a>

<?php } else { ?>

<span style="color:#999;">Belum Upload</span>

<br>

<a href="?halaman=edit_dokumen_perusahaan&tipe=npwp&tab=dokumen" class="doc-btn">
Upload
</a>

<?php } ?>
</div>


<!-- MOU -->
<div class="doc-card">
<div class="doc-icon">
<i class="fa fa-file-text"></i>
</div>

<div class="doc-title">MOU Kerjasama</div>

<?php if(!empty($tampil['file_mou'])){ ?>

<a href="dokumen/<?php echo $tampil['file_mou']; ?>" target="_blank" class="doc-btn">
Lihat Dokumen
</a>

<br>

<a href="?halaman=edit_dokumen_perusahaan&tipe=mou&tab=dokumen" class="doc-btn">
<i class="fa fa-edit"></i> Edit
</a>

<a href="?halaman=hapus_dokumen_perusahaan&tipe=mou&tab=dokumen"
onclick="return confirm('Hapus dokumen ini?')"
class="doc-btn"
style="border-color:#dc3545;color:#dc3545;">
<i class="fa fa-trash"></i> Hapus
</a>

<?php } else { ?>

<span style="color:#999;">Belum Upload</span>

<br>

<a href="?halaman=edit_dokumen_perusahaan&tipe=mou&tab=dokumen" class="doc-btn">
Upload
</a>

<?php } ?>
</div>

</div>

</div>

</div>


<!-- VERIFIKASI -->

<div id="verifikasi" class="tab-pane">

<div class="data-card">

<div class="data-header">
<span>Verifikasi BKK</span>
</div>

<?php
$status = $tampil['status_verifikasi'] ?? "Belum Diverifikasi";
?>

<div class="verify-box">

<?php
if(!$profil_lengkap){
?>

<div class="verify-icon verify-wait">
<i class="fa fa-exclamation-circle"></i>
</div>

<div class="verify-title">Lengkapi Profil Perusahaan</div>

<div class="verify-desc">
Untuk mengajukan verifikasi perusahaan, Anda harus melengkapi profil perusahaan terlebih dahulu.
</div>

<button class="btn btn-secondary" onclick="alert('Silakan lengkapi profil terlebih dahulu');">
<i class="fa fa-lock"></i> Ajukan Verifikasi
</button>

<?php
}
elseif($status=="Belum Diverifikasi"){
?>

<div class="verify-icon verify-wait">
<i class="fa fa-clock-o"></i>
</div>

<div class="verify-title">Menunggu Verifikasi</div>

<div class="verify-desc">
Data perusahaan Anda sedang menunggu proses verifikasi oleh admin BKK SMKN 7 Surabaya.
Pastikan dokumen perusahaan sudah lengkap.
</div>

<span class="verify-status" style="background:#fde68a;color:#92400e;padding:6px 15px;border-radius:20px;">
Belum Diverifikasi
</span>

<?php
}
elseif($status=="Terverifikasi"){
?>

<div class="verify-icon verify-success">
<i class="fa fa-check-circle"></i>
</div>

<div class="verify-title">Perusahaan Terverifikasi</div>

<div class="verify-desc">
Data perusahaan Anda telah diverifikasi oleh admin Bursa Kerja Khusus SMKN 7 Surabaya.
</div>

<span class="verify-status" style="background:#dcfce7;color:#166534;padding:6px 15px;border-radius:20px;">
Terverifikasi
</span>

<?php
}
elseif($status=="Ditolak"){
?>

<div class="verify-icon verify-wait">
<i class="fa fa-times-circle"></i>
</div>

<div class="verify-title">Verifikasi Ditolak</div>

<div class="verify-desc">
Verifikasi perusahaan Anda ditolak oleh admin. Silakan perbaiki data perusahaan dan ajukan kembali.
</div>

<span class="verify-status" style="background:#fecaca;color:#991b1b;padding:6px 15px;border-radius:20px;">
Ditolak
</span>

<?php } ?>

</div>

</div>

</div>
<!-- STATISTIK -->

<div id="statistik" class="tab-pane">

<?php

$id_perusahaan = $tampil['id_perusahaan'];

/* jumlah lowongan */
$jumlah_loker = $konek->query("
SELECT * FROM tb_lowongan 
WHERE id_perusahaan='".$tampil['id_perusahaan']."'
")->num_rows;


/* total pelamar */
$total_pelamar = $konek->query("
SELECT * FROM tb_lamaran 
WHERE id_lowongan IN (
  SELECT id_lowongan FROM tb_lowongan 
  WHERE id_perusahaan='$id_perusahaan'
)
")->num_rows;


/* alumni diterima */
$alumni_diterima = $konek->query("
SELECT * FROM tb_lamaran 
WHERE id_lowongan IN (
  SELECT id_lowongan FROM tb_lowongan 
  WHERE id_perusahaan='".$tampil['id_perusahaan']."'
)
AND status='Diterima'
")->num_rows;

?>

<div style="display:flex;gap:20px;flex-wrap:wrap;">

<div class="stat-box">
<div class="stat-title">Jumlah Lowongan</div>
<div class="stat-number"><?php echo $jumlah_loker; ?></div>
</div>

<div class="stat-box">
<div class="stat-title">Total Pelamar</div>
<div class="stat-number"><?php echo $total_pelamar; ?></div>
</div>

<div class="stat-box">
<div class="stat-title">Alumni Diterima</div>
<div class="stat-number"><?php echo $alumni_diterima; ?></div>
</div>

</div>

</div>



<a href="?halaman=beranda" class="btn btn-primary">
Kembali
</a>

</div>

</div>

<script>

function moveSlider(){

const slider = document.querySelector(".tab-slider");
const active = document.querySelector(".tab-nav a.active");

if(active){
slider.style.width = active.offsetWidth + "px";
slider.style.left = active.offsetLeft + "px";
}

}

document.querySelectorAll(".tab-nav a").forEach(function(tab){

tab.addEventListener("click",function(){

document.querySelectorAll(".tab-nav a").forEach(function(t){
t.classList.remove("active");
});

this.classList.add("active");

moveSlider();

});

});

window.addEventListener("load",function(){

document.querySelector(".tab-nav a").classList.add("active");
moveSlider();


});

window.addEventListener("resize",moveSlider);

</script>
<script id="tab-scroll-reset">
document.querySelectorAll(".tab-nav a").forEach(function(tab){

tab.addEventListener("click", function(){

/* tunggu tab selesai terbuka */
setTimeout(function(){

var activeTab = document.querySelector(".tab-pane.active");
if(activeTab){
activeTab.scrollTop = 0;
}

window.scrollTo(0,0);

}, 10);

});

});
</script>
<script>

const params = new URLSearchParams(window.location.search);
const tab = params.get("tab");

if(tab){

document.querySelectorAll(".tab-pane").forEach(function(p){
p.classList.remove("active");
});

document.querySelectorAll(".tab-nav a").forEach(function(a){
a.classList.remove("active");
});

const targetTab = document.getElementById(tab);
const targetBtn = document.querySelector('.tab-nav a[href="#'+tab+'"]');

if(targetTab) targetTab.classList.add("active");
if(targetBtn) targetBtn.classList.add("active");

}

</script>




    <?php
} elseif (isset($data_nisn)) {
   include "../peserta/profile_peserta.php";

}