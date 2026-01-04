<?php
session_start(); if (empty($_SESSION['user'])) { header('Location: index.php'); exit; }
function esc($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
$rows = [];
// Prefer DB if available
require_once __DIR__ . '/../backend/db_connect.php';
$pdo = get_db();
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM contacts ORDER BY id DESC LIMIT 1000");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = [
                $row['created_at'] ?? $row['timestamp'] ?? '',
                $row['name'] ?? '',
                $row['email'] ?? '',
                $row['phone'] ?? '',
                $row['subject'] ?? '',
                $row['message'] ?? ''
            ];
        }
    } catch (Exception $e) {
        error_log('[admin/view-contacts] DB read failed: ' . $e->getMessage());
    }
} else {
    $csv = __DIR__ . '/../backend/data/contacts.csv';
    if (file_exists($csv) && ($fp=fopen($csv,'r'))!==false){ while(($r=fgetcsv($fp))!==false) $rows[]=$r; fclose($fp);} 
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Contacts</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<div class="container py-4"><a class="btn btn-secondary mb-3" href="dashboard.php">Back</a><h1>Contact Submissions</h1>
<table class="table table-sm table-striped"><thead><tr><th>#</th><th>Time</th><th>Name</th><th>Email</th><th>Phone</th><th>Subject</th><th>Message</th></tr></thead><tbody>
<?php foreach ($rows as $i=>$r): ?><tr><td><?= $i+1 ?></td><td><?= esc($r[0]??'')?></td><td><?= esc($r[1]??'')?></td><td><?= esc($r[2]??'')?></td><td><?= esc($r[3]??'')?></td><td><?= esc($r[4]??'')?></td><td><?= esc($r[5]??'')?></td></tr><?php endforeach; ?></tbody></table>
<a class="btn btn-primary" href="?download=1">Download CSV</a>
</div>
<?php if (isset($_GET['download'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="contacts.csv"');
    $out = fopen('php://output','w');
    fputcsv($out, ['Time','Name','Email','Phone','Subject','Message']);
    foreach ($rows as $r) fputcsv($out, $r);
    fclose($out);
    exit;
} ?>
</body></html>