<?php
session_start();

// Establecer la duración de la sesión a 15 minutos
$inactivityLimit = 15 * 60;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $inactivityLimit) {
    // Si ha pasado más de 15 minutos de inactividad, destruir la sesión
    session_unset();
    session_destroy();
}

$_SESSION['LAST_ACTIVITY'] = time();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function register_user($username, $password) {
    global $pdo;
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    return $stmt->execute([$username, $hashed_password]);
}
?>
