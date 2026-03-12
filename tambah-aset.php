<?php
require_once 'includes/auth_check.php';
$activePage = 'tambah-aset';
$pageTitle  = 'Tambah Aset';
include 'includes/header.php';
?>

<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Manajemen Aset /</span> Tambah Aset Baru
</h4>

<div class="row">
  <div class="col-12 animate-in">
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title mb-0"><i class="bx bx-plus-circle text-primary me-2"></i>Form Tambah Aset (Create)</h5>
        <small class="text-muted">Isi semua field lalu klik Simpan untuk menambahkan data ke Firebase Realtime Database</small>
      </div>
      <div class="card-body">
        <form id="createFormFull">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="namaBarangFull">Nama Barang <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-box"></i></span>
                <input type="text" id="namaBarangFull" class="form-control" placeholder="Masukkan nama barang/aset" required />
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="kategoriFull">Kategori <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-category"></i></span>
                <select id="kategoriFull" class="form-select" required>
                  <option value="" disabled selected>-- Pilih Kategori --</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label" for="jumlahFull">Jumlah <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-hash"></i></span>
                <input type="number" id="jumlahFull" class="form-control" min="0" placeholder="0" required />
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="hargaFull">Harga (Rp) <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text">Rp</span>
                <input type="number" id="hargaFull" class="form-control" min="0" placeholder="0" required />
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="keteranganFull">Keterangan <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-notepad"></i></span>
                <input type="text" id="keteranganFull" class="form-control" placeholder="Catatan tambahan" required />
              </div>
            </div>
          </div>
          <hr class="my-4" />
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="bx bx-save me-1"></i> Simpan Aset
            </button>
            <button type="reset" class="btn btn-outline-secondary">
              <i class="bx bx-reset me-1"></i> Reset Form
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
