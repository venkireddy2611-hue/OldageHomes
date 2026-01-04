<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Invalid request']);
    exit;
}

// Collect & sanitize important fields (keep flexible for many optional fields)
$contactName = trim($_POST['contactName'] ?? '');
$contactPhone = trim($_POST['contactPhone'] ?? '');
$contactEmail = trim($_POST['contactEmail'] ?? '');
$residentName = trim($_POST['residentName'] ?? '');
$timeline = trim($_POST['timeline'] ?? '');
$roomType = trim($_POST['roomType'] ?? '');
$info = trim($_POST['additionalInfo'] ?? '');

if (!$contactName || !$contactPhone || !$contactEmail) {
    echo json_encode(['success'=>false,'message'=>'Please complete required contact fields']);
    exit;
}
if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success'=>false,'message'=>'Invalid email address']);
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

// Attempt to persist to DB if available
require_once __DIR__ . '/db_connect.php';
$pdo = get_db();
if ($pdo) {
    try {
        $stmt = $pdo->prepare("INSERT INTO admissions (contact_name,contact_phone,contact_email,resident_name,timeline,room_type,additional_info,ip,user_agent,created_at) VALUES (:contact_name,:contact_phone,:contact_email,:resident_name,:timeline,:room_type,:additional_info,:ip,:ua,:created_at)");
        $stmt->execute([
            ':contact_name' => $contactName,
            ':contact_phone' => $contactPhone,
            ':contact_email' => $contactEmail,
            ':resident_name' => $residentName,
            ':timeline' => $timeline,
            ':room_type' => $roomType,
            ':additional_info' => $info,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ':ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ':created_at' => date('c')
        ]);
    } catch (Exception $e) {
        error_log('[submit-admission] DB insert failed: ' . $e->getMessage());
    }
}

$csv = __DIR__ . '/data/admissions.csv';
$fp = fopen($csv, 'a');
if ($fp) {
    fputcsv($fp, $entry);
    fclose($fp);
}

// Prepare emails
$fromAddr = "$site_name <$admin_email>";
$textAdmin = "New admission inquiry:\n\n";
foreach ($entry as $k=>$v) $textAdmin .= ucfirst($k) . ": $v\n";
$htmlAdmin = render_template('admission-admin.html', array_merge($entry, ['site_name'=>$site_name]));
$okAdmin = send_mail($admin_email, "[Admission] New Inquiry", $textAdmin, $fromAddr, $htmlAdmin);

// send confirmation to user
$textUser = "Thanks $contactName,\n\nWe received your admission inquiry and will reach out within 48 hours.\n\nSummary:\nResident: $residentName\nTimeline: $timeline\nPreferred Room: $roomType\n";
$htmlUser = render_template('admission-user.html', array_merge(['site_name'=>$site_name], $entry));
$okUser = send_mail($contactEmail, "Admission inquiry received - $site_name", $textUser, $fromAddr, $htmlUser);

echo json_encode(['success'=>true,'message'=>'Admission inquiry submitted. We will reach out within 48 hours.']);
exit;
?>