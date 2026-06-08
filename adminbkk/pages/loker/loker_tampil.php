<?php
if ($data_status == "admin" || $data_status == "Ka. BKK") {

    $sql_loker = mysqli_query($con, "
    SELECT l.*, p.nama_perusahaan 
    FROM tb_lowongan l
    JOIN tb_perusahaan p ON l.id_perusahaan = p.id_perusahaan
    ORDER BY l.batas_lamaran DESC
");

 // ================= BULLET LIST =================
    function bulletList($text)
    {
        $items = preg_split('/\r\n|\r|\n/', trim($text));

        $html = "<ul style='padding-left:20px; margin:0;'>";

        foreach ($items as $item) {
            $item = trim($item);

            if ($item != "") {
                $item = ltrim($item, "•-* ");
                $html .= "<li>" . htmlspecialchars($item) . "</li>";
            }
        }

        $html .= "</ul>";

        return $html;
    }

    $selected_id = isset($_GET['detail']) ? trim($_GET['detail']) : "";

    $data_detail = null;
    $data_perusahaan = null;

    if ($selected_id != "") {
        $sql_detail = mysqli_query($con, "
    SELECT l.*, p.nama_perusahaan 
    FROM tb_lowongan l
    JOIN tb_perusahaan p ON l.id_perusahaan = p.id_perusahaan
    WHERE l.id_lowongan='$selected_id'
");
        $data_detail = mysqli_fetch_array($sql_detail, MYSQLI_BOTH);

        if ($data_detail) {
            $id_perusahaan = $data_detail['id_perusahaan'];

            $sql_perusahaan = mysqli_query($con, "
    SELECT * FROM tb_perusahaan 
    WHERE id_perusahaan='$id_perusahaan'
");

            $data_perusahaan = mysqli_fetch_array($sql_perusahaan, MYSQLI_BOTH);
            // ✅ QUERY SOSIAL MEDIA PERUSAHAAN
            $sosmed_data = [];
            if ($data_perusahaan && !empty($data_perusahaan['id_user'])) {
                $id_user_perusahaan = mysqli_real_escape_string($con, $data_perusahaan['id_user']);
                $sql_sosmed = mysqli_query($con, "
        SELECT * FROM tb_sosial_media 
        WHERE id_user = '$id_user_perusahaan'
        ORDER BY nama_platform ASC
    ");
                if ($sql_sosmed) {
                    while ($s = mysqli_fetch_assoc($sql_sosmed)) {
                        $sosmed_data[] = $s;
                    }
                }
            }
        }
    }
    ?>

<style>
html,
body {
    overflow-y: auto;
}

/* === MODERN ENHANCED STYLES === */

.job-container {
    display: flex;
    gap: 25px;
    height: calc(100vh - 180px);
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 10px;
    border-radius: 20px;
}

.job-list {
    width: 35%;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    padding: 25px;
    overflow-y: auto;
    border: none;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
}

.job-detail {
    width: 65%;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    padding: 35px;
    overflow-y: auto;
    border: none;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
}

.job-item {
    border: 2px solid #e8ecf1;
    padding: 20px;
    border-radius: 16px;
    margin-bottom: 18px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
    position: relative;
    overflow: hidden;
}

.job-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.job-item:hover {
    background: linear-gradient(135deg, #3f0745 0%, #9d8afa 100%);
    border-color: transparent;
    transform: translateX(8px);
    box-shadow: 0 8px 24px rgba(240, 147, 251, 0.3);
    color: white;
}

.job-item:hover::before {
    opacity: 1;
}

.job-item:hover .job-title,
.job-item:hover .job-company,
.job-item:hover .job-date {
    color: white !important;
}

.job-item.active {
    background: linear-gradient(135deg, #0a164b 0%, #a877d8 100%);
    border: 2px solid transparent;
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
    color: white;
}

.job-item.active .job-title,
.job-item.active .job-company,
.job-item.active .job-date {
    color: white !important;
}

.job-item.active::before {
    opacity: 1;
}

.job-title {
    font-size: 17px;
    font-weight: 700;
    margin-bottom: 8px;
    transition: color 0.3s ease;
}

.job-company {
    font-size: 14px;
    color: #555;
    margin-bottom: 10px;
    font-weight: 500;
    transition: color 0.3s ease;
}

.job-date {
    font-size: 12px;
    color: #888;
    margin-top: 10px;
    display: flex;
    h align-items: center;
    gap: 5px;
    transition: color 0.3s ease;
}

.badge-status {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    display: inline-block;
    margin-top: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.status-tampil {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: #fff;
}

.status-arsip {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
    color: #fff;
}

.status-tangguhkan {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: #fff;
}

.company-profile {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border: 2px solid #e8ecf1;
    border-radius: 20px;
    padding: 30px;
    margin-top: 30px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.company-profile:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
}

.content-wrapper {
    padding-top: 29px;
    margin-top: 50px !important;
}

/* Enhanced Box Styling */
.box.box-primary {
    background: transparent !important;
    box-shadow: none !important;
    border: none !important;
    margin-top: -25px !important;
}

.box-header.with-border {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px 30px;
    border-radius: 16px 16px 0 0;
    border: none !important;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.box-header h3 {
    font-size: 22px;
    font-weight: 700;
    margin: 0;
}

.box-body {
    background: transparent;
    padding: 0;
}

/* Button Enhancements */
.btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 10px 20px;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
}

.btn-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.btn-warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.btn-danger {
    background: linear-gradient(135deg, #fa709a 0%, #f45c43 100%);
}

.btn:hover {
    transform: translateY(-2px);
}

/* Scrollbar Styling */
.job-list::-webkit-scrollbar,
.job-detail::-webkit-scrollbar {
    width: 8px;
}

.job-list::-webkit-scrollbar-track,
.job-detail::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.job-list::-webkit-scrollbar-thumb,
.job-detail::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

/* Modal Enhancement */
.modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px 20px 0 0;
    padding: 25px 30px;
}

.modal-title {
    font-weight: 700;
    font-size: 20px;
}

.modal-body {
    padding: 30px;
}

.form-control {
    border-radius: 10px;
    border: 2px solid #e8ecf1;
    padding: 5px 15px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}


/* Responsive */
@media (max-width: 991px) {
    .job-container {
        flex-direction: column;
        height: auto;
    }

    .job-list,
    .job-detail {
        width: 100%;
    }
}

/* Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.job-item {
    animation: fadeIn 0.5s ease forwards;
}

.job-item:nth-child(1) {
    animation-delay: 0.1s;
}

.job-item:nth-child(2) {
    animation-delay: 0.2s;
}

.job-item:nth-child(3) {
    animation-delay: 0.3s;
}

/* ✅ SOSIAL MEDIA STYLES */
.sosmed-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 12px;
}

.sosmed-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.3s ease;
    border: 2px solid;
    color: inherit;
}

.sosmed-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    color: white !important;
}

.sosmed-facebook {
    color: #1877f2;
    border-color: #1877f2;
}

.sosmed-facebook:hover {
    background: #1877f2;
}

.sosmed-instagram {
    color: #e4405f;
    border-color: #e4405f;
}

.sosmed-instagram:hover {
    background: #e4405f;
}

.sosmed-twitter {
    color: #1da1f2;
    border-color: #1da1f2;
}

.sosmed-twitter:hover {
    background: #1da1f2;
}

.sosmed-linkedin {
    color: #0077b5;
    border-color: #0077b5;
}

.sosmed-linkedin:hover {
    background: #0077b5;
}

.sosmed-youtube {
    color: #ff0000;
    border-color: #ff0000;
}

.sosmed-youtube:hover {
    background: #ff0000;
}

.sosmed-tiktok {
    color: #000000;
    border-color: #000000;
}

.sosmed-tiktok:hover {
    background: #000000;
    color: white;
}

.sosmed-whatsapp {
    color: #25d366;
    border-color: #25d366;
}

.sosmed-whatsapp:hover {
    background: #25d366;
}

.sosmed-website {
    color: #667eea;
    border-color: #667eea;
}

.sosmed-website:hover {
    background: #667eea;
}

.sosmed-default {
    color: #6c757d;
    border-color: #6c757d;
}

.sosmed-default:hover {
    background: #6c757d;
}

/* ========================================
   📱 RESPONSIVE MOBILE - ADMIN JOB PANEL
   ======================================== */

/* Base mobile settings - Prevent issues */
@media (max-width: 991px) {

    /* Prevent horizontal scroll */
    html,
    body {
        overflow-y: auto !important;
        overflow-x: auto !important;
        width: 100% !important;
        position: relative;
    }

    /* Fix content wrapper spacing */
    .content-wrapper {
        padding-top: 10px !important;
        margin-top: 60px !important;
    }

    /* Box container */
    .box.box-primary {
        margin-top: 0 !important;
    }

    .box-header.with-border {
        padding: 15px 20px !important;
        border-radius: 12px 12px 0 0 !important;
        flex-wrap: wrap !important;
        gap: 10px !important;
    }

    .box-header h3 {
        font-size: 18px !important;
        width: 100% !important;
        margin-bottom: 5px !important;
    }

    .box-tools {

        width: 100% !important;

        display: flex !important;
        justify-content: center !important;
        align-items: center !important;

        margin: 0 auto !important;

        float: none !important;
    }

    .box-tools .btn {

        width: 100% !important;
        max-width: 250px !important;

        height: 48px !important;

        display: flex !important;
        align-items: center !important;
        justify-content: center !important;

        margin: 0 auto !important;

        border-radius: 14px !important;
    }

    /* Main Container */
    .job-container {
        flex-direction: column !important;
        height: auto !important;
        min-height: auto !important;
        padding: 15px !important;
        gap: 15px !important;
    }

    .job-list,
    .job-detail {
        width: 100% !important;
        max-height: none !important;
        height: auto !important;
        padding: 20px !important;
        border-radius: 16px !important;
    }

    .job-list {
        order: 1;
        margin-bottom: 10px;
    }

    .job-detail {
        order: 2;
    }

    /* Job Items */
    .job-item {
        padding: 15px !important;
        margin-bottom: 12px !important;
        border-radius: 12px !important;
        min-height: 80px;
        /* Touch target area */
    }

    .job-item:hover {
        transform: translateX(4px) !important;
    }

    .job-title {
        font-size: 15px !important;
        margin-bottom: 6px !important;
        line-height: 1.3;
    }

    .job-company {
        font-size: 13px !important;
        margin-bottom: 8px !important;
    }

    .job-date {
        font-size: 12px !important;
        margin-top: 8px !important;
    }

    .badge-status {
        padding: 5px 12px !important;
        font-size: 10px !important;
        margin-top: 8px !important;
    }

    /* Detail Content */
    .job-detail h2 {
        font-size: 20px !important;
        line-height: 1.4;
    }

    .job-detail>p {
        font-size: 14px !important;
    }

    .job-detail h4 {
        font-size: 16px !important;
        margin: 20px 0 12px 0 !important;
    }

    .job-detail p {
        font-size: 14px !important;
        line-height: 1.7;
    }

    /* Action Buttons */
    .job-detail>div[style*="display:flex"] {
        flex-wrap: wrap !important;
        gap: 8px !important;
    }

    .job-detail .btn {
        padding: 8px 14px !important;
        font-size: 13px !important;
        min-height: 44px !important;
        /* Touch target */
        width: calc(50% - 4px) !important;
    }

    /* Company Profile */
    .company-profile {
        padding: 20px !important;
        margin-top: 20px !important;
        border-radius: 16px !important;
    }

    .company-profile h4 {
        font-size: 18px !important;
    }

    .company-profile p {
        font-size: 14px !important;
    }

    /* Social Media Links */
    .sosmed-container {
        justify-content: flex-start !important;
        gap: 8px !important;
    }

    .sosmed-link {
        padding: 8px 14px !important;
        font-size: 12px !important;
        min-height: 40px !important;
    }

    .sosmed-link i {
        font-size: 14px !important;
    }

    /* Empty State */
    .job-detail>div[style*="text-align:center"] {
        padding-top: 40px !important;
    }

    .job-detail>div[style*="text-align:center"] div {
        font-size: 50px !important;
    }

    .job-detail>div[style*="text-align:center"] h3 {
        font-size: 18px !important;
    }

    .job-detail>div[style*="text-align:center"] p {
        font-size: 13px !important;
    }

    /* Modal Responsive */
    .modal-dialog {
        margin: 10px !important;
        width: auto !important;
        max-width: 95vw !important;
    }

    .modal-content {
        border-radius: 16px !important;
    }

    .modal-header {
        padding: 15px 20px !important;
        border-radius: 16px 16px 0 0 !important;
    }

    .modal-title {
        font-size: 18px !important;
    }

    .modal-body {
        padding: 20px !important;
        max-height: 65vh !important;
        overflow-y: auto !important;
    }

    .modal-footer {
        padding: 15px 20px !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 15px !important;
    }

    .form-control {
        padding: 12px 14px !important;
        font-size: 16px !important;
        /* Prevent iOS zoom */
        min-height: 44px !important;
        border-radius: 10px !important;
    }

    textarea.form-control {
        min-height: 100px !important;
        resize: vertical !important;
    }

    select.form-control {
        min-height: 44px !important;
    }

    .form-group label {
        font-size: 14px !important;
        margin-bottom: 6px !important;
        display: block !important;
    }

    /* Scrollbars */
    .job-list::-webkit-scrollbar,
    .job-detail::-webkit-scrollbar,
    .modal-body::-webkit-scrollbar {
        width: 4px !important;
    }
}

/* Medium Mobile (481px - 767px) */
@media (max-width: 767px) {
    body {
        padding: 8px !important;
    }

    .box-header.with-border {
        padding: 12px 15px !important;
    }

    .job-container {
        padding: 12px !important;
    }

    .job-list,
    .job-detail {
        padding: 18px !important;
    }

    .job-item {
        padding: 14px !important;
    }

    .job-title {
        font-size: 15px !important;
    }

    .job-company {
        font-size: 13px !important;
    }

    .job-detail h2 {
        font-size: 19px !important;
    }

    .job-detail h4 {
        font-size: 15px !important;
    }

    .job-detail .btn {
        width: 100% !important;
        padding: 10px !important;
        font-size: 14px !important;
    }

    .company-profile {
        padding: 18px !important;
    }

    .sosmed-link {
        padding: 7px 12px !important;
        font-size: 12px !important;
    }

    .modal-body {
        max-height: 70vh !important;
        padding: 18px !important;
    }

    .form-control {
        font-size: 16px !important;
    }
}

/* Small Mobile (376px - 480px) */
@media (max-width: 480px) {
    body {
        padding: 6px !important;
    }

    .box-header.with-border {
        padding: 10px 12px !important;
    }

    .box-header h3 {
        font-size: 16px !important;
    }

    .box-tools .btn {
        padding: 6px 12px !important;
        font-size: 12px !important;
    }

    .job-container {
        padding: 10px !important;
    }

    .job-list,
    .job-detail {
        padding: 16px !important;
        border-radius: 14px !important;
    }

    .job-item {
        padding: 12px !important;
        margin-bottom: 10px !important;
    }

    .job-title {
        font-size: 14px !important;
    }

    .job-company,
    .job-date {
        font-size: 12px !important;
    }

    .badge-status {
        padding: 4px 10px !important;
        font-size: 9px !important;
    }

    .job-detail h2 {
        font-size: 18px !important;
    }

    .job-detail h4 {
        font-size: 14px !important;
    }

    .job-detail p {
        font-size: 13px !important;
        line-height: 1.6;
    }

    .job-detail .btn {
        padding: 9px !important;
        font-size: 13px !important;
        min-height: 44px !important;
    }

    .company-profile {
        padding: 16px !important;
    }

    .company-profile h4 {
        font-size: 16px !important;
    }

    .company-profile p {
        font-size: 13px !important;
    }

    .sosmed-container {
        gap: 6px !important;
    }

    .sosmed-link {
        padding: 6px 10px !important;
        font-size: 11px !important;
        min-height: 38px !important;
    }

    .sosmed-link i {
        font-size: 13px !important;
    }

    .empty-state div {
        font-size: 40px !important;
    }

    .empty-state h3 {
        font-size: 16px !important;
    }

    .empty-state p {
        font-size: 12px !important;
    }

    /* Modal */
    .modal-header {
        padding: 12px 16px !important;
    }

    .modal-title {
        font-size: 16px !important;
    }

    .modal-body {
        padding: 16px !important;
        max-height: 60vh !important;
    }

    .modal-footer {
        padding: 12px 16px !important;
    }

    .form-control {
        padding: 10px 12px !important;
        font-size: 16px !important;
    }

    .form-group label {
        font-size: 13px !important;
    }

    textarea.form-control {
        min-height: 80px !important;
    }
}

/* Extra Small Mobile (≤ 375px) */
@media (max-width: 375px) {
    .box-header h3 {
        font-size: 15px !important;
    }

    .job-item {
        padding: 10px !important;
    }

    .job-title {
        font-size: 14px !important;
    }

    .job-company,
    .job-date {
        font-size: 11px !important;
    }

    .job-detail h2 {
        font-size: 17px !important;
    }

    .job-detail h4 {
        font-size: 14px !important;
    }

    .job-detail p {
        font-size: 13px !important;
    }

    .job-detail .btn {
        padding: 8px !important;
        font-size: 12px !important;
    }

    .company-profile h4 {
        font-size: 15px !important;
    }

    .sosmed-link {
        padding: 5px 8px !important;
        font-size: 10px !important;
    }

    .form-control {
        padding: 9px 10px !important;
    }

    .modal-title {
        font-size: 15px !important;
    }
}

/* Very Small Mobile (≤ 320px) */
@media (max-width: 320px) {
    .job-title {
        font-size: 13px !important;
    }

    .job-company,
    .job-date {
        font-size: 10px !important;
    }

    .job-detail h2 {
        font-size: 16px !important;
    }

    .job-detail h4 {
        font-size: 13px !important;
    }

    .job-detail .btn {
        font-size: 11px !important;
        padding: 7px !important;
    }

    .sosmed-link {
        font-size: 9px !important;
        padding: 4px 6px !important;
    }

    .form-control {
        font-size: 16px !important;
        padding: 8px 10px !important;
    }
}

/* Tablet Portrait (768px - 991px) */
@media (min-width: 768px) and (max-width: 991px) {
    .job-container {
        padding: 18px !important;
    }

    .job-list {
        width: 48% !important;
        padding: 22px !important;
    }

    .job-detail {
        width: 48% !important;
        padding: 22px !important;
    }

    .job-detail .btn {
        width: auto !important;
    }
}

/* Landscape Mode on Mobile */
@media (max-height: 500px) and (orientation: landscape) {
    .job-container {
        height: auto !important;
        max-height: calc(100vh - 120px) !important;
        overflow-y: auto !important;
    }

    .job-list,
    .job-detail {
        max-height: none !important;
        height: auto !important;
    }

    .modal-body {
        max-height: 75vh !important;
    }

    .modal-dialog {
        margin: 5px !important;
    }
}

/* Touch Device Optimizations */
@media (hover: none) and (pointer: coarse) {

    /* Remove hover effects, use active instead */
    .job-item:hover {
        transform: none !important;
    }

    .job-item:active {
        transform: scale(0.99) !important;
        opacity: 0.95 !important;
    }

    .btn:hover,
    .sosmed-link:hover {
        transform: none !important;
    }

    .btn:active,
    .sosmed-link:active {
        transform: scale(0.98) !important;
        opacity: 0.9 !important;
    }

    /* Ensure minimum touch target sizes */
    .job-item,
    .btn,
    .sosmed-link,
    .form-control,
    select.form-control,
    textarea.form-control {
        min-height: 44px !important;
        min-width: 44px !important;
    }

    /* Prevent zoom on input focus iOS */
    input.form-control,
    select.form-control,
    textarea.form-control {
        font-size: 16px !important;
    }

    /* Better tap feedback */
    a,
    button,
    [onclick] {
        -webkit-tap-highlight-color: transparent;
    }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {

    .job-list,
    .job-detail,
    .company-profile {
        background: rgba(30, 41, 59, 0.95) !important;
    }

    .job-item {
        background: #1e293b !important;
        border-color: #334155 !important;
    }

    .job-title {
        color: #f1f5f9 !important;
    }

    .job-company,
    .job-date {
        color: #94a3b8 !important;
    }

    .job-detail h2,
    .job-detail h4 {
        color: #f1f5f9 !important;
    }

    .job-detail p {
        color: #cbd5e1 !important;
    }

    .company-profile h4 {
        color: #818cf8 !important;
    }

    .company-profile p {
        color: #cbd5e1 !important;
    }

    .sosmed-link {
        background: #1e293b !important;
        color: #f1f5f9 !important;
    }

    .form-control {
        background: #1e293b !important;
        border-color: #334155 !important;
        color: #f1f5f9 !important;
    }

    .form-control:focus {
        background: #0f172a !important;
        border-color: #667eea !important;
    }

    .form-control::placeholder {
        color: #64748b !important;
    }

    .modal-content {
        background: #1e293b !important;
    }

    .modal-body {
        background: #1e293b !important;
    }

    .modal-footer {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
}

/* Reduced Motion Preference */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }

    .job-item:hover,
    .btn:hover,
    .sosmed-link:hover,
    .company-profile:hover {
        transform: none !important;
    }
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
    .job-item {
        border-width: 3px !important;
    }

    .badge-status {
        border: 2px solid currentColor !important;
    }

    .btn {
        border: 2px solid currentColor !important;
    }

    .form-control {
        border-width: 3px !important;
    }
}

/* Print Styles */
@media print {

    .box-tools,
    .btn,
    .sosmed-link,
    .modal {
        display: none !important;
    }

    .job-container {
        display: block !important;
        height: auto !important;
    }

    .job-list,
    .job-detail {
        width: 100% !important;
        page-break-inside: avoid !important;
    }

    .job-item {
        break-inside: avoid !important;
    }

    body {
        background: #fff !important;
        color: #000 !important;
    }
}

/* Accessibility: Focus Visible */
@media (prefers-reduced-motion: no-preference) {

    .form-control:focus,
    .btn:focus,
    .sosmed-link:focus {
        outline: 2px solid #667eea !important;
        outline-offset: 2px !important;
    }
}

/* Prevent text overflow on small screens */
@media (max-width: 480px) {

    .job-title,
    .job-company,
    .job-detail h2,
    .job-detail h4,
    .company-profile h4 {
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
        hyphens: auto !important;
    }

    .job-company,
    .job-date {
        white-space: nowrap !important;
        overflow: auto !important;
        text-overflow: ellipsis !important;
        max-width: 100% !important;
    }
}

/* Fix for iOS Safari viewport height */
@supports (-webkit-touch-callout: none) {
    @media (max-width: 767px) {

        .job-list,
        .job-detail {
            max-height: calc(100vh - 250px) !important;
        }
    }
}

/* Fix for Android Chrome address bar */
@supports (-webkit-overflow-scrolling: touch) {
    @media (max-width: 767px) {
        .job-container {
            min-height: calc(100dvh - 150px) !important;
        }
    }
}

/* =========================================
   RESPONSIVE MODAL TAMBAH LOWONGAN
========================================= */

/* MOBILE */
@media(max-width:991px) {

    #myModal .modal-dialog {

        width: 95% !important;
        max-width: 95% !important;

        margin-top: 115px !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    #myModal .modal-content {
        border-radius: 18px !important;
        overflow: hidden !important;
    }

    #myModal .modal-header {
        padding: 18px !important;
    }

    #myModal .modal-title {
        font-size: 18px !important;
        line-height: 1.5 !important;
    }

    #myModal .modal-body {
        padding: 18px !important;
        max-height: 75vh !important;
        overflow-y: auto !important;
    }

    #myModal .form-group {
        margin-bottom: 15px !important;
    }

    #myModal .form-control {
        min-height: 46px !important;
        font-size: 14px !important;
        border-radius: 12px !important;
    }

    #myModal textarea.form-control {
        min-height: 90px !important;
    }

    #myModal .modal-footer {
        display: flex !important;
        flex-direction: column !important;
        gap: 10px !important;

        padding: 18px !important;
    }

    #myModal .modal-footer .btn {
        width: 100% !important;
        height: 46px !important;

        display: flex !important;
        align-items: center !important;
        justify-content: center !important;

        border-radius: 12px !important;
    }

}
</style>
<br><br>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><b>📋 Daftar Lowongan Kerja</b></h3>

        <div class="box-tools pull-right">
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">
                <i class="fa fa-plus"></i> Tambah Lowongan
            </button>
        </div>
    </div>

    <div class="box-body">
        <div class="job-container">

            <!-- ================= LIST KIRI ================= -->
            <div class="job-list">

                <?php while ($data = mysqli_fetch_array($sql_loker, MYSQLI_BOTH)) {

                        $statusClass = "status-tampil";
                        if ($data['status'] == "nonaktif")
                            $statusClass = "status-arsip";

                        ?>
                <a href="?halaman=loker_tampil&detail=<?php echo $data['id_lowongan']; ?>"
                    style="text-decoration:none;color:black;">
                    <div class="job-item <?php echo ($selected_id == $data['id_lowongan']) ? 'active' : ''; ?>">
                        <div class="job-title"><?php echo $data['judul_lowongan']; ?></div>
                        <div class="job-company"><?php echo $data['nama_perusahaan']; ?></div>
                        <div class="job-date">
                            <i class="fa fa-calendar"></i>
                            Batas: <?php echo date('d F Y', strtotime($data['batas_lamaran'])); ?>
                        </div>
                        <span class="badge-status <?php echo $statusClass; ?>">
                            <?php echo $data['status']; ?>
                        </span>
                    </div>
                </a>
                <?php } ?>
            </div>

            <!-- ================= DETAIL KANAN ================= -->
            <div class="job-detail">

                <?php if ($data_detail == null) { ?>

                <div style="text-align:center;padding-top:60px;color:gray;">
                    <div style="font-size:80px; margin-bottom:20px; opacity:0.3;">📋</div>
                    <h3><b>Pilih lowongan kerja</b></h3>
                    <p>Detail lowongan akan tampil di sini</p>
                </div>

                <?php } else { ?>

                <h2 style="margin-top:0; color:#2d3748; font-weight:700;"><?php echo $data_detail['judul_lowongan']; ?>
                </h2>
                <p style="font-size:16px; color:#555; margin-bottom:20px;">
                    <i class="fa fa-building" style="color:#667eea;"></i> <?php echo $data_detail['nama_perusahaan']; ?>
                    •
                    <i class="fa fa-user" style="color:#667eea;"></i> <?php echo $data_detail['jekel']; ?>
                </p>

                <hr style="border:none; border-top:2px solid #e8ecf1; margin:25px 0;">
                <h4 style="color:#2d3748; font-weight:700; margin-bottom:15px;">
                    <b>💼 Tentang Posisi</b>
                </h4>
                <p style="color:#555; line-height:1.8;">
                    <?php echo nl2br($data_detail['posisi']); ?>
                </p>

                <h4 style="color:#2d3748; font-weight:700; margin-bottom:15px;">
                    <b>🧾 Jenis Pekerjaan</b>
                </h4>
                <p style="color:#555; line-height:1.8;">
                    <?php echo nl2br($data_detail['jenis_pekerjaan']); ?>
                </p>

                <h4 style="color:#2d3748; font-weight:700; margin:25px 0 15px 0;">
                    <b>🛠️ Tugas & Tanggung Jawab</b>
                </h4>
                <p style="color:#555; line-height:1.8;">
                    <?php echo bulletList($data_detail['deskripsi']); ?>
                </p>

                <h4 style="color:#2d3748; font-weight:700; margin:25px 0 15px 0;">
                    <b>🎯 Kualifikasi</b>
                </h4>
                <p style="color:#555; line-height:1.8;">
                    <?php echo bulletList($data_detail['kualifikasi']); ?>
                </p>

                <h4 style="color:#2d3748; font-weight:700; margin:25px 0 15px 0;">
                    <b>📍 Lokasi</b>
                </h4>
                <p style="color:#555; line-height:1.8;">
                    <?php echo nl2br($data_detail['lokasi']); ?>
                </p>

                <h4 style="color:#2d3748; font-weight:700; margin:25px 0 15px 0;">
                    <b>💰 Gaji</b>
                </h4>
                <p style="color:#555; line-height:1.8;">
                    <?php echo nl2br($data_detail['gaji']); ?>
                </p>

                <hr style="border:none; border-top:2px solid #e8ecf1; margin:25px 0;">

                <div style="margin-top:15px; display:flex; gap:10px; flex-wrap:wrap;">
                    <a href="?halaman=loker_konfirm&kode=<?php echo $data_detail['id_lowongan']; ?>"
                        class="btn btn-info btn-sm">
                        <i class="fa fa-eye"></i> Aktifkan
                    </a>

                    <a href="?halaman=loker_arsip&kode=<?php echo $data_detail['id_lowongan']; ?>"
                        class="btn btn-warning btn-sm">
                        <i class="fa fa-archive"></i> Arsip
                    </a>

                    <a href="?halaman=loker_ubah&kode=<?php echo $data_detail['id_lowongan']; ?>"
                        class="btn btn-primary btn-sm">
                        <i class="fa fa-edit"></i> Edit
                    </a>

                    <a href="?halaman=loker_aksi&kode=<?php echo $data_detail['id_lowongan']; ?>"
                        onclick="return confirm('Apakah anda yakin hapus data ini ?')" class="btn btn-danger btn-sm">
                        <i class="fa fa-trash"></i> Hapus
                    </a>
                </div>

                <!-- PROFIL PERUSAHAAN -->
                <h4 style="margin-top:35px; color:#2d3748; font-weight:700;"><b>🏢 Profil Perusahaan</b></h4>

                <?php if ($data_perusahaan) { ?>

                <div class="company-profile">
                    <h4 style="margin-top:0; color:#667eea; font-weight:700;">
                        <?php echo $data_perusahaan['nama_perusahaan']; ?>
                    </h4>
                    <p style="color:#555;"><b>Email:</b> <?php echo $data_perusahaan['email']; ?></p>
                    <p style="color:#555;"><b>Alamat:</b> <?php echo $data_perusahaan['alamat']; ?></p>
                    <p style="color:#555; line-height:1.8;"><?php echo nl2br($data_perusahaan['deskripsi']); ?></p>
                </div>

                <!-- ✅ SOSIAL MEDIA -->
                <?php if (!empty($sosmed_data)): ?>
                <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #e8ecf1;">
                    <b style="display:block; margin-bottom:10px; color:#667eea;">
                        <i class="fa fa-share-alt"></i> Sosial Media:
                    </b>
                    <div class="sosmed-container">
                        <?php foreach ($sosmed_data as $sosmed): ?>
                        <?php
                                            $platform = strtolower($sosmed['nama_platform']);
                                            $sosmed_class = 'sosmed-default';
                                            $icon = 'fa-link';

                                            if (strpos($platform, 'facebook') !== false) {
                                                $sosmed_class = 'sosmed-facebook';
                                                $icon = 'fa-facebook';
                                            } elseif (strpos($platform, 'instagram') !== false) {
                                                $sosmed_class = 'sosmed-instagram';
                                                $icon = 'fa-instagram';
                                            } elseif (strpos($platform, 'twitter') !== false || strpos($platform, 'x.com') !== false) {
                                                $sosmed_class = 'sosmed-twitter';
                                                $icon = 'fa-twitter';
                                            } elseif (strpos($platform, 'linkedin') !== false) {
                                                $sosmed_class = 'sosmed-linkedin';
                                                $icon = 'fa-linkedin';
                                            } elseif (strpos($platform, 'youtube') !== false) {
                                                $sosmed_class = 'sosmed-youtube';
                                                $icon = 'fa-youtube';
                                            } elseif (strpos($platform, 'tiktok') !== false) {
                                                $sosmed_class = 'sosmed-tiktok';
                                                $icon = 'fa-music';
                                            } elseif (strpos($platform, 'whatsapp') !== false) {
                                                $sosmed_class = 'sosmed-whatsapp';
                                                $icon = 'fa-whatsapp';
                                            } elseif (strpos($platform, 'website') !== false || strpos($platform, 'web') !== false) {
                                                $sosmed_class = 'sosmed-website';
                                                $icon = 'fa-globe';
                                            }
                                            ?>
                        <a href="<?php echo htmlspecialchars($sosmed['link']); ?>" target="_blank"
                            class="sosmed-link <?php echo $sosmed_class; ?>"
                            title="<?php echo htmlspecialchars($sosmed['nama_platform']); ?>">
                            <i class="fa <?php echo $icon; ?>"></i>
                            <span><?php echo htmlspecialchars($sosmed['nama_platform']); ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                <!-- ✅ AKHIR SOSIAL MEDIA -->

                <?php } else { ?>
                <p style="color:gray;">Profil perusahaan tidak ditemukan.</p>
                <?php } ?>

                <?php } ?>

            </div>

        </div>
    </div>
