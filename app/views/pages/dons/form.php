<div class="pagetitle">
  <h1><?= isset($pageTitle) ? $pageTitle : 'Saisir un don' ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= isset($homeUrl) ? $homeUrl : '/' ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/dons">Dons</a></li>
      <li class="breadcrumb-item active">Saisir</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Saisie d'un don</h5>

          <?php if (!empty($errors)) { ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach ($errors as $e) { ?>
                  <li><?= $e ?></li>
                <?php } ?>
              </ul>
            </div>
          <?php } ?>

          <form method="post" action="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/dons/saisir">

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">Article</label>
              <div class="col-sm-10">
                <select name="id_article" class="form-select" required>
                  <option value="">-- Choisir --</option>
                  <?php foreach ($articles as $a) { ?>
                    <option value="<?= $a->id_article ?>" <?php if (!empty($old['id_article']) && (int)$old['id_article'] === (int)$a->id_article) { ?>selected<?php } ?>>
                      <?= $a->nom_article ?> (<?= $a->symbole ?>)
                    </option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">Quantité totale</label>
              <div class="col-sm-10">
                <input type="number" step="0.01" min="0.01" class="form-control" name="quantite_totale" value="<?= isset($old['quantite_totale']) ? htmlspecialchars((string)$old['quantite_totale']) : '' ?>" required>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">Donateur</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="donateur" value="<?= isset($old['donateur']) ? htmlspecialchars((string)$old['donateur']) : '' ?>">
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">Source</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="source" value="<?= isset($old['source']) ? htmlspecialchars((string)$old['source']) : '' ?>">
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">Observations</label>
              <div class="col-sm-10">
                <textarea class="form-control" name="observations" rows="3"><?= isset($old['observations']) ? htmlspecialchars((string)$old['observations']) : '' ?></textarea>
              </div>
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-primary">Enregistrer</button>
              <a class="btn btn-secondary" href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/dons">Annuler</a>
            </div>

          </form>

        </div>
      </div>
    </div>
  </div>
</section>
