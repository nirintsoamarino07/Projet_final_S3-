<div class="pagetitle">
  <h1><?= isset($pageTitle) ? $pageTitle : 'Attribution des dons' ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= isset($homeUrl) ? $homeUrl : '/' ?>">Home</a></li>
      <li class="breadcrumb-item active">Attribution</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">

    <div class="col-lg-7">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Besoins</h5>

          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Ville</th>
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
                      $badge = 'secondary';
                      if ($reste <= 0) {
                        $statut = 'Couvert';
                        $badge = 'success';
                      } elseif ($attribue > 0) {
                        $statut = 'Partiel';
                        $badge = 'warning';
                      }
                    ?>
                    <tr class="besoin-row" data-id-besoin="<?= $b->id_besoin ?>" style="cursor:pointer;">
                      <td><?= $b->nom_ville ?></td>
                      <td><?= $b->nom_article ?></td>
                      <td><?= $b->quantite_demandee ?> <?= $b->symbole ?></td>
                      <td><?= $attribue ?> <?= $b->symbole ?></td>
                      <td><?= $reste ?> <?= $b->symbole ?></td>
                      <td><span class="badge bg-<?= $badge ?>"><?= $statut ?></span></td>
                    </tr>
                  <?php } ?>
                <?php } else { ?>
                  <tr>
                    <td colspan="6" class="text-center">Aucun besoin enregistré.</td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Attribution</h5>

          <div id="attrib-alert" class="alert alert-danger d-none"></div>
          <div id="attrib-info" class="alert alert-info">Sélectionnez un besoin à gauche.</div>

          <form id="attrib-form" class="d-none">
            <input type="hidden" name="id_besoin" id="id_besoin">

            <div class="mb-3">
              <label class="form-label">Besoin sélectionné</label>
              <div class="form-control" id="besoin-label" readonly></div>
            </div>

            <div class="mb-3">
              <label class="form-label">Don total disponible</label>
              <div class="form-control" id="don-total" readonly></div>
              <div class="form-text" id="don-details"></div>
            </div>

            <div class="mb-3">
              <label class="form-label">Quantité à attribuer</label>
              <input type="number" step="0.01" min="0.01" class="form-control" name="quantite_attribuee" id="quantite_attribuee" required>
              <div class="form-text" id="reste-details"></div>
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-primary" id="attrib-submit">Confirmer</button>
            </div>
          </form>

        </div>
      </div>
    </div>

  </div>
</section>

<?php $cspNonceLocal = (string) \Flight::app()->get('csp_nonce'); ?>
<script nonce="<?= htmlspecialchars($cspNonceLocal) ?>">
(() => {
  const base = (<?= json_encode(isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>);

  const alertBox = document.getElementById('attrib-alert');
  const infoBox = document.getElementById('attrib-info');
  const form = document.getElementById('attrib-form');
  const besoinLabel = document.getElementById('besoin-label');
  const idBesoinInput = document.getElementById('id_besoin');
  const donTotal = document.getElementById('don-total');
  const donDetails = document.getElementById('don-details');
  const qteInput = document.getElementById('quantite_attribuee');
  const resteDetails = document.getElementById('reste-details');
  const submitBtn = document.getElementById('attrib-submit');

  let currentBesoin = null;
  let totalDisponible = 0;

  const showError = (msg) => {
    alertBox.textContent = msg;
    alertBox.classList.remove('d-none');
  };
  const clearError = () => {
    alertBox.textContent = '';
    alertBox.classList.add('d-none');
  };

  const resetDonTotal = () => {
    donTotal.textContent = '';
    donDetails.textContent = '';
    totalDisponible = 0;
  };

  const updateValidation = () => {
    clearError();

    if (!currentBesoin) return;
    const qte = parseFloat(qteInput.value || '0');
    const reste = parseFloat(currentBesoin.quantite_restante || 0);
    const dispo = parseFloat(totalDisponible || 0);
    const maxAutorise = Math.min(reste, dispo);

    if (qteInput.value !== '' && (!Number.isFinite(qte) || qte <= 0)) {
      showError('Quantité invalide.');
      return;
    }

    if (qte > 0 && qte > reste) {
      showError('La quantité dépasse le reste à couvrir pour ce besoin.');
      return;
    }

    if (qte > 0 && qte > dispo) {
      showError('La quantité dépasse le don total disponible.');
      return;
    }

    if (qte > 0 && maxAutorise > 0 && qte > maxAutorise) {
      showError('La quantité dépasse le maximum attribuable.');
      return;
    }
  };

  document.querySelectorAll('.besoin-row').forEach(row => {
    row.addEventListener('click', async () => {
      clearError();
      infoBox.classList.add('d-none');
      form.classList.remove('d-none');

      document.querySelectorAll('.besoin-row').forEach(r => r.classList.remove('table-active'));
      row.classList.add('table-active');

      const idBesoin = row.getAttribute('data-id-besoin');
      idBesoinInput.value = idBesoin;
      qteInput.value = '';
      resetDonTotal();

      try {
        const res = await fetch(`${base}/attributions/besoin/${idBesoin}/dons`);
        const data = await res.json();
        if (!res.ok || !data || data.success !== true) {
          showError((data && data.message) ? data.message : 'Erreur de chargement.');
          return;
        }

        currentBesoin = data.besoin;
        totalDisponible = parseFloat(data.total_disponible || 0);

        besoinLabel.textContent = `${currentBesoin.nom_ville} - ${currentBesoin.nom_article} (demandée: ${currentBesoin.quantite_demandee}, restante: ${currentBesoin.quantite_restante})`;
        resteDetails.textContent = `Restant à couvrir: ${currentBesoin.quantite_restante} ${currentBesoin.symbole}`;

        donTotal.textContent = `${totalDisponible} ${currentBesoin.symbole}`;
        donDetails.textContent = `Maximum attribuable: ${Math.min(parseFloat(currentBesoin.quantite_restante || 0), totalDisponible)} ${currentBesoin.symbole}`;

        if (totalDisponible <= 0) {
          donDetails.textContent = 'Aucun don disponible pour cet article.';
        }

      } catch (e) {
        showError('Erreur réseau / serveur.');
      }
    });
  });

  qteInput.addEventListener('input', updateValidation);

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearError();
    updateValidation();
    if (!alertBox.classList.contains('d-none')) return;

    submitBtn.disabled = true;

    try {
      const params = new URLSearchParams();
      params.append('id_besoin', idBesoinInput.value);
      params.append('quantite_attribuee', qteInput.value);

      const res = await fetch(`${base}/attributions`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString(),
      });

      const data = await res.json();
      if (!res.ok || !data || data.success !== true) {
        showError((data && data.message) ? data.message : 'Erreur lors de l\'attribution.');
        return;
      }

      window.location.reload();
    } catch (e2) {
      showError('Erreur réseau / serveur.');
    } finally {
      submitBtn.disabled = false;
    }
  });
})();
</script>
