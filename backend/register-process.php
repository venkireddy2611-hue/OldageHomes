<?php
session_start();
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.html');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$name || !$email || !$password) {
    $_SESSION['flash_error'] = 'Please fill all required fields.';
    header('Location: ../register.html');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_error'] = 'Invalid email address';
    header('Location: ../register.html');
    exit;
}

$usersFile = __DIR__ . '/data/users.json';
$users = [];
if (file_exists($usersFile)) {
    $raw = file_get_contents($usersFile);
    $users = json_decode($raw, true) ?: [];
}

foreach ($users as $u) {
    if (strtolower($u['email']) === strtolower($email)) {
        $_SESSION['flash_error'] = 'An account with that email already exists.';
        header('Location: ../register.html');
        exit;
    }
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$user = [
    'id' => uniqid('u_', true),
    'name' => $name,
    'email' => $email,
    'password' => $hash,
    'created_at' => date('c')
];
$users[] = $user;
file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

// Auto-login after registration
$_SESSION['user'] = ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']];
header('Location: ../admin/dashboard.php');
exit;
?>