<?php
// includes/header.php
// Requires: $activePage (string), $pageTitle (string)
?>
<!DOCTYPE html>
<html
  lang="id"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="sneat-1.0.0/assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle ?? 'AssetPro') ?> — AssetPro</title>
    <meta name="description" content="Aplikasi manajemen aset dan inventaris profesional berbasis web menggunakan Firebase Realtime Database (BaaS)." />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="sneat-1.0.0/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="sneat-1.0.0/assets/vendor/fonts/boxicons.css?v=2" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="sneat-1.0.0/assets/vendor/css/core.css?v=2" class="template-customizer-core-css" />
    <link rel="stylesheet" href="sneat-1.0.0/assets/vendor/css/theme-default.css?v=2" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="sneat-1.0.0/assets/css/demo.css?v=2" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="sneat-1.0.0/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css?v=2" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css?v=2" />

    <!-- Helpers -->
    <script src="sneat-1.0.0/assets/vendor/js/helpers.js?v=2"></script>
    <script src="sneat-1.0.0/assets/js/config.js?v=2"></script>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">

        <?php include __DIR__ . '/sidebar.php'; ?>

        <!-- Layout page -->
        <div class="layout-page">

          <?php include __DIR__ . '/navbar.php'; ?>

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
