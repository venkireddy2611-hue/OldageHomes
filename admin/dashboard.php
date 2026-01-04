<?php
session_start();
require_once __DIR__ . '/../backend/config.php';
if (empty($_SESSION['user'])) { header('Location: index.php'); exit; }
function escape($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function read_csv($path) { if (!file_exists($path)) return []; $rows=[]; if (($fp=fopen($path,'r'))!==false){ while(($d=fgetcsv($fp))!==false) $rows[]=$d; fclose($fp);} return $rows; }
$contacts = read_csv(__DIR__ . '/../backend/data/contacts.csv');
$admissions = read_csv(__DIR__ . '/../backend/data/admissions.csv');
$users = [];
$usersFile = __DIR__ . '/../backend/data/users.json'; if (file_exists($usersFile)) $users=json_decode(file_get_contents($usersFile), true)?:[];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Dashboard - Harmony Haven</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Admin</a>
    <div class="d-flex">
      <a class="btn btn-outline-light me-2" href="manage-residents.php">Residents</a>
      <a class="btn btn-outline-light me-2" href="manage-staff.php">Staff</a>
      <a class="btn btn-outline-light me-2" href="view-contacts.php">Contacts</a>
      <a class="btn btn-outline-light me-2" href="view-admissions.php">Admissions</a>
      <a class="btn btn-outline-light" href="settings.php">Settings</a>
      <a class="btn btn-outline-light ms-3" href="../backend/logout.php">Logout</a>
    </div>
  </div>
</nav>
<div class="container py-4">
  <h1>Dashboard</h1>
  <div class="row">
    <div class="col-md-6">
      <div class="card mb-4"><div class="card-body">
        <h5 class="card-title">Recent Contacts</h5>
        <ul class="list-group list-group-flush">
          <?php foreach (array_slice(array_reverse($contacts),0,5) as $c): ?>
            <li class="list-group-item"><strong><?= escape($c[1] ?? '') ?></strong><br><?= escape($c[4] ?? '') ?></li>
          <?php endforeach; ?>
        </ul>
      </div></div>
    </div>
    <div class="col-md-6">
      <div class="card mb-4"><div class="card-body">
        <h5 class="card-title">Recent Admissions</h5>
        <ul class="list-group list-group-flush">
          <?php foreach (array_slice(array_reverse($admissions),0,5) as $a): ?>
            <li class="list-group-item"><strong><?= escape($a[1] ?? '') ?></strong><br><?= escape($a[4] ?? '') ?></li>
          <?php endforeach; ?>
        </ul>
      </div></div>
    </div>
  </div>
</div>
</body>
</html>