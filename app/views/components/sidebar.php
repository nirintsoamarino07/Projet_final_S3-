<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link" href="<?= isset($homeUrl) ? $homeUrl : '/' ?>">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#besoins-nav" data-bs-toggle="collapse" href="#" role="button" aria-expanded="false" aria-controls="besoins-nav">
        <i class="bi bi-journal-text"></i><span>Besoins</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="besoins-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/besoins">
            <i class="bi bi-circle"></i><span>Liste</span>
          </a>
        </li>
        <li>
          <a href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/besoins/saisir">
            <i class="bi bi-circle"></i><span>Saisir</span>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#dons-nav" data-bs-toggle="collapse" href="#" role="button" aria-expanded="false" aria-controls="dons-nav">
        <i class="bi bi-gift"></i><span>Dons</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="dons-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/dons">
            <i class="bi bi-circle"></i><span>Liste</span>
          </a>
        </li>
        <li>
          <a href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/dons/saisir">
            <i class="bi bi-circle"></i><span>Saisir</span>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#prix-unitaires-nav" data-bs-toggle="collapse" href="#" role="button" aria-expanded="false" aria-controls="prix-unitaires-nav">
        <i class="bi bi-cash-coin"></i><span>Prix unitaire</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="prix-unitaires-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/prix-unitaires">
            <i class="bi bi-circle"></i><span>Liste</span>
          </a>
        </li>
        <li>
          <a href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/prix-unitaires/saisir">
            <i class="bi bi-circle"></i><span>Saisir</span>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?= (isset($homeUrl) ? rtrim($homeUrl, '/') : '') ?>/attributions">
        <i class="bi bi-arrow-left-right"></i>
        <span>Attribution</span>
      </a>
    </li>

  </ul>
</aside>
