<?php
session_start(); if (empty($_SESSION['user'])) { header('Location: index.php'); exit; }
$store = __DIR__ . '/../backend/data/residents.json';
if (!file_exists($store)) file_put_contents($store, json_encode([]));
$patients = json_decode(file_get_contents($store), true) ?: [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
    $patients[] = ['id'=>uniqid('r_'),'name'=>$_POST['name'],'room'=>$_POST['room']??'','notes'=>$_POST['notes']??'','created'=>date('c')];
    file_put_contents($store, json_encode($patients, JSON_PRETTY_PRINT));
    header('Location: manage-residents.php'); exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Manage Residents</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<div class="container py-4"><a class="btn btn-secondary mb-3" href="dashboard.php">Back</a><h1>Residents</h1>
<form method="post" class="row g-3 mb-4"><div class="col-md-4"><input name="name" class="form-control" placeholder="Name" required></div><div class="col-md-2"><input name="room" class="form-control" placeholder="Room"></div><div class="col-md-4"><input name="notes" class="form-control" placeholder="Notes"></div><div class="col-md-2"><button class="btn btn-primary">Add</button></div></form>
<table class="table table-sm table-striped"><thead><tr><th>#</th><th>Name</th><th>Room</th><th>Notes</th><th>Added</th></tr></thead><tbody>
<?php foreach ($patients as $i=>$p): ?><tr><td><?= $i+1 ?></td><td><?= htmlspecialchars($p['name']) ?></td><td><?= htmlspecialchars($p['room']) ?></td><td><?= htmlspecialchars($p['notes']) ?></td><td><?= htmlspecialchars($p['created']) ?></td></tr><?php endforeach; ?></tbody></table>
</div></body></html>