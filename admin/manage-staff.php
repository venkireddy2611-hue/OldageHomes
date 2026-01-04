<?php
session_start(); if (empty($_SESSION['user'])) { header('Location: index.php'); exit; }
$store = __DIR__ . '/../backend/data/staff.json';
if (!file_exists($store)) file_put_contents($store, json_encode([]));
$staff = json_decode(file_get_contents($store), true) ?: [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
    $staff[] = ['id'=>uniqid('s_'),'name'=>$_POST['name'],'role'=>$_POST['role']??'','notes'=>$_POST['notes']??'','created'=>date('c')];
    file_put_contents($store, json_encode($staff, JSON_PRETTY_PRINT));
    header('Location: manage-staff.php'); exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Manage Staff</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<div class="container py-4"><a class="btn btn-secondary mb-3" href="dashboard.php">Back</a><h1>Staff</h1>
<form method="post" class="row g-3 mb-4"><div class="col-md-4"><input name="name" class="form-control" placeholder="Name" required></div><div class="col-md-3"><input name="role" class="form-control" placeholder="Role"></div><div class="col-md-3"><input name="notes" class="form-control" placeholder="Notes"></div><div class="col-md-2"><button class="btn btn-primary">Add</button></div></form>
<table class="table table-sm table-striped"><thead><tr><th>#</th><th>Name</th><th>Role</th><th>Notes</th><th>Added</th></tr></thead><tbody>
<?php foreach ($staff as $i=>$p): ?><tr><td><?= $i+1 ?></td><td><?= htmlspecialchars($p['name']) ?></td><td><?= htmlspecialchars($p['role']) ?></td><td><?= htmlspecialchars($p['notes']) ?></td><td><?= htmlspecialchars($p['created']) ?></td></tr><?php endforeach; ?></tbody></table>
</div></body></html>