// app.js — Firebase CRUD + Category Management + Search/Filter + Stats + Toast
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.4/firebase-app.js";
import {
  getDatabase, ref, push, set, onValue, update, remove
} from "https://www.gstatic.com/firebasejs/10.12.4/firebase-database.js";

// ═══════════ FIREBASE CONFIG ═══════════
const firebaseConfig = {
  apiKey: "AIzaSyCTD9y48V-OZO5sKqRL4Hq8LLkjoYxHjJA",
  authDomain: "db-crud-9d395.firebaseapp.com",
  databaseURL: "https://db-crud-9d395-default-rtdb.asia-southeast1.firebasedatabase.app",
  projectId: "db-crud-9d395",
  storageBucket: "db-crud-9d395.firebasestorage.app",
  messagingSenderId: "982989578926",
  appId: "1:982989578926:web:6b86d1efa2d46e0ee6033f",
  measurementId: "G-LS6L9T0SYV"
};

const app = initializeApp(firebaseConfig);
const db  = getDatabase(app);
const itemsRef      = ref(db, "inventory");
const categoriesRef = ref(db, "categories");

// ═══════════ STATE ═══════════
let allData = {};
let allCategories = {};
let activeFilter = "all";
let searchQuery  = "";

// ═══════════ DOM ELEMENTS ═══════════
const createFormFull    = document.getElementById("createFormFull");
const tbody             = document.getElementById("dataBody");
const recentTbody       = document.getElementById("recentDataBody");
const categoryBody      = document.getElementById("categoryBody");
const searchInput       = document.getElementById("searchInput");
const filterContainer   = document.getElementById("filterContainer");
const addCategoryForm   = document.getElementById("addCategoryForm");
const kategoriFullSelect  = document.getElementById("kategoriFull");
const editKategoriSelect  = document.getElementById("editKategori");

// Stats
const statTotal      = document.getElementById("statTotal");
const statCategories = document.getElementById("statCategories");
const statTotalQty   = document.getElementById("statTotalQty");
const statTotalValue = document.getElementById("statTotalValue");

// Toast
const toastEl   = document.getElementById("appToast");
const toastBody = document.getElementById("toastBody");
let bsToast;

// Edit modal
const editModalEl = document.getElementById("editModal");
let editModal;

// Delete modal
const deleteModalEl = document.getElementById("deleteModal");
let deleteModal;
let pendingDeleteId   = null;
let pendingDeleteType = "asset"; // "asset" or "category"

// ═══════════ LAZY BOOTSTRAP INIT ═══════════
function getEditModal() {
  if (!editModal) editModal = new bootstrap.Modal(editModalEl);
  return editModal;
}
function getDeleteModal() {
  if (!deleteModal) deleteModal = new bootstrap.Modal(deleteModalEl);
  return deleteModal;
}
function getToast() {
  if (!bsToast) bsToast = new bootstrap.Toast(toastEl, { delay: 3000 });
  return bsToast;
}

// ═══════════ TOAST HELPER ═══════════
function showToast(message, type = "success") {
  const iconMap = {
    success: '<i class="bx bx-check-circle text-success me-2"></i>',
    error:   '<i class="bx bx-x-circle text-danger me-2"></i>',
    info:    '<i class="bx bx-info-circle text-primary me-2"></i>',
    warning: '<i class="bx bx-error text-warning me-2"></i>',
  };
  toastBody.innerHTML = `${iconMap[type] || iconMap.success}<span>${message}</span>`;
  getToast().show();
}

// ═══════════ FORMAT HELPERS ═══════════
function formatRupiah(num) {
  return "Rp " + Number(num).toLocaleString("id-ID");
}
function formatShortValue(num) {
  if (num >= 1_000_000_000) return "Rp " + (num / 1_000_000_000).toFixed(1).replace(/\.0$/, "") + "M";
  if (num >= 1_000_000) return "Rp " + (num / 1_000_000).toFixed(1).replace(/\.0$/, "") + "Jt";
  if (num >= 1_000) return "Rp " + (num / 1_000).toFixed(1).replace(/\.0$/, "") + "Rb";
  return "Rp " + String(num);
}
function getQtyClass(qty) {
  if (qty <= 5)  return "low";
  if (qty <= 20) return "medium";
  return "high";
}

