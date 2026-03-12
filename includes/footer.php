<?php
// includes/footer.php
?>
            </div>
            <!-- / container-xxl -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl d-flex flex-wrap justify-content-center py-2">
                <div>
                  <strong>AssetPro</strong> — Sistem Manajemen Aset &amp; Inventaris &copy; <?= date('Y') ?>
                </div>
              </div>
            </footer>

            <div class="content-backdrop fade"></div>
          </div>
          <!-- / Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- ═══════════ EDIT MODAL ═══════════ -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form id="editForm">
            <div class="modal-header">
              <h5 class="modal-title"><i class="bx bx-edit-alt text-primary me-2"></i>Edit Aset (Update)</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" id="editId" />
              <div class="mb-3">
                <label class="form-label" for="editNama">Nama Barang</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bx-box"></i></span>
                  <input type="text" id="editNama" class="form-control" required />
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label" for="editKategori">Kategori</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bx-category"></i></span>
                  <select id="editKategori" class="form-select" required>
                    <option value="" disabled>-- Pilih Kategori --</option>
                  </select>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-6">
                  <label class="form-label" for="editJumlah">Jumlah</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-hash"></i></span>
                    <input type="number" id="editJumlah" class="form-control" min="0" required />
                  </div>
                </div>
                <div class="col-6">
                  <label class="form-label" for="editHarga">Harga (Rp)</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text">Rp</span>
                    <input type="number" id="editHarga" class="form-control" min="0" required />
                  </div>
                </div>
              </div>
              <div class="mb-0">
                <label class="form-label" for="editKeterangan">Keterangan</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bx-notepad"></i></span>
                  <input type="text" id="editKeterangan" class="form-control" required />
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i>Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ═══════════ DELETE CONFIRM MODAL ═══════════ -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
          <div class="modal-body text-center py-4">
            <div class="delete-icon-wrapper">
              <i class="bx bx-error-circle"></i>
            </div>
            <h5 class="fw-bold mb-2">Hapus Data?</h5>
            <p class="text-muted mb-0" style="font-size:0.9rem">
              Data <strong id="deleteItemName"></strong> akan dihapus secara permanen dari database.
            </p>
          </div>
          <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
            <button class="btn btn-danger" id="confirmDeleteBtn">
              <i class="bx bx-trash me-1"></i>Hapus
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══════════ TOAST ═══════════ -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
      <div class="toast align-items-center border-0" id="appToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body" id="toastBody">
            <i class="bx bx-check-circle text-success me-2"></i>
            <span>Berhasil!</span>
          </div>
          <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>

    <!-- ═══════════ CORE JS ═══════════ -->
    <script src="sneat-1.0.0/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="sneat-1.0.0/assets/vendor/libs/popper/popper.js"></script>
    <script src="sneat-1.0.0/assets/vendor/js/bootstrap.js"></script>
    <script src="sneat-1.0.0/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="sneat-1.0.0/assets/vendor/js/menu.js"></script>

    <!-- Main JS -->
    <script src="sneat-1.0.0/assets/js/main.js"></script>

    <!-- Navbar search → redirect to daftar-aset -->
    <script>
      document.getElementById('navSearchInput')?.addEventListener('focus', () => {
        window.location.href = 'daftar-aset.php';
      });
    </script>

    <!-- Firebase App JS -->
    <script type="module" src="js/app.js"></script>
  </body>
</html>
