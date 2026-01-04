<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/../backend/config.php';
require_once __DIR__ . '/../backend/mail.php';

function json_err($msg, $code = 400) { http_response_code($code); echo json_encode(['success'=>false,'message'=>$msg]); exit; }

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) $data = $_POST;

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$preferred = trim($data['preferred_date'] ?? '');
$notes = trim($data['notes'] ?? '');
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

if (!$name || !$email || !$phone) json_err('Missing required fields');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) json_err('Invalid email');

// Rate limit: 3 appts per IP per 3600s
$limitfile = __DIR__ . '/../backend/data/api_rate.json';
$limitKey = 'appt|'.$ip;
$now = time();
$rates = @json_decode(@file_get_contents($limitfile), true) ?: [];
$window = 3600; $max = 3;
$times = $rates[$limitKey] ?? [];
$times = array_filter($times, function($t) use ($now,$window){ return $t > $now - $window; });
if (count($times) >= $max) json_err('Rate limit exceeded for appointments', 429);
$times[] = $now; $rates[$limitKey] = $times; file_put_contents($limitfile, json_encode($rates), LOCK_EX);

$dataDir = __DIR__ . '/../backend/data'; if (!is_dir($dataDir)) @mkdir($dataDir,0755,true);
$csv = $dataDir . '/appointments.csv';
$fp = fopen($csv,'a');
if ($fp) {
    fputcsv($fp, [date('c'), $name, $email, $phone, $preferred, $notes, $ip]); fclose($fp);
}

$body = "New appointment request:\nName: $name\nEmail: $email\nPhone: $phone\nPreferred: $preferred\nNotes: $notes\nIP: $ip\n";
@send_mail($admin_email, "[Appointment] New Request", $body, "$name <$email>");

echo json_encode(['success'=>true,'message'=>'Appointment request received. We will contact you.']);
exit;