<?php
require_once 'includes/auth_check.php';
$activePage = 'daftar-aset';
$pageTitle  = 'Daftar Aset';
include 'includes/header.php';
?>

<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Manajemen Aset /</span> Daftar Aset
</h4>

<div class="card animate-in">
  <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
    <div>
      <h5 class="card-title mb-0"><i class="bx bx-box text-primary me-2"></i>Daftar Seluruh Aset</h5>
    </div>
    <div class="d-flex align-items-center gap-2">
      <div class="search-wrapper">
        <i class="bx bx-search search-icon"></i>
        <input type="text" id="searchInput" class="form-control" placeholder="Cari aset..." style="min-width:250px;" />
      </div>
      <a href="tambah-aset.php" class="btn btn-primary">
        <i class="bx bx-plus me-1"></i> Tambah
      </a>
    </div>
  </div>

  <!-- Filter Badges -->
  <div class="card-body pb-0">
    <div class="d-flex flex-wrap align-items-center gap-2" id="filterContainer">
      <span class="filter-badge active" data-filter="all">
        <i class="bx bx-grid-alt"></i> Semua
      </span>
    </div>
  </div>

  <div class="card-body">
    <div class="table-responsive text-nowrap">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th style="width:50px">No</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Jumlah</th>
            <th>Harga</th>
            <th>Keterangan</th>
            <th style="width:100px">Aksi</th>
          </tr>
        </thead>
        <tbody id="dataBody"></tbody>
      </table>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
