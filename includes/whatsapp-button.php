<?php
// whatsapp-button.php - small floating WhatsApp button
$wa_number = $wa_number ?? '+918519937446'; // override by setting $wa_number before including
$wa_message = isset($wa_message) ? rawurlencode($wa_message) : rawurlencode('Hello Harmony Haven, I would like to know more about your senior living community');
?>
<a href="https://wa.me/<?php echo htmlspecialchars($wa_number); ?>?text=<?php echo $wa_message; ?>" 
   class="whatsapp-float" 
   target="_blank" 
   aria-label="Chat on WhatsApp">
    <i class="fab fa-whatsapp"></i>
</a>

<style>.whatsapp-float{position:fixed;right:20px;bottom:80px;width:56px;height:56px;background:#25D366;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;z-index:9999;box-shadow:0 6px 18px rgba(0,0,0,0.2);text-decoration:none}</style>