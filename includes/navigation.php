<?php
// navigation.php - reusable navigation menu
// Usage: include 'includes/navigation.php';
?>
<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container">
    <a class="navbar-brand" href="/">
      <img src="/images/logo.svg" alt="Logo" style="height:36px; vertical-align:middle; margin-right:8px"> ElderCare Haven
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="/index.html">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="/pages/about.html">About Us</a></li>
        <li class="nav-item"><a class="nav-link" href="/pages/services.html">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="/pages/facilities.html">Facilities</a></li>
        <li class="nav-item"><a class="nav-link" href="/pages/admission.html">Admission</a></li>
        <li class="nav-item"><a class="nav-link" href="/pages/gallery.html">Gallery</a></li>
        <li class="nav-item"><a class="nav-link" href="/pages/contact.html">Contact</a></li>
        <li class="nav-item"><a class="nav-link btn btn-outline-light ms-2" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Admin</a></li>
      </ul>
    </div>
  </div>
</nav>