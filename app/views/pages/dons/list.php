<div class="pagetitle">
  <h1><?= isset($pageTitle) ? $pageTitle : 'Liste des dons' ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= isset($homeUrl) ? $homeUrl : '/' ?>">Home</a></li>
      <li class="breadcrumb-item active">Dons</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Dons</h5>

          <div class="d-flex justify-content-end mb-3">
            <a class="btn btn-primary" href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/dons/saisir">
              Saisir un don
            </a>
          </div>

          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Article</th>
                  <th>Quantité totale</th>
                  <th>Distribuée</th>
                  <th>Donateur</th>
                  <th>Source</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($dons)) { ?>
                  <?php foreach ($dons as $d) { ?>
                    <tr>
                      <td><?= $d->id_don ?></td>
                      <td><?= $d->nom_article ?></td>
                      <td><?= $d->quantite_totale ?> <?= $d->symbole ?></td>
                      <td><?= $d->quantite_distribuee ?> <?= $d->symbole ?></td>
                      <td><?= !empty($d->donateur) ? $d->donateur : '-' ?></td>
                      <td><?= !empty($d->source) ? $d->source : '-' ?></td>
                      <td><?= $d->date_reception ?></td>
                    </tr>
                  <?php } ?>
                <?php } else { ?>
                  <tr>
                    <td colspan="7" class="text-center">Aucun don enregistré.</td>
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
