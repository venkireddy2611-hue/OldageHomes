<?php
session_start(); if (empty($_SESSION['user'])) { header('Location: index.php'); exit; }
$configFile = __DIR__ . '/../data/config.json';
$config = json_decode(file_get_contents($configFile), true) ?: [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $config['site']['admin_email'] = $_POST['admin_email'] ?? $config['site']['admin_email'] ?? '';
    file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    $saved = true;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Settings</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<div class="container py-4"><a class="btn btn-secondary mb-3" href="dashboard.php">Back</a><h1>Settings</h1>
<?php if (!empty($saved)): ?><div class="alert alert-success">Settings saved.</div><?php endif; ?>
<form method="post" class="mb-4">
  <div class="mb-3"><label class="form-label">Admin Email</label><input class="form-control" type="email" name="admin_email" value="<?= htmlspecialchars($config['site']['admin_email'] ?? '') ?>"></div>
  <button class="btn btn-primary">Save</button>
</form>
</div></body></html>