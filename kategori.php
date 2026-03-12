<?php
require_once 'includes/auth_check.php';
$activePage = 'kategori';
$pageTitle  = 'Kategori';
include 'includes/header.php';
?>

<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Informasi /</span> Kategori
</h4>

<div class="row g-4">
  <!-- Add Category Form -->
  <div class="col-md-4 animate-in">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0"><i class="bx bx-plus-circle text-primary me-2"></i>Tambah Kategori</h5>
      </div>
      <div class="card-body">
        <form id="addCategoryForm">
          <div class="mb-3">
            <label class="form-label" for="newCategoryName">Nama Kategori <span class="text-danger">*</span></label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-category"></i></span>
              <input type="text" id="newCategoryName" class="form-control" placeholder="Contoh: Elektronik" required />
            </div>
          </div>
          <button type="submit" class="btn btn-primary w-100">
            <i class="bx bx-save me-1"></i> Simpan Kategori
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Category Table -->
  <div class="col-md-8 animate-in delay-2">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0"><i class="bx bx-category text-primary me-2"></i>Daftar Kategori</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th style="width:50px">No</th>
                <th>Kategori</th>
                <th style="width:120px">Aksi</th>
              </tr>
            </thead>
            <tbody id="categoryBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
