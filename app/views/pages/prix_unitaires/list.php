<div class="pagetitle">
  <h1><?= isset($pageTitle) ? $pageTitle : 'Liste des prix unitaires' ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= isset($homeUrl) ? $homeUrl : '/' ?>">Home</a></li>
      <li class="breadcrumb-item active">Prix unitaires</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Prix unitaires</h5>

          <div class="d-flex justify-content-end mb-3">
            <a class="btn btn-primary" href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/prix-unitaires/saisir">
              Saisir un prix unitaire
            </a>
          </div>

          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Article</th>
                  <th>Type</th>
                  <th>Prix</th>
                  <th>Unité</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($prixUnitaires)) { ?>
                  <?php foreach ($prixUnitaires as $p) { ?>
                    <tr>
                      <td><?= $p->id_prix_unitaire ?></td>
                      <td><?= $p->nom_article ?></td>
                      <td><?= $p->nom_type ?></td>
                      <td><?= $p->prix ?></td>
                      <td><?= $p->symbole ?></td>
                      <td><?= $p->created_at ?></td>
                      <td>
                        <a class="btn btn-sm btn-outline-primary" href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/prix-unitaires/<?= $p->id_prix_unitaire ?>/edit">Modifier</a>
                        <form method="post" action="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/prix-unitaires/<?= $p->id_prix_unitaire ?>/delete" style="display:inline-block;">
                          <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                        </form>
                      </td>
                    </tr>
                  <?php } ?>
                <?php } else { ?>
                  <tr>
                    <td colspan="7" class="text-center">Aucun prix unitaire enregistré.</td>
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
