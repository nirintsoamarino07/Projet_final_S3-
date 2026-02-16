<div class="pagetitle">
  <h1><?= isset($pageTitle) ? $pageTitle : 'Liste des besoins' ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= isset($homeUrl) ? $homeUrl : '/' ?>">Home</a></li>
      <li class="breadcrumb-item active">Besoins</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Besoins</h5>

          <div class="d-flex justify-content-end mb-3">
            <a class="btn btn-primary" href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/besoins/saisir">
              Saisir un besoin
            </a>
          </div>

          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Ville</th>
                  <th>Article</th>
                  <th>Quantité</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($besoins)) { ?>
                  <?php foreach ($besoins as $b) { ?>
                    <tr>
                      <td><?= $b->id_besoin ?></td>
                      <td><?= $b->nom_ville ?></td>
                      <td><?= $b->nom_article ?></td>
                      <td><?= $b->quantite_demandee ?> <?= $b->symbole ?></td>
                      <td><?= $b->date_saisie ?></td>
                    </tr>
                  <?php } ?>
                <?php } else { ?>
                  <tr>
                    <td colspan="5" class="text-center">Aucun besoin enregistré.</td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>
