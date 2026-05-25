<?php
session_start();

// hapus semua session
$_SESSION = [];

// hancurkan session
session_destroy();

// 🔥 HAPUS COOKIE SESSION (INI YANG SERING KAMU LEWATKAN)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// redirect ke login
header("Location: ../index.php");
exit;
?>