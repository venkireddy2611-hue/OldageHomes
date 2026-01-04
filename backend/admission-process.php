<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/admission.html');
    exit;
}

$contactName = trim($_POST['contactName'] ?? '');
$contactPhone = trim($_POST['contactPhone'] ?? '');
$contactEmail = trim($_POST['contactEmail'] ?? '');
$residentName = trim($_POST['residentName'] ?? '');
$timeline = trim($_POST['timeline'] ?? '');
$roomType = trim($_POST['roomType'] ?? '');
$info = trim($_POST['additionalInfo'] ?? '');

if (!$contactName || !$contactPhone || !$contactEmail) {
    $_SESSION['flash_error'] = 'Please complete required contact fields';
    header('Location: ../admission.html');
    exit;
}

if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_error'] = 'Invalid email address';
    header('Location: ../admission.html');
    exit;
}

$entry = [
    'timestamp' => date('c'),
    'contactName' => $contactName,
    'contactPhone' => $contactPhone,
    'contactEmail' => $contactEmail,
    'residentName' => $residentName,
    'timeline' => $timeline,
    'roomType' => $roomType,
    'additionalInfo' => $info
];

$csv = __DIR__ . '/data/admissions.csv';
$fp = fopen($csv, 'a');
if ($fp) {
    fputcsv($fp, $entry);
    fclose($fp);
}

$body = "New admission inquiry:\n\n";
foreach ($entry as $k => $v) $body .= ucfirst($k) . ": $v\n";
@send_mail($admin_email, "[Admission] New Inquiry", $body, "$contactName <$contactEmail>");

$_SESSION['flash_success'] = 'Admission inquiry submitted. We will reach out within 48 hours.';
header('Location: ../pages/admission.html?status=success');
exit;
?>