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

          <div id="don-alert" class="alert alert-danger d-none"></div>

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
                  <th>Action</th>
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
                      <td>
                        <?php if (isset($d->nom_type) && (string) $d->nom_type !== 'Argent') { ?>
                          <div class="d-flex gap-2 align-items-center">
                            <input type="number" step="0.01" min="0.01" class="form-control form-control-sm don-vendre-qte" style="max-width:120px;" placeholder="Qte" data-id-don="<?= $d->id_don ?>">
                            <input type="number" step="0.01" min="0" max="99.99" class="form-control form-control-sm don-vendre-red" style="max-width:110px;" value="10" data-id-don="<?= $d->id_don ?>">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-vendre" data-id-don="<?= $d->id_don ?>">Vendre</button>
                          </div>
                        <?php } else { ?>
                          -
                        <?php } ?>
                      </td>
                    </tr>
                  <?php } ?>
                <?php } else { ?>
                  <tr>
                    <td colspan="8" class="text-center">Aucun don enregistré.</td>
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

<?php $cspNonceLocal = (string) \Flight::app()->get('csp_nonce'); ?>
<script nonce="<?= htmlspecialchars($cspNonceLocal) ?>">
(() => {
  const base = (<?= json_encode(isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>);

  const alertBox = document.getElementById('don-alert');

  const showError = (msg) => {
    alertBox.textContent = msg;
    alertBox.classList.remove('d-none');
  };
  const clearError = () => {
    alertBox.textContent = '';
    alertBox.classList.add('d-none');
  };

  const getInputValue = (selector, idDon) => {
    const el = document.querySelector(`${selector}[data-id-don="${idDon}"]`);
    return el ? el.value : '';
  };

  document.querySelectorAll('.btn-vendre').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      clearError();

      const idDon = btn.getAttribute('data-id-don');
      const qte = getInputValue('.don-vendre-qte', idDon);
      const red = getInputValue('.don-vendre-red', idDon);

      if (!confirm('Confirmer la vente de ce don matériel ?')) {
        return;
      }

      try {
        const body = new URLSearchParams();
        body.set('id_don', idDon);
        body.set('quantite', qte);
        body.set('reduction_percent', red);

        const res = await fetch(`${base}/dons/vendre`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: body.toString(),
        });
        const data = await res.json();
        if (!res.ok || !data || data.success !== true) {
          showError((data && data.message) ? data.message : 'Erreur lors de la vente.');
          return;
        }

        window.location.reload();
      } catch (err) {
        showError('Erreur réseau / serveur.');
      }
    });
  });
})();
</script>
