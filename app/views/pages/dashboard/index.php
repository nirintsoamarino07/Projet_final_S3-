<div class="pagetitle">
  <h1><?= isset($pageTitle) ? $pageTitle : 'Tableau de bord' ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= isset($homeUrl) ? $homeUrl : '/' ?>">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>
</div>

<section class="section">

  <div class="row">

    <div class="col-12 col-md-6 col-lg-3">
      <div class="card info-card">
        <div class="card-body">
          <h5 class="card-title">Villes aidées</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-geo-alt"></i>
            </div>
            <div class="ps-3">
              <h6><?= $stats['villes_aidees'] ?? 0 ?></h6>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
      <div class="card info-card">
        <div class="card-body">
          <h5 class="card-title">Besoins couverts</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-check2-circle"></i>
            </div>
            <div class="ps-3">
              <h6><?= $stats['besoins_couverts'] ?? 0 ?> / <?= $stats['besoins_total'] ?? 0 ?></h6>
              <span class="text-muted small pt-2 ps-1"><?= $stats['pourcentage_couverture'] ?? 0 ?>%</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
      <div class="card info-card">
        <div class="card-body">
          <h5 class="card-title">Dons disponibles</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-box-seam"></i>
            </div>
            <div class="ps-3">
              <h6><?= $stats['dons_disponibles'] ?? 0 ?></h6>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
      <div class="card info-card">
        <div class="card-body">
          <h5 class="card-title">Besoins à couvrir</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="ps-3">
              <h6><?= $stats['besoins_a_couvrir'] ?? 0 ?></h6>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Filtres</h5>

      <form method="get" class="row g-3">

        <div class="col-12 col-md-3">
          <label class="form-label">Région</label>
          <select class="form-select" name="id_region">
            <option value="">Toutes</option>
            <?php foreach ($regions as $r) { ?>
              <option value="<?= $r->id_region ?>" <?php if (!empty($filters['id_region']) && (int)$filters['id_region'] === (int)$r->id_region) { ?>selected<?php } ?>>
                <?= $r->nom_region ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label">Ville</label>
          <select class="form-select" name="id_ville">
            <option value="">Toutes</option>
            <?php foreach ($villes as $v) { ?>
              <option value="<?= $v->id_ville ?>" <?php if (!empty($filters['id_ville']) && (int)$filters['id_ville'] === (int)$v->id_ville) { ?>selected<?php } ?>>
                <?= $v->nom_ville ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label">Type</label>
          <select class="form-select" name="id_type">
            <option value="">Tous</option>
            <?php foreach ($types as $t) { ?>
              <option value="<?= $t->id_type ?>" <?php if (!empty($filters['id_type']) && (int)$filters['id_type'] === (int)$t->id_type) { ?>selected<?php } ?>>
                <?= $t->nom_type ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label">Événement</label>
          <select class="form-select" name="id_evenement">
            <option value="">Tous</option>
            <?php foreach ($evenements as $e) { ?>
              <option value="<?= $e->id_evenement ?>" <?php if (!empty($filters['id_evenement']) && (int)$filters['id_evenement'] === (int)$e->id_evenement) { ?>selected<?php } ?>>
                <?= $e->nom_evenement ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="col-12 text-end">
          <button class="btn btn-primary" type="submit">Filtrer</button>
          <a class="btn btn-secondary" href="<?= isset($homeUrl) ? $homeUrl : '/' ?>">Réinitialiser</a>
        </div>

      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Besoins non couverts / partiels</h5>

      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Région</th>
              <th>Ville</th>
              <th>Type</th>
              <th>Article</th>
              <th>Demandée</th>
              <th>Attribuée</th>
              <th>Restante</th>
              <th>Statut</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($besoins)) { ?>
              <?php foreach ($besoins as $b) { ?>
                <?php
                  $reste = (float) $b->quantite_restante;
                  $attribue = (float) $b->quantite_attribuee;
                  $statut = 'Non couvert';
                  $badge = 'danger';
                  if ($reste <= 0) {
                    $statut = 'Couvert';
                    $badge = 'success';
                  } elseif ($attribue > 0) {
                    $statut = 'Partiel';
                    $badge = 'warning';
                  }
                ?>
                <tr class="dash-row" data-id-besoin="<?= $b->id_besoin ?>" style="cursor:pointer;">
                  <td><?= $b->nom_region ?></td>
                  <td><?= $b->nom_ville ?></td>
                  <td><?= $b->nom_type ?></td>
                  <td><?= $b->nom_article ?></td>
                  <td><?= $b->quantite_demandee ?> <?= $b->symbole ?></td>
                  <td><?= $attribue ?> <?= $b->symbole ?></td>
                  <td><?= $reste ?> <?= $b->symbole ?></td>
                  <td><span class="badge bg-<?= $badge ?>"><?= $statut ?></span></td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="8" class="text-center">Aucun besoin à afficher.</td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

</section>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasDetails" aria-labelledby="offcanvasDetailsLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasDetailsLabel">Détails</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div id="details-loading" class="text-muted">Sélectionnez une ligne.</div>
    <div id="details-error" class="alert alert-danger d-none"></div>

    <h6>Historique des attributions</h6>
    <div class="table-responsive">
      <table class="table table-sm">
        <thead>
          <tr>
            <th>Date</th>
            <th>Don</th>
            <th>Quantité</th>
          </tr>
        </thead>
        <tbody id="details-body">
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php $cspNonceLocal = (string) \Flight::app()->get('csp_nonce'); ?>
<script nonce="<?= htmlspecialchars($cspNonceLocal) ?>">
(() => {
  const base = (<?= json_encode(isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>);

  const loading = document.getElementById('details-loading');
  const errorBox = document.getElementById('details-error');
  const tbody = document.getElementById('details-body');

  const showError = (msg) => {
    errorBox.textContent = msg;
    errorBox.classList.remove('d-none');
  };
  const clearError = () => {
    errorBox.textContent = '';
    errorBox.classList.add('d-none');
  };

  const offcanvasEl = document.getElementById('offcanvasDetails');
  const offcanvas = new bootstrap.Offcanvas(offcanvasEl);

  document.querySelectorAll('.dash-row').forEach(row => {
    row.addEventListener('click', async () => {
      const id = row.getAttribute('data-id-besoin');
      clearError();
      loading.textContent = 'Chargement...';
      tbody.innerHTML = '';
      offcanvas.show();

      try {
        const res = await fetch(`${base}/dashboard/besoin/${id}/attributions`);
        const data = await res.json();
        if (!res.ok || !data || data.success !== true) {
          showError((data && data.message) ? data.message : 'Erreur lors du chargement.');
          loading.textContent = '';
          return;
        }

        const items = data.items || [];
        if (items.length === 0) {
          loading.textContent = 'Aucune attribution pour ce besoin.';
          return;
        }

        loading.textContent = '';
        items.forEach(it => {
          const tr = document.createElement('tr');
          const donLabel = `#${it.id_don}${it.donateur ? ' - ' + it.donateur : ''}`;
          tr.innerHTML = `<td>${it.date_attribution}</td><td>${donLabel}</td><td>${it.quantite_attribuee}</td>`;
          tbody.appendChild(tr);
        });
      } catch (e) {
        showError('Erreur réseau / serveur.');
        loading.textContent = '';
      }
    });
  });
})();
</script>
