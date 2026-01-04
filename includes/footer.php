<?php
// footer.php - reusable footer snippet
// Usage: include __DIR__ . '/includes/footer.php';
?>
<footer class="main-footer bg-dark text-light py-5">
  <div class="container">
    <div class="row">
      <div class="col-md-4">
        <h5>Harmony Haven</h5>
        <p>Providing compassionate elderly care services since 1995.</p>
      </div>
      <div class="col-md-3">
        <h5>Quick Links</h5>
        <ul class="list-unstyled">
          <li><a href="/pages/about.html" class="text-light">About Us</a></li>
          <li><a href="/pages/services.html" class="text-light">Services</a></li>
          <li><a href="/pages/contact.html" class="text-light">Contact</a></li>
        </ul>
      </div>
      <div class="col-md-5 text-end">
        <p>&copy; <?= date('Y') ?> Harmony Haven. All rights reserved.</p>
      </div>
    </div>
  </div>
</footer>