// ═══════════ STATS UPDATE ═══════════
function updateStats(dataObj) {
  const entries  = Object.values(dataObj);
  const total    = entries.length;
  const categories = new Set(entries.map(i => (i.kategori || "").toLowerCase())).size;
  const totalQty   = entries.reduce((s, i) => s + (Number(i.jumlah) || 0), 0);
  const totalValue = entries.reduce((s, i) => s + ((Number(i.jumlah) || 0) * (Number(i.harga) || 0)), 0);

  animateCounter(statTotal, total);
  animateCounter(statCategories, categories);
  animateCounter(statTotalQty, totalQty);
  statTotalValue.textContent = formatShortValue(totalValue);
}

function animateCounter(el, target) {
  const current = parseInt(el.textContent) || 0;
  if (current === target) return;
  const diff = target - current;
  const steps = Math.min(Math.abs(diff), 20);
  const increment = diff / steps;
  let step = 0;
  const interval = setInterval(() => {
    step++;
    if (step >= steps) {
      el.textContent = target;
      clearInterval(interval);
    } else {
      el.textContent = Math.round(current + increment * step);
    }
  }, 30);
}

// ═══════════ FILTER BADGES ═══════════
function updateFilterBadges(dataObj) {
  const entries = Object.values(dataObj);
  const catCount = {};
  entries.forEach(item => {
    const cat = (item.kategori || "Lainnya").trim();
    catCount[cat] = (catCount[cat] || 0) + 1;
  });

  const existing = filterContainer.querySelectorAll("[data-filter]:not([data-filter='all'])");
  existing.forEach(el => el.remove());

  Object.entries(catCount)
    .sort((a, b) => b[1] - a[1])
    .forEach(([cat, count]) => {
      const badge = document.createElement("span");
      badge.className = "filter-badge" + (activeFilter === cat ? " active" : "");
      badge.dataset.filter = cat;
      badge.innerHTML = `${cat} <span class="badge-count">${count}</span>`;
      filterContainer.appendChild(badge);
    });

  const allBadge = filterContainer.querySelector("[data-filter='all']");
  if (allBadge) {
    allBadge.innerHTML = `<i class="bx bx-grid-alt"></i> Semua <span class="badge-count">${entries.length}</span>`;
    allBadge.classList.toggle("active", activeFilter === "all");
  }
}

// ═══════════ POPULATE CATEGORY DROPDOWNS ═══════════
function populateCategoryDropdowns(selectedValue) {
  const cats = Object.values(allCategories).map(c => c.name).sort();

  // Populate createFormFull dropdown
  kategoriFullSelect.innerHTML = '<option value="" disabled selected>-- Pilih Kategori --</option>';
  cats.forEach(cat => {
    const opt = document.createElement("option");
    opt.value = cat;
    opt.textContent = cat;
    kategoriFullSelect.appendChild(opt);
  });

  // Populate edit modal dropdown
  editKategoriSelect.innerHTML = '<option value="" disabled>-- Pilih Kategori --</option>';
  cats.forEach(cat => {
    const opt = document.createElement("option");
    opt.value = cat;
    opt.textContent = cat;
    if (selectedValue && cat === selectedValue) opt.selected = true;
    editKategoriSelect.appendChild(opt);
  });
}

