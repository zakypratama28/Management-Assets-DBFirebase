<?php
require_once 'includes/auth_check.php';
$activePage = 'dashboard';
$pageTitle  = 'Dashboard';
include 'includes/header.php';
?>

<!-- Welcome Banner -->
<div class="row mb-4 animate-in delay-1">
  <div class="col-12">
    <div class="card welcome-card">
      <div class="card-body py-3 px-4">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h5 class="mb-1 text-white fw-bold">Selamat Datang di AssetPro! 👋</h5>
            <p class="mb-0" style="color:rgba(255,255,255,0.8);font-size:0.9rem;">
              Sistem manajemen aset & inventaris dengan <strong>Firebase Realtime Database</strong> — 
              BaaS (Backend as a Service)
            </p>
          </div>
          <div class="d-none d-md-block">
            <img src="sneat-1.0.0/assets/img/illustrations/man-with-laptop-light.png" height="100" alt="Welcome" />
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3 animate-in delay-1">
    <div class="card stats-card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <div class="stats-label mb-1">Total Aset</div>
            <div class="stats-value" id="statTotal">0</div>
          </div>
          <div class="stats-icon" style="background: rgba(105,108,255,0.08); color: #696cff;">
            <i class="bx bx-box"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3 animate-in delay-2">
    <div class="card stats-card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <div class="stats-label mb-1">Kategori</div>
            <div class="stats-value" id="statCategories">0</div>
          </div>
          <div class="stats-icon" style="background: rgba(113,221,55,0.08); color: #71dd37;">
            <i class="bx bx-category"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3 animate-in delay-3">
    <div class="card stats-card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <div class="stats-label mb-1">Total Unit</div>
            <div class="stats-value" id="statTotalQty">0</div>
          </div>
          <div class="stats-icon" style="background: rgba(255,171,0,0.08); color: #ffab00;">
            <i class="bx bx-package"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3 animate-in delay-4">
    <div class="card stats-card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <div class="stats-label mb-1">Total Nilai Aset</div>
            <div class="stats-value" id="statTotalValue">Rp 0</div>
          </div>
          <div class="stats-icon" style="background: rgba(3,195,236,0.08); color: #03c3ec;">
            <i class="bx bx-wallet"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Assets Table -->
<div class="row">
  <div class="col-12 animate-in delay-2">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between pb-0">
        <div class="card-title mb-0">
          <h5 class="mb-0"><i class="bx bx-list-ul text-primary me-2"></i>Aset Terbaru</h5>
          <small class="text-muted">5 data aset terakhir ditambahkan</small>
        </div>
        <a href="daftar-aset.php" class="btn btn-sm btn-outline-primary">
          Lihat Semua <i class="bx bx-right-arrow-alt ms-1"></i>
        </a>
      </div>
      <div class="card-body pt-3">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="recentDataBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
