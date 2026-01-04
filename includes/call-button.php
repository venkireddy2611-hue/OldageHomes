<?php
// call-button.php - floating call button
$call_number = $call_number ?? '+918519937446';
?>
<a href="tel:<?php echo htmlspecialchars($call_number); ?>" class="phone-float" aria-label="Call us">
  <i class="fas fa-phone"></i>
</a>

<style>.phone-float{position:fixed;right:20px;bottom:20px;width:56px;height:56px;background:#ff7e5f;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;z-index:9999;box-shadow:0 6px 18px rgba(0,0,0,0.2);text-decoration:none}</style>