// ═══════════ RENDER CATEGORY TABLE ═══════════
function renderCategoryTable() {
  categoryBody.innerHTML = "";
  const entries = Object.entries(allCategories);

  if (entries.length === 0) {
    categoryBody.innerHTML = `
      <tr>
        <td colspan="3">
          <div class="empty-state py-4">
            <i class="bx bx-category"></i>
            <h5>Belum ada kategori</h5>
            <p>Tambahkan kategori pertama</p>
          </div>
        </td>
      </tr>`;
    return;
  }

  entries
    .sort((a, b) => (a[1].name || "").localeCompare(b[1].name || ""))
    .forEach(([id, cat], idx) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td class="text-muted fw-semibold">${idx + 1}</td>
        <td><span class="fw-semibold">${cat.name ?? ""}</span></td>
        <td>
          <div class="d-flex gap-1">
            <button class="btn-action edit" data-action="edit-cat" data-id="${id}" data-name="${cat.name ?? ''}" title="Edit">
              <i class="bx bx-edit-alt"></i>
            </button>
            <button class="btn-action delete" data-action="delete-cat" data-id="${id}" data-name="${cat.name ?? ''}" title="Hapus">
              <i class="bx bx-trash"></i>
            </button>
          </div>
        </td>
      `;
      categoryBody.appendChild(tr);
    });
}

// ═══════════ RENDER FULL TABLE (Daftar Aset page) ═══════════
function renderTable() {
  const entries = Object.entries(allData);
  tbody.innerHTML = "";

  const filtered = entries.filter(([id, item]) => {
    if (activeFilter !== "all") {
      if ((item.kategori || "").trim() !== activeFilter) return false;
    }
    if (searchQuery) {
      const q = searchQuery.toLowerCase();
      const searchable = [
        item.namaBarang, item.kategori, item.keterangan
      ].map(v => (v || "").toLowerCase()).join(" ");
      if (!searchable.includes(q)) return false;
    }
    return true;
  });

  if (filtered.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="7">
          <div class="empty-state">
            <i class="bx bx-inbox"></i>
            <h5>${searchQuery || activeFilter !== "all" ? "Tidak ditemukan" : "Belum ada data"}</h5>
            <p>${searchQuery || activeFilter !== "all" ? "Coba ubah kata kunci atau filter Anda" : "Tambahkan aset pertama menggunakan form Tambah Aset"}</p>
          </div>
        </td>
      </tr>`;
    return;
  }

  filtered.forEach(([id, item], idx) => {
    const tr = document.createElement("tr");
    const qtyClass = getQtyClass(Number(item.jumlah) || 0);
    tr.innerHTML = `
      <td class="text-muted fw-semibold">${idx + 1}</td>
      <td><span class="item-name">${item.namaBarang ?? ""}</span></td>
      <td><span class="category-tag">${item.kategori ?? ""}</span></td>
      <td><span class="qty-badge ${qtyClass}">${item.jumlah ?? 0}</span></td>
      <td class="price-text">${formatRupiah(item.harga ?? 0)}</td>
      <td class="text-muted">${item.keterangan ?? ""}</td>
      <td>
        <div class="d-flex gap-1">
          <button class="btn-action edit" data-action="edit" data-id="${id}" title="Edit">
            <i class="bx bx-edit-alt"></i>
          </button>
          <button class="btn-action delete" data-action="delete" data-id="${id}" data-name="${item.namaBarang ?? ''}" title="Hapus">
            <i class="bx bx-trash"></i>
          </button>
        </div>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

// ═══════════ RENDER RECENT TABLE (Dashboard — last 5) ═══════════
function renderRecentTable() {
  const entries = Object.entries(allData);
  recentTbody.innerHTML = "";

  if (entries.length === 0) {
    recentTbody.innerHTML = `
      <tr>
        <td colspan="5">
          <div class="empty-state py-4">
            <i class="bx bx-inbox"></i>
            <h5>Belum ada data</h5>
            <p>Tambahkan aset pertama</p>
          </div>
        </td>
      </tr>`;
    return;
  }

  const sorted = entries
    .sort((a, b) => (b[1].createdAt || 0) - (a[1].createdAt || 0))
    .slice(0, 5);

  sorted.forEach(([id, item]) => {
    const tr = document.createElement("tr");
    const qtyClass = getQtyClass(Number(item.jumlah) || 0);
    tr.innerHTML = `
      <td><span class="item-name">${item.namaBarang ?? ""}</span></td>
      <td><span class="category-tag">${item.kategori ?? ""}</span></td>
      <td><span class="qty-badge ${qtyClass}">${item.jumlah ?? 0}</span></td>
      <td class="price-text">${formatRupiah(item.harga ?? 0)}</td>
      <td>
        <div class="d-flex gap-1">
          <button class="btn-action edit" data-action="edit" data-id="${id}" title="Edit">
            <i class="bx bx-edit-alt"></i>
          </button>
          <button class="btn-action delete" data-action="delete" data-id="${id}" data-name="${item.namaBarang ?? ''}" title="Hapus">
            <i class="bx bx-trash"></i>
          </button>
        </div>
      </td>
    `;
    recentTbody.appendChild(tr);
  });
}

// ═══════════ CREATE ASSET (Full page form only) ═══════════
createFormFull.addEventListener("submit", async (e) => {
  e.preventDefault();
  const data = {
    namaBarang: document.getElementById("namaBarangFull").value.trim(),
    kategori:   document.getElementById("kategoriFull").value.trim(),
    jumlah:     Number(document.getElementById("jumlahFull").value),
    harga:      Number(document.getElementById("hargaFull").value),
    keterangan: document.getElementById("keteranganFull").value.trim(),
    createdAt:  Date.now()
  };
  try {
    const newRef = push(itemsRef);
    await set(newRef, data);
    createFormFull.reset();
    showToast(`Aset <strong>${data.namaBarang}</strong> berhasil ditambahkan!`, "success");
  } catch (err) {
    showToast("Gagal menyimpan data: " + err.message, "error");
  }
});

// ═══════════ ADD CATEGORY ═══════════
addCategoryForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  const name = document.getElementById("newCategoryName").value.trim();
  if (!name) return;

  // Check if category already exists
  const existing = Object.values(allCategories).find(c => c.name.toLowerCase() === name.toLowerCase());
  if (existing) {
    showToast(`Kategori <strong>${name}</strong> sudah ada!`, "warning");
    return;
  }

  try {
    const newRef = push(categoriesRef);
    await set(newRef, { name, createdAt: Date.now() });
    addCategoryForm.reset();
    showToast(`Kategori <strong>${name}</strong> berhasil ditambahkan!`, "success");
  } catch (err) {
    showToast("Gagal menyimpan kategori: " + err.message, "error");
  }
});

// ═══════════ READ (Realtime) — ASSETS ═══════════
onValue(itemsRef, (snapshot) => {
  allData = snapshot.exists() ? snapshot.val() : {};
  updateStats(allData);
  updateFilterBadges(allData);
  renderTable();
  renderRecentTable();
});

// ═══════════ READ (Realtime) — CATEGORIES ═══════════
onValue(categoriesRef, (snapshot) => {
  allCategories = snapshot.exists() ? snapshot.val() : {};
  renderCategoryTable();
  populateCategoryDropdowns();
});

// ═══════════ SEARCH ═══════════
searchInput.addEventListener("input", (e) => {
  searchQuery = e.target.value.trim();
  renderTable();
});

// ═══════════ FILTER CLICK ═══════════
filterContainer.addEventListener("click", (e) => {
  const badge = e.target.closest(".filter-badge");
  if (!badge) return;
  activeFilter = badge.dataset.filter;
  filterContainer.querySelectorAll(".filter-badge").forEach(b => b.classList.remove("active"));
  badge.classList.add("active");
  renderTable();
});

// ═══════════ TABLE ACTIONS (Edit / Delete) — ASSETS ═══════════
function handleTableAction(e) {
  const btn = e.target.closest("button");
  if (!btn) return;
  const action = btn.dataset.action;
  const id     = btn.dataset.id;

  if (action === "delete") {
    pendingDeleteId = id;
    pendingDeleteType = "asset";
    document.getElementById("deleteItemName").textContent = btn.dataset.name || "item ini";
    getDeleteModal().show();
  }
  if (action === "edit") {
    openEdit(id);
  }
}

tbody.addEventListener("click", handleTableAction);
recentTbody.addEventListener("click", handleTableAction);

// ═══════════ TABLE ACTIONS — CATEGORIES ═══════════
categoryBody.addEventListener("click", (e) => {
  const btn = e.target.closest("button");
  if (!btn) return;
  const action = btn.dataset.action;
  const id     = btn.dataset.id;

  if (action === "delete-cat") {
    pendingDeleteId = id;
    pendingDeleteType = "category";
    document.getElementById("deleteItemName").textContent = btn.dataset.name || "kategori ini";
    getDeleteModal().show();
  }
  if (action === "edit-cat") {
    const newName = prompt("Edit nama kategori:", btn.dataset.name || "");
    if (newName !== null && newName.trim() !== "") {
      updateCategory(id, newName.trim(), btn.dataset.name);
    }
  }
});

async function updateCategory(id, newName, oldName) {
  try {
    const catRef = ref(db, `categories/${id}`);
    await update(catRef, { name: newName });

    // Update all assets that use the old category name
    const entriesToUpdate = Object.entries(allData).filter(([_, item]) => item.kategori === oldName);
    for (const [assetId] of entriesToUpdate) {
      const assetRef = ref(db, `inventory/${assetId}`);
      await update(assetRef, { kategori: newName });
    }

    showToast(`Kategori diubah menjadi <strong>${newName}</strong>!`, "info");
  } catch (err) {
    showToast("Gagal mengupdate kategori: " + err.message, "error");
  }
}

// ═══════════ OPEN EDIT MODAL ═══════════
function openEdit(id) {
  const itemRef = ref(db, `inventory/${id}`);
  onValue(itemRef, (snap) => {
    if (!snap.exists()) return;
    const item = snap.val();
    document.getElementById("editId").value        = id;
    document.getElementById("editNama").value       = item.namaBarang ?? "";
    document.getElementById("editJumlah").value     = item.jumlah ?? 0;
    document.getElementById("editHarga").value      = item.harga ?? 0;
    document.getElementById("editKeterangan").value = item.keterangan ?? "";

    // Populate dropdown and select current value
    populateCategoryDropdowns(item.kategori ?? "");
    editKategoriSelect.value = item.kategori ?? "";

    getEditModal().show();
  }, { onlyOnce: true });
}

// ═══════════ UPDATE ASSET ═══════════
const editForm = document.getElementById("editForm");
editForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  const id = document.getElementById("editId").value;
  const payload = {
    namaBarang: document.getElementById("editNama").value.trim(),
    kategori:   document.getElementById("editKategori").value.trim(),
    jumlah:     Number(document.getElementById("editJumlah").value),
    harga:      Number(document.getElementById("editHarga").value),
    keterangan: document.getElementById("editKeterangan").value.trim(),
    updatedAt:  Date.now()
  };
  try {
    const itemRef = ref(db, `inventory/${id}`);
    await update(itemRef, payload);
    getEditModal().hide();
    showToast(`Aset <strong>${payload.namaBarang}</strong> berhasil diperbarui!`, "info");
  } catch (err) {
    showToast("Gagal mengupdate data: " + err.message, "error");
  }
});

// ═══════════ DELETE (Confirm Modal — handles both asset & category) ═══════════
document.getElementById("confirmDeleteBtn").addEventListener("click", async () => {
  if (!pendingDeleteId) return;
  try {
    if (pendingDeleteType === "category") {
      const catRef = ref(db, `categories/${pendingDeleteId}`);
      await remove(catRef);
      showToast("Kategori berhasil dihapus!", "warning");
    } else {
      const itemRef = ref(db, `inventory/${pendingDeleteId}`);
      await remove(itemRef);
      showToast("Aset berhasil dihapus!", "warning");
    }
    getDeleteModal().hide();
    pendingDeleteId = null;
    pendingDeleteType = "asset";
  } catch (err) {
    showToast("Gagal menghapus data: " + err.message, "error");
  }
});
