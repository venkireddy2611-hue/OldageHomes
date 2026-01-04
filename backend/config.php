<?php
// Basic config for backend scripts
$admin_email = 'venkireddy2611@gmail.com';
$site_name = 'Harmony Haven';
$data_dir = __DIR__ . '/data';
if (!is_dir($data_dir)) {@mkdir($data_dir, 0755, true);} 
?>
