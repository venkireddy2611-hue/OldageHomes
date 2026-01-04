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

$email = trim($data['email'] ?? '');
$name = trim($data['name'] ?? '');
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) json_err('Valid email required');

// Deduplicate & store in CSV (if not exist)
$dataDir = __DIR__ . '/../backend/data'; if (!is_dir($dataDir)) @mkdir($dataDir,0755,true);
$file = $dataDir . '/newsletter.csv';
$existing = [];
if (file_exists($file) && ($fp=fopen($file,'r'))!==false) {
    while (($r=fgetcsv($fp))!==false) $existing[] = strtolower($r[1] ?? '');
    fclose($fp);
}
if (in_array(strtolower($email), $existing)) { echo json_encode(['success'=>true,'message'=>'Already subscribed']); exit; }

$fp = fopen($file,'a');
if ($fp) { fputcsv($fp, [date('c'), $email, $name, $ip]); fclose($fp); }

// Optional: send welcome email (uses templates if you integrate)
@send_mail($email, "Welcome to Harmony Haven newsletter", "Thank you for subscribing to our newsletter.", $admin_email);

echo json_encode(['success'=>true,'message'=>'Subscribed to newsletter']);
exit;