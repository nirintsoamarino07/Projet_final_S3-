<div class="pagetitle">
  <h1><?= isset($pageTitle) ? $pageTitle : 'Saisir un prix unitaire' ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= isset($homeUrl) ? $homeUrl : '/' ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/prix-unitaires">Prix unitaires</a></li>
      <li class="breadcrumb-item active"><?= !empty($isEdit) ? 'Modifier' : 'Saisir' ?></li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><?= !empty($isEdit) ? 'Modification' : 'Saisie' ?> d'un prix unitaire</h5>

          <?php if (!empty($errors)) { ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach ($errors as $e) { ?>
                  <li><?= $e ?></li>
                <?php } ?>
              </ul>
            </div>
          <?php } ?>

          <?php
            $action = (!empty($isEdit) && !empty($idPrixUnitaire))
              ? ((isset($homeUrl) ? rtrim($homeUrl, '/') : '') . '/prix-unitaires/' . (int)$idPrixUnitaire . '/edit')
              : ((isset($homeUrl) ? rtrim($homeUrl, '/') : '') . '/prix-unitaires/saisir');
          ?>

          <form method="post" action="<?= $action ?>">

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
              <label class="col-sm-2 col-form-label">Prix unitaire</label>
              <div class="col-sm-10">
                <input type="number" step="0.01" min="0" class="form-control" name="prix" value="<?= isset($old['prix']) ? htmlspecialchars((string)$old['prix']) : '' ?>" required>
              </div>
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-primary">Enregistrer</button>
              <a class="btn btn-secondary" href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/prix-unitaires">Annuler</a>
            </div>

          </form>

        </div>
      </div>
    </div>
  </div>
</section>
