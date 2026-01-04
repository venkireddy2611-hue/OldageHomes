<?php
session_start();
require_once __DIR__ . '/config.php';

// simple auth guard
if (empty($_SESSION['user'])) {
    header('Location: ../login.html');
    exit;
}

function escape($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Read CSV helper
function read_csv($path) {
    if (!file_exists($path)) return [];
    $rows = [];
    if (($fp = fopen($path, 'r')) !== false) {
        while (($data = fgetcsv($fp)) !== false) {
            $rows[] = $data;
        }
        fclose($fp);
    }
    return $rows;
}

$contacts = read_csv(__DIR__ . '/data/contacts.csv');
$admissions = read_csv(__DIR__ . '/data/admissions.csv');
$users = [];
$usersFile = __DIR__ . '/data/users.json';
if (file_exists($usersFile)) $users = json_decode(file_get_contents($usersFile), true) ?: [];

?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Admin</a>
    <div class="d-flex">
      <span class="navbar-text me-3 text-white">Signed in as: <?= escape($_SESSION['user']['email']) ?></span>
      <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
    </div>
  </div>
</nav>
<div class="container py-4">
  <h1 class="mb-4">Dashboard</h1>

  <section class="mb-5">
    <h3>Contacts</h3>
    <div class="table-responsive">
      <table class="table table-sm table-striped">
        <thead><tr><th>#</th><th>Time</th><th>Name</th><th>Email</th><th>Phone</th><th>Subject</th><th>Message</th></tr></thead>
        <tbody>
          <?php foreach ($contacts as $i => $r): ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td><?= escape($r[0] ?? '') ?></td>
              <td><?= escape($r[1] ?? '') ?></td>
              <td><?= escape($r[2] ?? '') ?></td>
              <td><?= escape($r[3] ?? '') ?></td>
              <td><?= escape($r[4] ?? '') ?></td>
              <td><?= escape($r[5] ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="mb-5">
    <h3>Admissions</h3>
    <div class="table-responsive">
      <table class="table table-sm table-striped">
        <thead><tr><th>#</th><th>Time</th><th>Contact Name</th><th>Contact Phone</th><th>Contact Email</th><th>Resident Name</th><th>Timeline</th><th>Room Type</th><th>Info</th></tr></thead>
        <tbody>
          <?php foreach ($admissions as $i => $r): ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td><?= escape($r[0] ?? '') ?></td>
              <td><?= escape($r[1] ?? '') ?></td>
              <td><?= escape($r[2] ?? '') ?></td>
              <td><?= escape($r[3] ?? '') ?></td>
              <td><?= escape($r[4] ?? '') ?></td>
              <td><?= escape($r[5] ?? '') ?></td>
              <td><?= escape($r[6] ?? '') ?></td>
              <td><?= escape($r[7] ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="mb-5">
    <h3>Registered Users</h3>
    <div class="table-responsive">
      <table class="table table-sm table-striped">
        <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Created</th></tr></thead>
        <tbody>
          <?php foreach ($users as $i => $u): ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td><?= escape($u['name'] ?? '') ?></td>
              <td><?= escape($u['email'] ?? '') ?></td>
              <td><?= escape($u['created_at'] ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

</div>
</body>
</html>