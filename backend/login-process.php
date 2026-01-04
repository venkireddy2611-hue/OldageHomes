<?php
session_start();
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.html');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    $_SESSION['flash_error'] = 'Please enter email and password.';
    header('Location: ../login.html');
    exit;
}

$usersFile = __DIR__ . '/data/users.json';
$users = [];
if (file_exists($usersFile)) {
    $raw = file_get_contents($usersFile);
    $users = json_decode($raw, true) ?: [];
}

$found = null;
foreach ($users as $u) {
    if (strtolower($u['email']) === strtolower($email)) {
        $found = $u; break;
    }
}

if (!$found || !password_verify($password, $found['password'])) {
    $_SESSION['flash_error'] = 'Invalid login credentials.';
    header('Location: ../login.html');
    exit;
}

// Login success
$_SESSION['user'] = ['id' => $found['id'], 'email' => $found['email'], 'name' => $found['name'] ?? ''];
header('Location: ../admin/dashboard.php');
exit;
?>