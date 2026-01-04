<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/../backend/config.php';
require_once __DIR__ . '/../backend/mail.php';

function json_err($msg, $code = 400) { http_response_code($code); echo json_encode(['success'=>false,'message'=>$msg]); exit; }

// Read JSON body or form data
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) $data = $_POST;

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$subject = trim($data['subject'] ?? 'Inquiry');
$message = trim($data['message'] ?? '');
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

if (!$name || !$email || !$message) json_err('Missing required fields: name, email and message required');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) json_err('Invalid email');

// Basic rate limiter: max 5 requests per IP per 60s
$limitfile = __DIR__ . '/../backend/data/api_rate.json';
$limitKey = 'contact|'.$ip;
$now = time();
$rates = @json_decode(@file_get_contents($limitfile), true) ?: [];
$window = 60; $max = 5;
$times = $rates[$limitKey] ?? [];
$times = array_filter($times, function($t) use ($now,$window){ return $t > $now - $window; });
if (count($times) >= $max) json_err('Rate limit exceeded, try again later', 429);
$times[] = $now; $rates[$limitKey] = $times; file_put_contents($limitfile, json_encode($rates), LOCK_EX);

// Persist to CSV
$dataDir = __DIR__ . '/../backend/data'; if (!is_dir($dataDir)) @mkdir($dataDir,0755,true);
$csv = $dataDir . '/contacts.csv';
$fp = fopen($csv,'a');
if ($fp) {
    fputcsv($fp, [date('c'), $name, $email, $phone, $subject, $message, $ip, $ua]);
    fclose($fp);
}

// Notify admin
$body = "Contact form (API) submission:\n\n";
$body .= "Name: $name\nEmail: $email\nPhone: $phone\nSubject: $subject\nMessage: $message\nIP: $ip\nUser-Agent: $ua\n";
@send_mail($admin_email, "[API Contact] $subject", $body, "$name <$email>");

echo json_encode(['success'=>true,'message'=>'Message received. Thank you!']);
exit;