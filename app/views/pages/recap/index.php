<div class="pagetitle">
  <h1><?= isset($pageTitle) ? $pageTitle : 'Récapitulatif' ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= isset($homeUrl) ? $homeUrl : '/' ?>">Home</a></li>
      <li class="breadcrumb-item active">Récapitulatif</li>
    </ol>
  </nav>
</div>

<section class="section">

  <div class="card">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Récapitulatif des montants</h5>
        <button id="btnRefreshRecap" class="btn btn-primary">
          <i class="bi bi-arrow-repeat"></i>
          Actualiser
        </button>
      </div>

      <div id="recapLoading" class="text-muted mt-3">Cliquez sur "Actualiser" pour charger les données.</div>
      <div id="recapError" class="alert alert-danger d-none mt-3"></div>

      <div class="row mt-3" id="recapCards" style="display:none;">
        <div class="col-12 col-md-6 col-lg-3">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Besoins totaux (Ar)</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-clipboard-data" style="color:#0d6efd;"></i>
                </div>
                <div class="ps-3">
                  <h6 id="mBesoinTotal">0</h6>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Besoins satisfaits (Ar)</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-check2-circle" style="color:#0d6efd;"></i>
                </div>
                <div class="ps-3">
                  <h6 id="mBesoinSatisfait">0</h6>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Dons reçus (Ar)</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-gift" style="color:#0d6efd;"></i>
                </div>
                <div class="ps-3">
                  <h6 id="mDonsRecus">0</h6>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Dons dispatchés (Ar)</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-arrow-left-right" style="color:#0d6efd;"></i>
                </div>
                <div class="ps-3">
                  <h6 id="mDonsDispatche">0</h6>
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

  const btn = document.getElementById('btnRefreshRecap');
  const loading = document.getElementById('recapLoading');
  const errorBox = document.getElementById('recapError');
  const cards = document.getElementById('recapCards');

  const mBesoinTotal = document.getElementById('mBesoinTotal');
  const mBesoinSatisfait = document.getElementById('mBesoinSatisfait');
  const mDonsRecus = document.getElementById('mDonsRecus');
  const mDonsDispatche = document.getElementById('mDonsDispatche');

  const showError = (msg) => {
    errorBox.textContent = msg;
    errorBox.classList.remove('d-none');
  };
  const clearError = () => {
    errorBox.textContent = '';
    errorBox.classList.add('d-none');
  };

  const fmt = (n) => {
    const x = Number(n || 0);
    return x.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  const loadStats = async () => {
    clearError();
    loading.textContent = 'Chargement...';
    cards.style.display = 'none';

    try {
      const res = await fetch(`${base}/api/recap`);
      const data = await res.json();
      if (!res.ok || !data || data.success !== true) {
        showError((data && data.message) ? data.message : 'Erreur lors du chargement.');
        loading.textContent = '';
        return;
      }

      const s = data.stats || {};
      mBesoinTotal.textContent = fmt(s.besoins_totaux);
      mBesoinSatisfait.textContent = fmt(s.besoins_satisfaits);
      mDonsRecus.textContent = fmt(s.dons_recus);
      mDonsDispatche.textContent = fmt(s.dons_dispatche);

      loading.textContent = '';
      cards.style.display = '';
    } catch (e) {
      showError('Erreur réseau / serveur.');
      loading.textContent = '';
    }
  };

  btn.addEventListener('click', (e) => {
    e.preventDefault();
    loadStats();
  });
})();
</script>
