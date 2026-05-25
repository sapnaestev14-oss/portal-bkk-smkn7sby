<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['ses_nisn']) || empty($_SESSION['ses_nisn'])) {
  header("Location: ../peserta.php");
  exit;
} else {
  $data_nisn = $_SESSION["ses_nisn"];
  $data_level = isset($_SESSION["ses_level"]) ? $_SESSION["ses_level"] : "";
}

include_once("../koneksi.php");

/* ========================
   HITUNG KELENGKAPAN PROFIL
   ======================== */

$nisn = $_SESSION['ses_nisn'];

// ambil data siswa
$sql_profil = mysqli_query($con, "SELECT * FROM tb_siswa WHERE nisn='$nisn'");
$profil = mysqli_fetch_assoc($sql_profil);

// ambil id_siswa untuk tracer
$id_siswa = isset($profil['id_siswa']) ? $profil['id_siswa'] : 0;

// cek tracer
$sql_tracer = mysqli_query($con, "SELECT * FROM tb_tracer WHERE id_siswa='$id_siswa'");
$tracer = mysqli_fetch_assoc($sql_tracer);

$persen = 0;

// Cek data profil
if (!empty($profil['nik']))
  $persen += 10;
if (!empty($profil['nama']))
  $persen += 10;
if (!empty($profil['alamat']))
  $persen += 10;
if (!empty($profil['no_hp']))
  $persen += 10;
if (!empty($profil['deskripsi']))
  $persen += 10;
if (!empty($profil['email']))
  $persen += 10;
if (!empty($profil['status_perkawinan']))
  $persen += 10;
if (!empty($profil['foto']))
  $persen += 10;

// Cek tracer study - pastikan ada data
if ($tracer && !empty($tracer['id_tracer'])) {
  $persen += 20;
}

// Pastikan tidak lebih dari 100%
if ($persen > 100)
  $persen = 100;

$query_foto = mysqli_query($con, "SELECT foto FROM tb_siswa WHERE nisn='$data_nisn'");
$data_foto = mysqli_fetch_array($query_foto, MYSQLI_BOTH);

if ($data_foto['foto'] == "" || $data_foto['foto'] == NULL) {
  $foto_peserta = "../dist/img/pegawai.png";
} else {
  $foto_peserta = "foto/" . $data_foto['foto'];
}

