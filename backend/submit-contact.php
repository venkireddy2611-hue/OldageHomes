<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Invalid request']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? 'General Inquiry');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$message) {
    echo json_encode(['success'=>false,'message'=>'Please complete required fields']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success'=>false,'message'=>'Invalid email']);
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

// Attempt to persist to DB if available
require_once __DIR__ . '/db_connect.php';
$pdo = get_db();
if ($pdo) {
    try {
        $stmt = $pdo->prepare("INSERT INTO contacts (name,email,phone,subject,message,ip,user_agent,created_at) VALUES (:name,:email,:phone,:subject,:message,:ip,:ua,:created_at)");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':subject' => $subject,
            ':message' => $message,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ':ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ':created_at' => date('c')
        ]);
    } catch (Exception $e) {
        error_log('[submit-contact] DB insert failed: ' . $e->getMessage());
    }
}

$csv = __DIR__ . '/data/contacts.csv';
$fp = fopen($csv, 'a');
if ($fp) {
    fputcsv($fp, $entry);
    fclose($fp);
}

// Prepare email bodies
$fromAddr = "$site_name <$admin_email>";
$textAdmin = "New contact form submission:\n\n";
foreach ($entry as $k=>$v) $textAdmin .= ucfirst($k) . ": $v\n";
$htmlAdmin = render_template('contact-admin.html', array_merge($entry, ['site_name'=>$site_name]));
// send to admin (html if template available)
$okAdmin = send_mail($admin_email, "[Contact] $subject", $textAdmin, $fromAddr, $htmlAdmin);

// send confirmation to user
$textUser = "Thanks $name,\n\nWe received your message and will get back to you shortly.\n\nYour message:\n$message\n";
$htmlUser = render_template('contact-user.html', ['site_name'=>$site_name,'name'=>$name,'message'=>$message]);
$okUser = send_mail($email, "Thanks for contacting $site_name", $textUser, $fromAddr, $htmlUser);

echo json_encode(['success'=>true,'message'=>'Message sent successfully. We will contact you soon.']);
exit;
?>