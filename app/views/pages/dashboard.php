<div class="pagetitle">
  <h1><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= isset($homeUrl) ? $homeUrl : '/' ?>">Home</a></li>
      <li class="breadcrumb-item active"><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?></li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Bienvenue</h5>
          <?php if (!empty($message)) { ?>
          <p class="mb-0"><?=$message?></p>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>