</div>

<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Tambah Lowongan Baru</h4>
            </div>
            <div class="modal-body">
                <form action="?halaman=loker_tambah" method="post" enctype="multipart/form-data">

                    <div class="form-group">
                        <label>Perusahaan</label>
                        <select name="id_perusahaan" class="form-control" required>
                            <option value="">-- Pilih Perusahaan --</option>
                            <?php
                                $q = mysqli_query($con, "SELECT * FROM tb_perusahaan");
                                while ($p = mysqli_fetch_array($q)) {
                                    ?>
                            <option value="<?= $p['id_perusahaan']; ?>">
                                <?= $p['nama_perusahaan']; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Lowongan</label>
                        <input type="text" class="form-control" name="txtjudul_lowongan" placeholder="Lowongan"
                            required />
                    </div>

                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="txtjekel" class="form-control" required>
                            <option value="">- Pilih -</option>
                            <option>Pria</option>
                            <option>Wanita</option>
                            <option>Pria / Wanita</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tentang Posisi</label>
                        <textarea class="form-control" name="txtposisi" rows="4"
                            placeholder="Deskripsi singkat tentang posisi ini..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi Pekerjaan</label>
                        <textarea class="form-control" name="txtdeskripsi" rows="5"
                            placeholder="• Tugas dan tanggung jawab&#10;• Target pekerjaan&#10;• Lingkungan kerja"
                            required></textarea>
                        <small style="color:#777;">
                            <i class="fa fa-info-circle"></i>
                            Isi setiap poin pada baris baru (tekan Enter per poin).
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Kualifikasi</label>
                        <textarea class="form-control" name="txtkualifikasi" rows="5"
                            placeholder="• Pendidikan minimal&#10;• Pengalaman kerja&#10;• Skill yang diperlukan&#10;• Sertifikasi (jika ada)"
                            required></textarea>
                            <small style="color:#777;">
                            <i class="fa fa-info-circle"></i>
                            Isi setiap poin pada baris baru (tekan Enter per poin).
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Lokasi</label>
                        <textarea class="form-control" name="txtlokasi" rows="5" placeholder="Alamat Perusahaan"
                            required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Jenis Pekerjaan</label>
                        <textarea class="form-control" name="txtjenis_pekerjaan" rows="5"
                            placeholder="Full-time, Part-time, Contract, Freelance" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Gaji</label>
                        <textarea class="form-control" name="txtgaji" rows="5"
                            placeholder="Contoh: Rp 3.000.000 - Rp 5.000.000" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" class="form-control" name="txttanggal_posting" required />
                    </div>

                    <div class="form-group">
                        <label>Batas Tanggal</label>
                        <input type="date" class="form-control" name="txtbatas_lamaran" required />
                    </div>

                    <!-- <div class="form-group">
                            <label>Sumber</label>
                            <?php echo $data_nama; ?>
                        </div> -->

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" name="btnSimpan">Simpan</button>
                    </div>

                </form>
            </div>
        </div>







        <?php
} elseif ($data_status == "perusahaan") {

    // ✅ PASTIKAN $data_id ADA
    if (!isset($data_id) || empty($data_id)) {
        // Coba ambil dari session
        if (isset($_SESSION['ses_id_perusahaan'])) {
            $data_id = $_SESSION['ses_id_perusahaan'];
        } else {
            // Ambil dari database dengan JOIN tb_user
            $username = $_SESSION['ses_username'] ?? '';
            if (!empty($username)) {
                $q = mysqli_query($con, "
                    SELECT p.id_perusahaan 
                    FROM tb_perusahaan p
                    INNER JOIN tb_user u ON p.id_user = u.id_user
                    WHERE u.username='$username'
                ");
                $r = mysqli_fetch_assoc($q);
                $data_id = $r['id_perusahaan'] ?? 0;
            }
        }
    }

    // Jika masih kosong, tampilkan error
    if (empty($data_id)) {
        echo "<div class='alert alert-danger'>⚠️ ID Perusahaan tidak ditemukan. Silakan login ulang.</div>";
    } else {
        ?>

        <style>
        /* Hilangkan scrollbar utama */
        html,
        body {
            height: 100%;
            overflow-x: auto !important;
            overflow-y: auto !important;
            margin: 0;
            padding: 0;
        }

        /* === MODERN COMPANY STYLES === */

        .loker-container {
            display: flex;
            gap: 20px;
            height: calc(110vh - 280px);
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
            border-radius: 20px;
        }

        .loker-list {
            width: 45%;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            border: none;
            overflow-y: auto;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .loker-detail {
            width: 55%;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            border: none;
            padding: 30px;
            overflow-y: auto;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .loker-card {
            border: 2px solid #e8ecf1;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 18px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            position: relative;
            overflow: auto;
        }

        .loker-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #00a65a 0%, #00d4aa 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .loker-card:hover {
            border-color: #00a65a;
            background: linear-gradient(135deg, #f0fff4 0%, #e6fff7 100%);
            transform: translateX(8px);
            box-shadow: 0 8px 24px rgba(0, 166, 90, 0.2);
        }

        .loker-card:hover::before {
            opacity: 1;
        }

        .loker-card.active {
            border: 2px solid #00a65a;
            background: linear-gradient(135deg, #e6fff7 0%, #ccffeb 100%);
            box-shadow: 0 8px 24px rgba(0, 166, 90, 0.3);
        }

        .loker-card.active::before {
            opacity: 1;
        }

        .loker-title {
            font-weight: 700;
            font-size: 16px;
            margin: 0 0 8px 0;
            color: #2d3748;
        }

        .loker-company {
            font-size: 13px;
            color: #555;
            margin: 5px 0;
        }

        .loker-date {
            font-size: 12px;
            color: #777;
            margin: 10px 0 0 0;
        }

        .badge-status {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 11px;
            color: white;
            display: inline-block;
            margin-top: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .badge-tampil {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .badge-arsip {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        }

        .badge-tangguh {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .placeholder-detail {
            text-align: center;
            margin-top: 120px;
            color: #888;
        }

        .placeholder-detail i {
            font-size: 55px;
            margin-bottom: 15px;
            color: #00a65a;
            opacity: 0.5;
        }

        .box-header.with-border {
            background: linear-gradient(135deg, #00a65a 0%, #00d4aa 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 16px 16px 0 0;
            border: none !important;
            box-shadow: 0 4px 15px rgba(0, 166, 90, 0.3);
        }

        .btn-primary {
            background: linear-gradient(135deg, #00a65a 0%, #00d4aa 100%);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 166, 90, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #00d4aa 0%, #00a65a 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 166, 90, 0.4);
        }

        @media (max-width: 991px) {
            .loker-container {
                flex-direction: column;
                height: auto;
            }

            .loker-list,
            .loker-detail {
                width: 100%;
            }
        }

        @media (max-width: 768px) {

            .box {
                margin-top: 70px !important;
            }

            .box-header.with-border {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 15px;
                padding: 20px 18px !important;
            }

            .box-title {
                width: 100%;
                font-size: 18px !important;
                line-height: 1.5;
            }

            .box-tools {
                width: 100% !important;
                margin: 0 !important;
                float: none !important;
            }

            .box-tools .btn {
                width: 100% !important;
                height: 50px;
                border-radius: 14px;
                font-size: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

        }

        /* =====================================================
   RESPONSIVE MOBILE MODERN
===================================================== */
        @media (max-width: 768px) {

            html,
            body {
                overflow-x: hidden !important;
            }

            /* BOX */
            .box {
                margin-top: 6px !important;
                border-radius: 18px !important;
                overflow: hidden;
            }

            .box-body {
                padding: 12px !important;
            }

            /* HEADER */
            .box-header.with-border {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 15px !important;
                padding: 20px 18px !important;
            }

            .box-title {
                width: 100% !important;
                font-size: 18px !important;
                line-height: 1.5 !important;
                text-align: center;
            }

            .box-tools {
                width: 100% !important;
                margin: 0 !important;
                float: none !important;
            }

            .box-tools .btn {
                width: 100% !important;
                height: 48px !important;
                border-radius: 14px !important;
                font-size: 15px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            /* CONTAINER */
            .loker-container {
                display: flex !important;
                flex-direction: column !important;
                gap: 15px !important;
                height: auto !important;
                padding: 0 !important;
                background: transparent !important;
            }

            /* LIST & DETAIL */
            .loker-list,
            .loker-detail {
                width: 100% !important;
                padding: 18px !important;
                border-radius: 18px !important;
                overflow: hidden !important;
            }

            .loker-list {
                max-height: 400px !important;
                overflow-y: auto !important;
            }

            .loker-detail {
                min-height: auto !important;
            }

            /* CARD */
            .loker-card {
                padding: 16px !important;
                border-radius: 16px !important;
                margin-bottom: 14px !important;
            }

            .loker-card:hover {
                transform: none !important;
            }

            .loker-title {
                font-size: 15px !important;
                line-height: 1.5 !important;
            }

            .loker-company,
            .loker-date {
                font-size: 12px !important;
            }

            .badge-status {
                width: 100%;
                text-align: center;
                margin-top: 14px !important;
                padding: 10px !important;
                border-radius: 12px !important;
            }

            /* DETAIL PLACEHOLDER */
            .placeholder-detail {
                margin-top: 40px !important;
                padding: 20px !important;
            }

            .placeholder-detail i {
                font-size: 42px !important;
            }

            .placeholder-detail h4 {
                font-size: 18px !important;
            }

            .placeholder-detail p {
                font-size: 13px !important;
                line-height: 1.6;
            }

            /* MODAL */
            .modal-dialog {
                width: 95% !important;
                margin: 10px auto !important;
            }

            .modal-modern .modal-content {
                border-radius: 18px !important;
            }

            .modal-modern .modal-header {
                padding: 18px !important;
            }

            .modal-modern .modal-title {
                font-size: 18px !important;
                line-height: 1.5;
            }

            .modal-modern .modal-body {
                padding: 18px !important;
                max-height: 75vh !important;
                overflow-y: auto !important;
            }

            /* FORM */
            .form-control-modern {
                min-height: 48px !important;
                font-size: 14px !important;
                border-radius: 12px !important;
            }

            textarea.form-control-modern {
                min-height: 100px !important;
            }

            .form-label-modern {
                font-size: 13px !important;
                line-height: 1.5;
            }

            /* GRID */
            .row-modern {
                grid-template-columns: 1fr !important;
                gap: 0 !important;
            }

            /* FOOTER MODAL */
            .modal-footer-modern {
                display: flex !important;
                flex-direction: column !important;
                gap: 10px !important;
                padding: 18px !important;
            }

            .btn-modern {
                width: 100% !important;
                height: 48px !important;
                justify-content: center !important;
                border-radius: 12px !important;
            }

            /* TABLE RESPONSIVE */
            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }

            table {
                width: 100% !important;
            }

            /* SCROLL */
            ::-webkit-scrollbar {
                width: 4px;
                height: 4px;
            }
        }
        </style>

        <div class="form-group">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><b>💼 Lowongan Kerja Perusahaan</b></h3>
                    <div class="box-tools pull-right">
                        <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Tambah Lowongan
                        </a>
                    </div>
                </div>

                <div class="box-body">
                    <div class="loker-container">

                        <!-- ================= LIST LOKER SEBELAH KIRI ================= -->
                        <div class="loker-list">
                            <h4 style="margin-top:0; color:#2d3748; font-weight:700;"><b>📋 Daftar Lowongan</b></h4>
                            <p style="font-size:12px; color:#777;">Klik salah satu lowongan untuk melihat detail.</p>
                            <hr style="margin-top:10px; margin-bottom:15px; border:none; border-top:2px solid #e8ecf1;">

                            <?php
                                    // ✅ QUERY DENGAN FILTER ID PERUSAHAAN
                                    $sql_tampil = "SELECT l.*, p.nama_perusahaan 
                                      FROM tb_lowongan l
                                      INNER JOIN tb_perusahaan p ON l.id_perusahaan = p.id_perusahaan
                                      WHERE p.id_perusahaan = '$data_id'
                                      ORDER BY l.tanggal_posting DESC";

                                    $query_tampil = mysqli_query($con, $sql_tampil);

                                    if (!$query_tampil) {
                                        echo "<p class='text-danger'>Error Query: " . mysqli_error($con) . "</p>";
                                    } else {
                                        $adaData = false;

                                        while ($data = mysqli_fetch_array($query_tampil, MYSQLI_BOTH)) {
                                            $adaData = true;

                                            $statusClass = "badge-tampil";
                                            if ($data['status'] == "Arsip")
                                                $statusClass = "badge-arsip";
                                            if ($data['status'] == "Tangguhkan")
                                                $statusClass = "badge-tangguh";
                                            ?>

                            <div class="loker-card" onclick="showDetail('<?php echo $data['id_lowongan']; ?>')"
                                id="card-<?php echo $data['id_lowongan']; ?>">

                                <p class="loker-title"><?php echo htmlspecialchars($data['judul_lowongan']); ?></p>
                                <p class="loker-company">
                                    <i class="fa fa-building"></i>
                                    <?php echo htmlspecialchars($data['nama_perusahaan']); ?>
                                </p>
                                <p class="loker-date">
                                    <i class="fa fa-calendar"></i>
                                    <?php echo date('d F Y', strtotime($data['tanggal_posting'])); ?>
                                </p>
                                <span class="badge-status <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($data['status']); ?>
                                </span>
                            </div>
                            <?php
                                        }

                                        if (!$adaData) {
                                            echo "<p style='color:#777; text-align:center; padding:30px;'>Belum ada lowongan yang dibuat.</p>";
                                        }
                                    }
                                    ?>
                        </div>

                        <!-- ================= DETAIL LOWONGAN SEBELAH KANAN ================= -->
                        <div class="loker-detail">
                            <h4 style="margin-top:0; color:#2d3748; font-weight:700;"><b>📄 Detail Lowongan</b></h4>
                            <hr style="border:none; border-top:2px solid #e8ecf1;">
                            <div id="detail-content">
                                <div class="placeholder-detail">
                                    <i class="fa fa-hand-pointer-o"></i>
                                    <h4><b>Pilih Lowongan</b></h4>
                                    <p>Klik salah satu lowongan di sebelah kiri untuk melihat detail.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <script>
        function showDetail(id) {
            var cards = document.getElementsByClassName("loker-card");
            for (var i = 0; i < cards.length; i++) {
                cards[i].classList.remove("active");
            }
            document.getElementById("card-" + id).classList.add("active");

            var xhr = new XMLHttpRequest();
            xhr.open("GET", "pages/loker/get_detail_loker.php?id=" + id, true);
            xhr.onload = function() {
                if (this.status == 200) {
                    document.getElementById("detail-content").innerHTML = this.responseText;
                } else {
                    document.getElementById("detail-content").innerHTML =
                        "<p style='color:red; text-align:center;'>Gagal mengambil detail lowongan.</p>";
                }
            };
            xhr.send();
        }
        </script>

        <!-- ================= MODAL TAMBAH LOKER (MODERN) ================= -->
        <style>
        /* Modern Modal Styling */
        .modal-modern .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: auto;
        }

        .modal-modern .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 25px 30px;
            position: relative;
        }

        .modal-modern .modal-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
        }

        .modal-modern .modal-title {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-modern .close {
            color: white;
            opacity: 0.8;
            font-size: 28px;
            font-weight: 300;
            text-shadow: none;
            transition: all 0.3s;
        }

        .modal-modern .close:hover {
            opacity: 1;
            transform: rotate(90deg);
        }

        .modal-modern .modal-body {
            padding: 30px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .form-section-modern {
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-section-modern:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-title-modern {
            font-size: 14px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group-modern {
            margin-bottom: 20px;
        }

        .form-label-modern {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .form-label-modern .required {
            color: #e53e3e;
            margin-left: 2px;
        }

        .form-control-modern {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control-modern:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        textarea.form-control-modern {
            resize: vertical;
            min-height: 80px;
            font-family: inherit;
        }

        .form-control-readonly {
            background: #edf2f7 !important;
            color: #718096;
            cursor: not-allowed;
        }

        .input-icon-modern {
            position: relative;
        }

        .input-icon-modern i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 16px;
            z-index: 1;
        }

        .input-icon-modern .form-control-modern {
            padding-left: 45px;
        }

        .row-modern {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .help-text-modern {
            font-size: 12px;
            color: #718096;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .help-text-modern i {
            font-size: 10px;
        }

        .modal-footer-modern {
            padding: 20px 30px;
            border-top: 2px solid #f0f0f0;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            background: #f8f9fa;
        }

        .btn-modern {
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .btn-secondary-modern {
            background: #e2e8f0;
            color: #4a5568;
        }

        .btn-secondary-modern:hover {
            background: #cbd5e0;
            transform: translateY(-2px);
        }

        /* Scrollbar Styling */
        .modal-modern .modal-body::-webkit-scrollbar {
            width: 8px;
        }

        .modal-modern .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .modal-modern .modal-body::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 10px;
        }

        .modal-modern .modal-body::-webkit-scrollbar-thumb:hover {
            background: #764ba2;
        }

        /* Animation */
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-modern .modal-dialog {
            animation: modalSlideIn 0.3s ease;
        }

        @media (max-width: 768px) {
            .modal-modern .modal-body {
                padding: 20px;
                max-height: 80vh;
            }

            .row-modern {
                grid-template-columns: 1fr;
            }

            .modal-footer-modern {
                flex-direction: column;
            }

            .btn-modern {
                width: 100%;
                justify-content: center;
            }
        }
        </style>

        <div id="myModal" class="modal fade modal-modern" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <!-- Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">
                            <i class="fa fa-plus-circle"></i>
                            Tambah Lowongan Baru
                        </h4>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <form action="?halaman=loker_tambah_per" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id_perusahaan" value="<?php echo $data_id; ?>">

                            <!-- Section: Informasi Dasar -->
                            <div class="form-section-modern">
                                <div class="section-title-modern">
                                    <i class="fa fa-building"></i>
                                    Informasi Dasar
                                </div>

                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fa fa-building me-2"></i>
                                        Nama Perusahaan
                                    </label>
                                    <div class="input-icon-modern">
                                        <input type="text" class="form-control-modern form-control-readonly"
                                            name="txtnama_perusahaan"
                                            value="<?php echo htmlspecialchars($data_nama); ?>" readonly>
                                        <i class="fa fa-building"></i>
                                    </div>
                                </div>

                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        Judul Lowongan <span class="required">*</span>
                                    </label>
                                    <div class="input-icon-modern">
                                        <input type="text" class="form-control-modern" name="txtjudul_lowongan"
                                            placeholder="Contoh: Staff Administrasi" required>
                                        <i class="fa fa-briefcase"></i>
                                    </div>
                                </div>

                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        Jenis Kelamin <span class="required">*</span>
                                    </label>
                                    <select name="txtjekel" class="form-control-modern" required>
                                        <option value="">- Pilih Jenis Kelamin -</option>
                                        <option value="Pria">Pria</option>
                                        <option value="Wanita">Wanita</option>
                                        <option value="Pria / Wanita">Pria / Wanita</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Section: Detail Pekerjaan -->
                            <div class="form-section-modern">
                                <div class="section-title-modern">
                                    <i class="fa fa-info-circle"></i>
                                    Detail Pekerjaan
                                </div>

                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        Tentang Posisi <span class="required">*</span>
                                    </label>
                                    <textarea class="form-control-modern" name="txtposisi" rows="3"
                                        placeholder="Deskripsi singkat tentang posisi ini..." required></textarea>
                                    <div class="help-text-modern">
                                        <i class="fa fa-info-circle"></i>
                                        Jelaskan gambaran umum posisi yang ditawarkan
                                    </div>
                                </div>

                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        Deskripsi Pekerjaan <span class="required">*</span>
                                    </label>
                                    <textarea class="form-control-modern" name="txtdeskripsi" rows="5"
                                        placeholder="• Tugas dan tanggung jawab&#10;• Target pekerjaan&#10;• Lingkungan kerja"
                                        required></textarea>
                                    <small style="color:#777;">
                            <i class="fa fa-info-circle"></i>
                            Isi setiap poin pada baris baru (tekan Enter per poin).
                        </small>
                                </div>

                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        Kualifikasi <span class="required">*</span>
                                    </label>
                                    <textarea class="form-control-modern" name="txtkualifikasi" rows="5"
                                        placeholder="• Pendidikan minimal&#10;• Pengalaman kerja&#10;• Skill yang diperlukan&#10;• Sertifikasi (jika ada)"
                                        required></textarea>
                                        <small style="color:#777;">
                            <i class="fa fa-info-circle"></i>
                            Isi setiap poin pada baris baru (tekan Enter per poin).
                        </small>
                                </div>
                            </div>

                            <!-- Section: Informasi Tambahan -->
                            <div class="form-section-modern">
                                <div class="section-title-modern">
                                    <i class="fa fa-map-marker-alt"></i>
                                    Informasi Tambahan
                                </div>

                                <div class="row-modern">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            <i class="fa fa-map-pin me-2"></i>Lokasi
                                        </label>
                                        <textarea class="form-control-modern" name="txtlokasi" rows="3"
                                            placeholder="Kota, Provinsi" required></textarea>
                                    </div>

                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            <i class="fa fa-briefcase me-2"></i>Jenis Pekerjaan
                                        </label>
                                        <textarea class="form-control-modern" name="txtjenis_pekerjaan" rows="3"
                                            placeholder="Full-time, Part-time, Contract, Freelance" required></textarea>
                                    </div>
                                </div>

                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fa fa-money-bill-wave me-2"></i>Gaji
                                    </label>
                                    <div class="input-icon-modern">
                                        <input type="text" class="form-control-modern" name="txtgaji"
                                            placeholder="Contoh: Rp 3.000.000 - Rp 5.000.000" required>
                                        <i class="fa fa-money-bill-wave"></i>
                                    </div>
                                    <div class="help-text-modern">
                                        <i class="fa fa-info-circle"></i>
                                        Masukkan range gaji untuk menarik lebih banyak kandidat
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Jadwal -->
                            <div class="form-section-modern">
                                <div class="section-title-modern">
                                    <i class="fa fa-calendar-alt"></i>
                                    Jadwal Posting
                                </div>

                                <div class="row-modern">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            Tanggal Posting <span class="required">*</span>
                                        </label>
                                        <input type="date" class="form-control-modern" name="txttanggal_posting"
                                            value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>

                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            Batas Lamaran <span class="required">*</span>
                                        </label>
                                        <input type="date" class="form-control-modern" name="txtbatas_lamaran" required>
                                    </div>
                                </div>
                                <div class="help-text-modern">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    Pastikan batas lamaran lebih besar dari tanggal posting
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="modal-footer-modern">
                                <button type="button" class="btn-modern btn-secondary-modern" data-dismiss="modal">
                                    <i class="fa fa-times"></i> Batal
                                </button>
                                <button type="submit" class="btn-modern btn-primary-modern" name="btnSimpan">
                                    <i class="fa fa-save"></i> Simpan Lowongan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <?php
    } // End if empty $data_id
} // End elseif perusahaan
?>