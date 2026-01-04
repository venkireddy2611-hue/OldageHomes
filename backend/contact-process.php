<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/contact.html');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? 'General Inquiry');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$message) {
    $_SESSION['flash_error'] = 'Please complete required fields';
    header('Location: ../contact.html');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_error'] = 'Invalid email address';
    header('Location: ../contact.html');
    exit;
}

$entry = [
    'timestamp' => date('c'),
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'subject' => $subject,
    'message' => $message
];

// Append to CSV for simple storage
$csv = __DIR__ . '/data/contacts.csv';
$fp = fopen($csv, 'a');
if ($fp) {
    fputcsv($fp, $entry);
    fclose($fp);
}

// Notify admin
$body = "New contact form submission:\n\n";
foreach ($entry as $k => $v) {
    $body .= ucfirst($k) . ": $v\n";
}
@send_mail($admin_email, "[Contact] $subject", $body, "$name <$email>");

$_SESSION['flash_success'] = 'Message sent successfully. We will contact you soon.';
header('Location: ../pages/contact.html?status=success');
exit;
?>