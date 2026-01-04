<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../forgot-password.html');
    exit;
}

$email = trim($_POST['email'] ?? '');
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_error'] = 'Please enter a valid email.';
    header('Location: ../forgot-password.html');
    exit;
}

$usersFile = __DIR__ . '/data/users.json';
$users = [];
if (file_exists($usersFile)) $users = json_decode(file_get_contents($usersFile), true) ?: [];
$found = null;
foreach ($users as $u) {
    if (strtolower($u['email']) === strtolower($email)) { $found = $u; break; }
}

if (!$found) {
    // Don't reveal whether email exists
    $_SESSION['flash_success'] = 'If an account exists for that email, you will receive reset instructions.';
    header('Location: ../forgot-password.html');
    exit;
}

$token = bin2hex(random_bytes(16));
$expires = time() + 3600; // 1 hour
$resetsFile = __DIR__ . '/data/password_resets.json';
$resets = [];
if (file_exists($resetsFile)) $resets = json_decode(file_get_contents($resetsFile), true) ?: [];
$resets[] = ['email' => $email, 'token' => $token, 'expires' => $expires];
file_put_contents($resetsFile, json_encode($resets, JSON_PRETTY_PRINT));

// Send email (link points to admin; you can implement reset endpoint later)
$resetLink = (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/backend/reset-password.php?token=' . $token;
$body = "You requested a password reset. Use the link below (valid 1 hour):\n\n$resetLink\n\nIf you didn't request this, ignore this email.";
@send_mail($email, "Password reset instructions", $body, $admin_email);

$_SESSION['flash_success'] = 'If an account exists for that email, you will receive reset instructions.';
header('Location: ../forgot-password.html');
exit;
?>