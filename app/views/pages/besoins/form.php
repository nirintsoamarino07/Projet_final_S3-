<div class="pagetitle">
  <h1><?= isset($pageTitle) ? $pageTitle : 'Saisir un besoin' ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= isset($homeUrl) ? $homeUrl : '/' ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/besoins">Besoins</a></li>
      <li class="breadcrumb-item active">Saisir</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Saisie d'un besoin</h5>

          <?php if (!empty($errors)) { ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach ($errors as $e) { ?>
                  <li><?= $e ?></li>
                <?php } ?>
              </ul>
            </div>
          <?php } ?>

          <form method="post" action="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/besoins/saisir">
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label">Ville</label>
              <div class="col-sm-10">
                <div class="input-group">
                  <select id="id_ville" name="id_ville" class="form-select" required>
                    <option value="">-- Choisir --</option>
                    <?php foreach ($villes as $v) { ?>
                      <option value="<?= $v->id_ville ?>" <?php if (!empty($old['id_ville']) && (int)$old['id_ville'] === (int)$v->id_ville) { ?>selected<?php } ?>>
                        <?= $v->nom_ville ?>
                      </option>
                    <?php } ?>
                  </select>
                  <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalVille" aria-label="Ajouter une ville">
                    <i class="bi bi-plus-lg"></i>
                  </button>
                </div>
              </div>
            </div>

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
              <label class="col-sm-2 col-form-label">Quantité</label>
              <div class="col-sm-10">
                <input type="number" step="0.01" min="0.01" class="form-control" name="quantite_demandee" value="<?= isset($old['quantite_demandee']) ? htmlspecialchars((string)$old['quantite_demandee']) : '' ?>" required>
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
              <a class="btn btn-secondary" href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/besoins">Annuler</a>
            </div>
          </form>

          <div class="modal fade" id="modalVille" tabindex="-1" aria-labelledby="modalVilleLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modalVilleLabel">Ajouter une ville</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                  <div id="modalVilleAlert" class="alert alert-danger d-none"></div>

                  <div class="mb-3">
                    <label class="form-label">Région</label>
                    <select id="modal_id_region" class="form-select" required>
                      <option value="">-- Choisir --</option>
                      <?php foreach ($regions as $r) { ?>
                        <option value="<?= $r->id_region ?>"><?= $r->nom_region ?></option>
                      <?php } ?>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Nom de la ville</label>
                    <input id="modal_nom_ville" type="text" class="form-control" placeholder="Ex: Antananarivo" required>
                  </div>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                  <button type="button" class="btn btn-primary" id="modalVilleSubmit">Ajouter</button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>

<?php $cspNonceLocal = (string) \Flight::app()->get('csp_nonce'); ?>
<script nonce="<?= htmlspecialchars($cspNonceLocal) ?>">
(() => {
  const base = (<?= json_encode(isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>);
  const url = `${base}/besoins/villes`;

  const btn = document.getElementById('modalVilleSubmit');
  const alertBox = document.getElementById('modalVilleAlert');
  const selVille = document.getElementById('id_ville');
  const inputNom = document.getElementById('modal_nom_ville');
  const selRegion = document.getElementById('modal_id_region');

  if (!btn || !alertBox || !selVille || !inputNom || !selRegion) return;

  const showError = (msg) => {
    alertBox.textContent = msg;
    alertBox.classList.remove('d-none');
  };
  const clearError = () => {
    alertBox.textContent = '';
    alertBox.classList.add('d-none');
  };

  btn.addEventListener('click', async () => {
    clearError();
    btn.disabled = true;

    try {
      const nomVille = (inputNom.value || '').trim();
      const idRegion = (selRegion.value || '').trim();

      if (!nomVille) {
        showError('Veuillez saisir le nom de la ville.');
        return;
      }
      if (!idRegion) {
        showError('Veuillez choisir la région.');
        return;
      }

      const params = new URLSearchParams();
      params.append('nom_ville', nomVille);
      params.append('id_region', idRegion);

      const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString(),
      });

      const data = await res.json();
      if (!res.ok || !data || data.success !== true) {
        showError((data && data.message) ? data.message : 'Erreur lors de la création de la ville.');
        return;
      }

      const v = data.ville;
      const opt = document.createElement('option');
      opt.value = v.id_ville;
      opt.textContent = v.nom_ville;
      opt.selected = true;
      selVille.appendChild(opt);

      inputNom.value = '';
      selRegion.value = '';

      const modalEl = document.getElementById('modalVille');
      const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
      modal.hide();
    } catch (e) {
      showError('Erreur réseau / serveur.');
    } finally {
      btn.disabled = false;
    }
  });
})();
</script>
