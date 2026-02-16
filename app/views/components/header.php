<header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
    <a href="<?= isset($homeUrl) ? $homeUrl : '/' ?>" class="logo d-flex align-items-center">
      <img src="<?= isset($assetBase) ? $assetBase : '' ?>/img/logo.png" alt="">
      <span class="d-none d-lg-block">S3Final</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div>

  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">
      <li class="nav-item">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#">
          <span class="d-none d-md-block ps-2"><?= isset($headerTitle) ? $headerTitle : 'Dashboard' ?></span>
        </a>
      </li>
    </ul>
  </nav>

</header>
