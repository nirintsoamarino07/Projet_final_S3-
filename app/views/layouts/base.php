<?php

$base = rtrim((string)\Flight::request()->base, '/');
$assetBase = $base . '/assets';
$homeUrl = $base . '/';
$cspNonce = (string) \Flight::app()->get('csp_nonce');

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?= isset($title) ? $title : 'Dashboard - S3Final' ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <link href="<?= $assetBase ?>/img/favicon.png" rel="icon">
  <link href="<?= $assetBase ?>/img/apple-touch-icon.png" rel="apple-touch-icon">

  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <link href="<?= $assetBase ?>/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= $assetBase ?>/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= $assetBase ?>/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="<?= $assetBase ?>/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="<?= $assetBase ?>/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="<?= $assetBase ?>/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="<?= $assetBase ?>/vendor/simple-datatables/style.css" rel="stylesheet">

  <link href="<?= $assetBase ?>/css/style.css" rel="stylesheet">
</head>

<body>

  <?php include __DIR__ . '/../components/header.php'; ?>
  <?php include __DIR__ . '/../components/sidebar.php'; ?>

  <main id="main" class="main">
    <?= isset($content) ? $content : '' ?>
  </main>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script nonce="<?= htmlspecialchars($cspNonce) ?>" src="<?= $assetBase ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script nonce="<?= htmlspecialchars($cspNonce) ?>" src="<?= $assetBase ?>/js/main.js"></script>

</body>

</html>
