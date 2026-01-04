<?php
// header.php - include where you want to render the top bar + navigation
// Usage: include __DIR__ . '/includes/header.php';
?>
<div class="top-bar bg-white py-2">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="top-bar-info">
      <a href="mailto:venkireddy2611@gmail.com"><i class="fas fa-envelope"></i> venkireddy2611@gmail.com</a>
      <span class="mx-3">|</span>
      <a href="tel:+918519937446"><i class="fas fa-phone"></i> +91 85199 37446</a>
    </div>
    <div class="social-icons">
      <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
      <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
    </div>
  </div>
</div>

<header class="main-header">
  <div class="container header-content">
    <?php include_once __DIR__ . '/navigation.php'; ?>
  </div>
</header>