$data_status = isset($_SESSION['ses_level']) ? $_SESSION['ses_level'] : '';
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Portal BKK | SMKN 7 Surabaya</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!-- Modern Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">

  <!-- Bootstrap & Icons -->
  <link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="../bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">

  <!-- Modern Sidebar Styles -->
  <style>
  /* ========================================
     GLOBAL & RESET
     ======================================== */
  * {
    font-family: 'Plus Jakarta Sans', sans-serif;
    box-sizing: border-box;
  }

  body {
    background: #f8fafc;
    overflow-x: hidden;
  }

  /* ========================================
     HEADER & NAVBAR
     ======================================== */
  .main-header {    /* bagian kotak Portal BKK */
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 0%) !important;
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);
    height: 70px !important;
    position: fixed !important;
    width: 100%;
    z-index: 1001;
    border: none !important;
  }

  .logo {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
    color: white !important;
    font-weight: 800 !important;
    font-size: 22px !important;
    border: none !important;
    width: 280px !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .logo:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
  }

  .logo-lg {
    letter-spacing: 0.5px;
  }

  .logo-mini {
    font-size: 18px !important;
  }

  /* Modern Navbar */
  .navbar {
    margin-left: 280px !important;
    background:linear-gradient(135deg, #3b82f6 0%, #2563eb 0%) !important;     /* atas nya beranda */
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    border: none !important;
    height: 20px !important;
    transition: all 0.3s ease;
  }

  .navbar-custom-menu .nav > li > a {
    color: #f1e3e7 !important;
    font-weight: 600;
    padding: 25px 20px !important;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
  }

  .navbar-custom-menu .nav > li > a:hover {         /* tempat tulisan Portal BKK */
    background: rgba(59, 130, 246, 0.1) !important;
    color: #3b82f6 !important;
    transform: translateY(-2px);
  }

  .navbar-nav > .notifications-menu > .dropdown-menu,
  .navbar-nav > .tasks-menu > .dropdown-menu,
  .navbar-nav > .user-menu > .dropdown-menu {
    top: 70px !important;
    right: 0 !important;
    left: auto !important;
    border-radius: 16px !important;
    border: none !important;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
    padding: 0 !important;
    overflow: hidden;
    min-width: 280px !important;
    animation: slideDown 0.3s ease;
  }

  @keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .navbar-nav > .user-menu > .dropdown-menu > li > a {
    padding: 14px 20px !important;
    color: #475569 !important;
    font-weight: 500 !important;
    transition: all 0.3s ease !important;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .navbar-nav > .user-menu > .dropdown-menu > li > a:hover {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
    color: white !important;
    padding-left: 25px !important;
  }

  .navbar-nav > .user-menu > .dropdown-menu > li:last-child > a {
    border-bottom: none !important;
    color: #ef4444 !important;
    font-weight: 600 !important;
  }

  .navbar-nav > .user-menu > .dropdown-menu > li:last-child > a:hover {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
    color: white !important;
  }

  /* ========================================
     SIDEBAR
     ======================================== */
  .main-sidebar {
    background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%) !important;
    box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
    width: 280px !important;
    position: fixed !important;
    height: 100vh !important;
    overflow-y: auto;
    overflow-x: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1000;
  }

  .main-sidebar::-webkit-scrollbar {
    width: 6px;
  }

  .main-sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
  }

  .main-sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
  }

  /* ========================================
     USER PANEL - PERBAIKAN SPASI FOTO & NISN
     ======================================== */
  .user-panel {
    padding: 20px 2px !important;
    margin: -3px !important;
    margin-top: 22px !important;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 22px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    
    /* ✅ FLEXBOX: Agar foto dan teks sejajar & berjarak */
    display: flex;
    align-items: center;
    gap: 47px; /* 🔥 Jarak antara FOTO dan TEKS (NISN+Online) */
  }

  .user-panel > .pull-left.image {
    margin-right: 0 !important;
    flex-shrink: 0; /* ✅ Foto tidak mengecil */
  }

  .user-panel .img-circle {
    width: 90px !important;  /* ✅ Ukuran foto lebih besar */
    height: 50px !important;
    border: 3px solid rgba(59, 130, 246, 0.5) !important;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    object-fit: cover;
    transition: all 0.3s ease;
  }

  .user-panel .img-circle:hover {
    transform: scale(1.05);
    border-color: #3b82f6 !important;
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
  }

  .user-panel .info {
    padding: 0 !important;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 5px; /* 🔥 Jarak vertikal antara NISN dan "Online" */
    padding-left: 15px;
  }

  .user-panel .info p {
    font-weight: 700 !important;
    color: #f1f5f9 !important;
    font-size: 16px !important;
    margin: 0 !important; /* ✅ Reset margin agar tidak bertumpuk */
    letter-spacing: 0.3px;
    line-height: 1.3;
    padding-left: 55px;    /* ubah jarak tulisan nisn ke foto */
  }

  .user-panel .info a {
    color: #94a3b8 !important;
    font-weight: 500 !important;
    font-size: 13px !important;
    display: flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    margin-top: 0 !important;
    padding-left: 55px;      /* ubah jarak tulisan online ke foto */
  }

  .user-panel .info a .fa-circle {
    font-size: 8px !important;
    color: #10b981 !important;
    animation: pulse 2s infinite;
  }

  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
  }

  /* ========================================
     MENU SIDEBAR (Tulisan MENU SYSTEM/MENU LAIN)
     ======================================== */
  .sidebar-menu > li.header {
    color: #c7d4e7 !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 1.5px !important;
    padding: 20px 25px 12px 25px !important;
    margin-top: 10px;
    position: relative;
  }

  .sidebar-menu > li.header::before {
    content: '';
    position: absolute;
    left: 25px;
    bottom: 8px;
    width: 30px;
    height: 2px;
    background: linear-gradient(90deg, #75aaff, transparent);   /* garis slirid di bawah menu sistem */
    border-radius: 2px;
  }

  .sidebar-menu > li > a {      /* tulisan dashboard dll */
    color: #e1cbda !important;
    font-weight: 500 !important;
    font-size: 14px !important;
    padding: 14px 25px !important;
    border-radius: 12px !important;
    margin: 4px 15px !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    border: none !important;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .sidebar-menu > li > a:hover {
    background: rgba(59, 130, 246, 0.15) !important;
    color: #ffffff !important;
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
  }

  .sidebar-menu > li.active > a {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
    color: white !important;
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.4);
    font-weight: 600 !important;
  }

  .sidebar-menu li > a > .fa,
  .sidebar-menu li > a > .ion,
  .sidebar-menu li > a > .glyphicon {
    width: 24px !important;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px !important;
    transition: all 0.3s ease;
  }

  .sidebar-menu > li.active > a > .fa,
  .sidebar-menu > li.active > a > .ion {
    transform: scale(1.1);
  }

  /* Treeview Menu */
  .treeview-menu {
    background: rgba(0, 0, 0, 0.1) !important;
    border-radius: 8px;
    margin: 2px 25px !important;
    padding: 8px 0 !important;
  }

  .treeview-menu > li > a {
    color: #94a3b8 !important;
    font-weight: 500 !important;
    font-size: 13px !important;
    padding: 10px 20px 10px 55px !important;
    transition: all 0.3s ease !important;
    border-radius: 8px !important;
    margin: 2px 10px !important;
  }

  .treeview-menu > li > a:hover {
    background: rgba(59, 130, 246, 0.1) !important;
    color: #fcc9c9 !important;
    padding-left: 60px !important;
  }

  .treeview-menu > li.active > a {
    color: #3b82f6 !important;
    background: rgba(59, 130, 246, 0.15) !important;
    font-weight: 600 !important;
  }

  .pull-right-container > .fa-angle-left {
    transition: transform 0.3s ease !important;
    opacity: 0.7;
  }

  .treeview-menu-open > .pull-right-container > .fa-angle-left {
    transform: rotate(90deg);
  }

  /* ========================================
     CONTENT WRAPPER
     ======================================== */

         /* Content Header - Area Beranda */
    .content-header {
      background: linear-gradient(135deg,#f9f8fc 0%, #e2e8f0 100%) !important;
      padding: 10px 10px !important; /* 🔥 INI yang mengatur jarak */
      margin: 0 !important;
      border-bottom: none !important;

    }
  .content-wrapper {
    margin-left: 280px !important;
    margin-top: 70px !important;
    min-height: calc(100vh - 70px);
    background: linear-gradient(135deg, #f9f8fc 0%, #e2e8f0 100%);
    padding: 30px;
    transition: all 0.3s ease;
  }

  /* ========================================
     PROGRESS CARD
     ======================================== */
  .box-progress {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
    animation: fadeInUp 0.6s ease;
  }

  .box-progress::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
  }

  .title-progress {
    font-size: 20px;
    font-weight: 700;
    color: white;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
    z-index: 1;
  }

  .title-progress i {
    font-size: 28px;
    background: rgba(255, 255, 255, 0.2);
    padding: 10px;
    border-radius: 12px;
  }

  .progress {
    height: 35px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.2);
    overflow: hidden;
    box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1;
  }

  .progress-bar {
    background: linear-gradient(90deg, #fff 0%, #f0f0f0 100%);
    line-height: 35px;
    font-weight: 700;
    font-size: 15px;
    color: #667eea;
    transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
  }

  .progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    animation: shimmer 2s infinite;
  }

  @keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
  }

  /* ========================================
     INFO CARDS (Small Boxes)
     ======================================== */
  .small-box {
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    animation: fadeInUp 0.6s ease backwards;
  }

  .small-box:nth-child(1) { animation-delay: 0.1s; }
  .small-box:nth-child(2) { animation-delay: 0.2s; }
  .small-box:nth-child(3) { animation-delay: 0.3s; }

  .small-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: rgba(255, 255, 255, 0.3);
  }

  .small-box:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  }

  .small-box.bg-blue {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  }

  .small-box.bg-orange {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  }

  .small-box.bg-green {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  }

  .small-box .inner {
    padding: 0;
    margin-bottom: 20px;
  }

  .small-box h3 {
    font-size: 48px;
    font-weight: 800;
    margin: 0 0 10px 0;
    color: white;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    line-height: 1;
  }

  .small-box p {
    font-size: 15px;
    color: rgba(255, 255, 255, 0.95);
    margin: 0;
    font-weight: 500;
  }

  .small-box .icon {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 80px;
    color: rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
  }

  .small-box:hover .icon {
    transform: translateY(-50%) scale(1.1);
    color: rgba(255, 255, 255, 0.3);
  }

  .small-box-footer {
    background: rgba(255, 255, 255, 0.2);
    padding: 12px 20px;
    text-align: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
    border-radius: 12px;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: auto;
    text-decoration: none;
  }

  .small-box-footer:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    color: white;
  }

  /* ========================================
     ALERTS
     ======================================== */
  .alert-modern {
    border-radius: 15px;
    padding: 20px 25px;
    margin-bottom: 25px;
    border: none;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 15px;
    animation: slideIn 0.5s ease;
  }

  @keyframes slideIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .alert-modern .fa {
    font-size: 32px;
  }

  .alert-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: white;
  }

  .alert-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
  }

  /* ========================================
     BADGES & LABELS
     ======================================== */
  .label {
    border-radius: 20px !important;
    padding: 4px 12px !important;
    font-weight: 600 !important;
    font-size: 11px !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  }

  /* ========================================
     ANIMATIONS
     ======================================== */
  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* ========================================
     RESPONSIVE DESIGN
     ======================================== */
  @media (max-width: 768px) {
    .main-sidebar { transform: translateX(-100%); }
    .sidebar-open .main-sidebar { transform: translateX(0); }
    .navbar { margin-left: 0 !important; }
    .logo { width: 50px !important; }
    .content-wrapper { margin-left: 0 !important; padding: 15px; }
    
    .box-progress { padding: 20px; }
    .small-box { padding: 20px; margin-bottom: 20px; }
    .small-box h3 { font-size: 36px; }
    .small-box .icon { font-size: 60px; }
    
    .user-panel {
      flex-direction: row;
      gap: 15px;
      padding: 20px 15px !important;
    }
    .user-panel .img-circle {
      width: 50px !important;
      height: 50px !important;
    }
    .user-panel .info p { font-size: 14px !important; }
    .user-panel .info a { font-size: 12px !important; }
  }

  /* ========================================
     SMOOTH TRANSITIONS
     ======================================== */
  .sidebar-menu li,
  .sidebar-menu a,
  .user-panel,
  .logo,
  .small-box,
  .content-wrapper {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* ========================================
     CUSTOM SCROLLBAR
     ======================================== */
  ::-webkit-scrollbar { width: 8px; }
  ::-webkit-scrollbar-track { background: #f1f1f1; }
  ::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
  }
  ::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #764ba2 0%, #667eea 100%);
  }

  /* =====================================
   MOBILE HEADER PERFECT ALIGNMENT
===================================== */
@media (max-width: 768px){

    html,
    body{
        overflow-x: hidden !important;
    }

    /* ===== HEADER ATAS ===== */
    .main-header{
        position: fixed !important;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 9999;
    }

    /* ===== LOGO ===== */
    .main-header .logo{
        width: 100% !important;
        height: 50px !important;
        display: flex !important;
        align-items: center;
        justify-content: flex-start;
        padding-left: 15px !important;
        font-size: 18px !important;
    }

    .logo-lg{
        display: none !important;
    }

    .logo-mini{
        display: block !important;
        color: #fff !important;
        font-size: 18px !important;
        font-weight: 700;
    }

    /* ===== NAVBAR BAWAH ===== */
    .main-header .navbar{
        margin-left: 0 !important;
        width: 100% !important;
        height: 55px !important;
        min-height: 55px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between;
        padding: 0 12px !important;
        border: none !important;
    }

    /* ===== HAMBURGER KIRI ===== */
    .sidebar-toggle{
        width: 42px;
        height: 55px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        margin: 0 !important;
        float: left !important;
        font-size: 20px;
    }

    /* ===== MENU KANAN ===== */
    .navbar-custom-menu{
        margin-left: auto !important;
    }

    .navbar-custom-menu .navbar-nav{
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 10px;
        margin: 0 !important;
        height: 55px;
    }

    .navbar-custom-menu .navbar-nav > li{
        display: flex !important;
        align-items: center !important;
        height: 55px;
    }

    .navbar-custom-menu .navbar-nav > li > a{
        height: 55px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 4px !important;
        margin: 0 !important;
    }

    /* ===== ICON ===== */
    .navbar-nav i,
    .navbar-nav .fa{
        font-size: 18px !important;
    }

    /* ===== FOTO PROFILE ===== */
    .user-menu .user-image{
        width: 34px !important;
        height: 34px !important;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 !important;
        display: block;
    }

    /* ===== HIDE TEXT ===== */
    .hidden-xs{
        display: none !important;
    }

    /* ===== CONTENT ===== */
    .content-wrapper{
        margin-top: 105px !important;
        margin-left: 0 !important;
        padding: 10px !important;
    }

    .content{
        padding: 0 !important;
    }

    /* ===== BOX ===== */
    .box{
        margin-top: 5px !important;
        border-radius: 14px;
        overflow: hidden;
    }

    /* ===== SIDEBAR ===== */
    .main-sidebar{
        padding-top: 105px !important;
    }

}
</style>
</head>

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    <header class="main-header">
      <a href="index_pst.php" class="logo">
        <span class="logo-mini"><b>🎓 Portal BKK</b></span>
        <span class="logo-lg">🎓 Portal BKK</span>
      </a>

      <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" style="color: #ffffff; font-size: 20px;">
          <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <li class="dropdown notifications-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-bell-o" style="font-size: 20px;"></i>
                <span class="label label-warning"><?php
                // Notification count here
                ?></span>
              </a>
            </li>

            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img src="<?php echo $foto_peserta; ?>" class="user-image"
                  style="width:40px;height:40px;margin:-10px 0 -10px 0;border-radius:50%;object-fit:cover;margin-right:15px;" alt="User Image">
                <span class="hidden-xs" style="font-weight: 600;"><?php echo $data_nisn ?></span>
              </a>
              <ul class="dropdown-menu">
                <li
                  style="text-align:center;padding:25px 20px;background:linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                  <img src="<?php echo $foto_peserta; ?>"
                    style="width:70px;height:70px;border-radius:50%;margin-bottom:12px;object-fit:cover;border:3px solid rgba(255,255,255,0.3);">
                  <h4 style="color:white;margin:10px 0 5px 0;font-weight:700;"><?php echo $profil['nama']; ?></h4>
                  <p style="color:rgba(255,255,255,0.9);margin:0;font-size:13px;">NISN: <?php echo $data_nisn; ?></p>
                </li>
              <!--  <li><a href="?halaman=profile"><i class="fa fa-user" style="margin-right:10px;"></i> Profil saya</a>
                </li>
                <li><a href="?halaman=riwayat"><i class="fa fa-history" style="margin-right:10px;"></i> Riwayat
                    hidup</a></li>
                <li><a href="?halaman=lamaran"><i class="fa fa-briefcase" style="margin-right:10px;"></i> Daftar
                    lamaran</a></li>
                <li><a href="?halaman=pengaturan"><i class="fa fa-cog" style="margin-right:10px;"></i> Pengaturan
                    akun</a></li> -->
                <li style="border-top:2px solid #f1f5f9;">
                  <a data-toggle="modal" data-target="#exampleModal"><i class="fa fa-sign-out"
                      style="margin-right:10px;"></i> Keluar</a>
                </li>
              </ul>
            </li>

            <!-- <li>
              <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears" style="font-size: 20px;"></i></a>
            </li> -->
          </ul>
        </div>
      </nav>
    </header>

    <aside class="main-sidebar">
      <section class="sidebar">

        <div class="user-panel">
          <div class="image">
            <img src="<?php echo $foto_peserta; ?>" class="img-circle" alt="User Image">
          </div>
          <div class="info">
            <p><?php echo $data_nisn ?></p>
            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">
          <li class="header">Menu System</li>

          <li>
            <a href="?halaman=beranda">
              <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            </a>
          </li>

         <li class="treeview"> 
            <a href="?halaman=loker">
              <i class="fa fa-briefcase"></i>
              <span>Lowongan</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="?halaman=loker"><i class="fa fa-circle-o"></i> Lowongan Kerja</a></li>
               <li><a href="?halaman=pendaftar"><i class="fa fa-circle-o"></i> Daftar Saya</a></li> 
            </ul>
          </li> 

          <li class="treeview">
            <a href="#">
              <i class="fa fa-info-circle"></i>
              <span>Informasi</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="?halaman=jadwal"><i class="fa fa-circle-o"></i> Jadwal</a></li>
              <li><a href="?halaman=hasil"><i class="fa fa-circle-o"></i> Pengumuman Hasil</a></li>
            </ul>
          </li> 

          <li>
            <a href="?halaman=profile_peserta&id_siswa=<?php echo $id_siswa; ?>">
              <i class="fa fa-info-circle"></i> <span>Profil</span>
            </a>
          </li>

          <li>
            <a href="?halaman=tracer">
              <i class="fa fa-graduation-cap"></i> <span>Tracer Study</span>
            </a>
          </li>

          <li class="header">Menu Lain</li>

          <li>
            <a class="nav-link" data-toggle="modal" data-target="#exampleModal">
              <i class="fa fa-sign-out"></i> <span>Logout</span>
            </a>
          </li>
        </ul>

      </section>
    </aside>

    <div class="content-wrapper">
      <div class="container-fluid">
        <?php
        if (isset($_GET['halaman'])) {
          $hal = $_GET['halaman'];

          switch ($hal) {
            case 'beranda':
              include "../pages/beranda.php";
              break;
            case 'profile':
              include "../pages/profile.php";
              break;
            case 'upload_foto_peserta':
              include "upload_foto_peserta.php";
              break;
            case 'profile_edit':
              include "profile_edit.php";
              break;
            case 'profile_update':
              include "profile_update.php";
              break;
            case 'loker':
              include "loker_tampil.php";
              break;
            case 'daftar':
              include "daftar.php";
              break;
            case 'daftar_tambah':
              include "daftar_tambah.php";
              break;
            case 'pendaftar':
              include "pendaftaran.php";
              break;
            case 'jadwal':
              include "jadwal_tampil.php";
              break;
            case 'hasil':
              include "hasil_tampil.php";
              break;
            case 'batal_lamaran':
              include "batal_lamaran.php";
              break;
            case 'tracer':
              include "tracer_form.php";
              break;
            case 'profile_peserta':
              include "profile_peserta.php";
              break;
            case 'edit_foto':
              include "edit_foto.php";
              break;
            default:
              echo "<center><h3> ERROR !</h3></center>";
              break;
          }
        } else {
          include "../pages/beranda.php";
        }
        ?>
      </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
          <div class="modal-header"
            style="background:linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);border-radius:16px 16px 0 0;padding:25px;">
            <h5 class="modal-title" id="exampleModalLabel" style="color:white;font-weight:700;">👋 Yakin Keluar?</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close" style="color:white;opacity:1;">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body" style="padding:25px;font-size:15px;color:#475569;">
            Apakah Anda yakin ingin keluar dari sistem?
          </div>
          <div class="modal-footer" style="border:none;padding:20px 25px;">
            <button class="btn" type="button" data-dismiss="modal"
              style="background:#f1f5f9;color:#475569;font-weight:600;padding:10px 24px;border-radius:10px;">Cancel</button>
            <a class="btn" href="../logout.php"
              style="background:linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);color:white;font-weight:600;padding:10px 24px;border-radius:10px;box-shadow:0 4px 12px rgba(59, 130, 246, 0.3);">Logout</a>
          </div>
        </div>
      </div>
    </div>

    <aside class="control-sidebar control-sidebar-dark" style="display: none;">
      <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      </ul>
      <div class="tab-content">
        <div class="tab-pane" id="control-sidebar-home-tab">
        </div>
      </div>
    </aside>
    <div class="control-sidebar-bg"></div>
  </div>

  <script src="../bower_components/jquery/dist/jquery.min.js"></script>
  <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  <script src="../dist/js/adminlte.min.js"></script>

  <script>
    $(document).ready(function () {
      // Smooth hover effects
      $('.sidebar-menu a').hover(
        function () {
          $(this).find('i').css('transform', 'scale(1.1)');
        },
        function () {
          $(this).find('i').css('transform', 'scale(1)');
        }
      );

      // Active menu highlight
      $('.sidebar-menu a').on('click', function () {
        $('.sidebar-menu li').removeClass('active');
        $(this).parent('li').addClass('active');
      });
    });
  </script>
</body>

</html>