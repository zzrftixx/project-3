{{-- resources/views/admin/databarang.blade.php --}}
@extends('layouts.app')

@section('title', 'Kelola Barang')

@push('styles')
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    @keyframes fade-in-down {
      from { opacity: 0; transform: translateY(-20px);}
      to { opacity: 1; transform: translateY(0);}
    }
    .animate-fade-in-down { animation: fade-in-down 0.3s ease-out;}
    
    .table-hover tr {
      transition: all 0.15s ease-in-out;
    }
    
    .table-hover tr:hover {
      background-color: #f8fafc;
    }
    
    .modal-transition {
      transition: all 0.2s ease;
    }
    
    /* Custom file input styling */
    .custom-file-input::-webkit-file-upload-button {
      visibility: hidden;
      width: 0;
    }
    .custom-file-input::before {
      content: 'Pilih File';
      display: inline-block;
      background: #f3f4f6;
      border: 1px solid #d1d5db;
      border-radius: 0.375rem;
      padding: 6px 12px;
      outline: none;
      white-space: nowrap;
      cursor: pointer;
      font-size: 0.875rem;
      font-weight: 500;
      color: #4b5563;
      margin-right: 10px;
    }
    .custom-file-input:hover::before {
      background: #e5e7eb;
    }
  </style>
@endpush

@section('content')
  <div class="container mx-auto px-4 py-6 max-w-7xl">
    <!-- Header dengan Card Style -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
      <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight flex items-center">
          <i class="fas fa-box-open text-blue-500 mr-3"></i>
          <span>Manajemen Produk</span>
        </h1>
        <div>
          <!-- Tombol Tambah Barang -->
          <button
            onclick="openAddModal()"
            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-white text-sm font-medium shadow-sm transition duration-150 hover:bg-blue-700 hover:shadow focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
          >
            <i class="fas fa-boxes"></i> Tambah Produk
          </button>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end px-6 py-4 bg-gray-50 border-b border-gray-100">
  <div>
    <button
      id="bulkDeleteBtn"
      class="relative inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-white text-sm font-medium shadow-sm transition duration-150 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
      disabled
      onclick="submitBulkDelete()"
    >
      <i class="fas fa-trash-alt"></i>
      Hapus Produk
      <span id="bulkCount" class="ml-1 inline-block bg-white text-red-600 rounded-full px-2 py‑0.5 text-xs font-semibold">0</span>
    </button>
  </div>
</div>
<form id="bulkDeleteForm" action="{{ route('admin.databarang.bulkDestroy') }}" method="POST" style="display: none;">
  @csrf
  @method('DELETE')
  {{-- Di sini nanti JS akan memasukkan <input name="ids[]" value="..."> untuk tiap ID terpilih --}}
</form>

    <!-- Alert Sukses dengan desain minimalis -->
    @if(session('success'))
      <div id="successAlert" class="fixed top-6 right-6 z-50 flex items-center px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 shadow-md animate-fade-in-down max-w-sm">
        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
        </svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
        <button onclick="closeSuccessAlert()" class="ml-auto focus:outline-none">
          <i class="fas fa-times text-green-500 hover:text-green-700"></i>
        </button>
      </div>
    @endif

    {{-- Tombol Bulk Delete (disabled kalau belum ada yang dicentang) --}}

    <!-- Tabel Barang dengan Desain Clean -->
    <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100">
      <!-- Header Bagian Table -->
      <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-lg font-medium text-gray-800">Daftar Produk</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola inventaris Produk disini</p>
      </div>
      
      <!-- Tabel dengan hover effect -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 table-hover">
  <thead class="bg-gray-50">
    <tr>
      {{-- Kolom checkbox master --}}
      <th class="whitespace-nowrap px-6 py-3.5 text-center">
        <input
          type="checkbox"
          id="selectAll"
          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
        />
      </th>
      {{-- Kolom Gambar --}}
      <th class="whitespace-nowrap px-6 py-3.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Gambar</th>
      <th class="whitespace-nowrap px-6 py-3.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nama Barang</th>
      <th class="whitespace-nowrap px-6 py-3.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Kategori</th>
      <th class="whitespace-nowrap px-6 py-3.5 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Stok</th>
      <th class="whitespace-nowrap px-6 py-3.5 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Harga</th>
      <th class="whitespace-nowrap px-6 py-3.5 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
    </tr>
  </thead>
  <tbody class="bg-white divide-y divide-gray-100">
    @foreach($barangs as $barang)
      <tr data-id="{{ $barang->id }}" class="hover:bg-gray-50">
        {{-- Checkbox per‐baris --}}
        <td class="px-6 py-4 text-center">
          <input
            type="checkbox"
            class="rowCheckbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            value="{{ $barang->id }}"
          />
        </td>
        {{-- Kolom Gambar --}}
        <td class="px-6 py-4">
          @if($barang->image)
            <img src="{{ asset('storage/' . $barang->image) }}" alt="{{ $barang->name }}" class="h-14 w-14 object-cover rounded-lg shadow-sm border border-gray-200">
          @else
            <div class="h-14 w-14 bg-gray-100 flex items-center justify-center rounded-lg">
              <i class="fas fa-image text-gray-400 text-lg"></i>
            </div>
          @endif
        </td>
        {{-- Kolom Nama + Deskripsi --}}
        <td class="px-6 py-4">
          <p class="text-gray-800 font-medium">{{ $barang->name }}</p>
          <p class="text-xs text-gray-500 mt-0.5 truncate max-w-xs">{{ $barang->description ?: 'Tidak ada deskripsi' }}</p>
        </td>
        {{-- Kolom Kategori --}}
        <td class="px-6 py-4">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
            {{ $barang->category == 'Elektronik' ? 'bg-blue-100 text-blue-800' : 
               ($barang->category == 'Perkakas' ? 'bg-amber-100 text-amber-800' : 
                'bg-emerald-100 text-emerald-800') }}">
            {{ $barang->category }}
          </span>
        </td>
        {{-- Kolom Stok --}}
        <td class="px-6 py-4 text-right whitespace-nowrap">
          <span class="font-medium {{ $barang->stock > 10 ? 'text-green-600' : ($barang->stock > 5 ? 'text-yellow-600' : 'text-red-600') }}">
            {{ $barang->stock }}
          </span>
        </td>
        {{-- Kolom Harga --}}
        <td class="px-6 py-4 text-right font-medium text-gray-800 whitespace-nowrap">
          Rp {{ number_format($barang->price,0,',','.') }}
        </td>
        {{-- Kolom Aksi --}}
        <td class="px-6 py-4 text-center">
          <div class="flex items-center justify-center space-x-2">
            <button
              type="button"
              class="p-1.5 rounded-md text-blue-500 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-colors duration-150"
              onclick="editItem({{ $barang->id }})"
              title="Edit"
            ><i class="fas fa-edit"></i></button>
            <button
              type="button"
              class="p-1.5 rounded-md text-red-500 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400 transition-colors duration-150"
              onclick="showDeleteModal({{ $barang->id }}, '{{ addslashes($barang->name) }}')"
              title="Hapus"
            ><i class="fas fa-trash-alt"></i></button>
            <form id="delete-form-{{ $barang->id }}" action="{{ route('admin.databarang.destroy', $barang) }}" method="POST" style="display:none;">
              @csrf @method('DELETE')
            </form>
          </div>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
      </div>
      
      <!-- Empty State jika tidak ada barang -->
      @if(count($barangs) == 0)
        <div class="flex flex-col items-center justify-center py-12 px-4">
          <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada produk</h3>
          <p class="text-gray-500 text-center max-w-sm mb-4">Tambahkan produk baru untuk mulai mengelola inventaris Anda</p>
          <button
            onclick="openAddModal()"
            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white text-sm font-medium"
          >
            <i class="fas fa-plus"></i> Tambah Barang Pertama
          </button>
        </div>
      @endif
    </div>

    <!-- Modal Tambah/Edit Barang -->
    <div
      id="itemModal"
      class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 p-4 modal-transition"
      role="dialog"
      aria-modal="true"
      aria-labelledby="modalTitle"
    >
      <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-lg overflow-y-auto max-h-[90vh] modal-transition">
        <button
          type="button"
          onclick="closeModal()"
          class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 focus:outline-none"
          aria-label="Tutup Modal"
        >
          <i class="fas fa-times text-lg"></i>
        </button>
        <h2 id="modalTitle" class="text-xl font-semibold text-gray-800 mb-5">Tambah Barang Baru</h2>

        <form id="itemForm" method="POST" action="{{ route('admin.databarang.store') }}" enctype="multipart/form-data" class="space-y-4">
          @csrf
          <input type="hidden" name="_method" id="formMethod" value="POST">
          <input type="hidden" name="id" id="itemId">

          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
            <input
              type="text"
              name="name"
              id="name"
              required
              class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-800 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm"
              placeholder="Masukkan nama barang"
            />
          </div>

          <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
            <div class="relative">
              <select
                name="category"
                id="category"
                required
                class="block w-full appearance-none rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-800 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 pr-8 sm:text-sm"
              >
                <option value="" disabled selected>Pilih Kategori</option>
                <option value="Elektronik">Elektronik</option>
                <option value="Aksesoris">Aksesoris</option>
                <option value="CCTV Indoor">CCTV Indoor</option>
                <option value="CCTV Outdoor">CCTV Outdoor</option>
                <option value="IP Camera">IP Camera</option>
                <option value="DVR/NVR">DVR/NVR</option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                <i class="fas fa-chevron-down text-xs"></i>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
              <input
                type="number"
                name="stock"
                id="stock"
                min="0"
                required
                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-800 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm"
                placeholder="Jumlah stok"
              />
            </div>

            <div>
              <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="text-gray-500 sm:text-sm"></span>
                </div>
                <input
                  type="number"
                  name="price"
                  id="price"
                  min="0"
                  required
                  class="block w-full rounded-lg border border-gray-300 bg-white pl-12 px-3 py-2 text-gray-800 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm"
                  placeholder="Harga satuan"
                />
              </div>
            </div>
          </div>

          <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea
              name="description"
              id="description"
              rows="3"
              class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-800 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm"
              placeholder="Deskripsi barang (opsional)"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Barang</label>
            <div class="mt-1 flex items-center">
              <label class="w-full flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm cursor-pointer hover:bg-gray-50 transition-colors duration-150">
                <i class="fas fa-upload mr-2 text-gray-500"></i>
                <span class="text-sm text-gray-600">Pilih gambar</span>
                <input
                  type="file"
                  name="image"
                  id="image"
                  accept="image/*"
                  class="sr-only"
                />
              </label>
            </div>
            <p class="mt-1 text-xs text-gray-500">Format JPG, PNG, maksimal 2MB</p>
            <div id="previewImage" class="mt-2"></div>
            <input type="hidden" name="current_image" id="current_image">
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 sticky bottom-0 bg-white mt-5">
            <button
              type="button"
              onclick="closeModal()"
              class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
            >
              Batal
            </button>
            <button
              type="submit"
              class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
            >Simpan</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div
      id="deleteModal"
      class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 p-4 modal-transition"
      role="dialog"
      aria-modal="true"
    >
      <div class="bg-white rounded-xl p-6 max-w-sm w-full shadow-lg relative modal-transition">
        <div class="mb-5 text-center">
          <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-50 mb-3">
            <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
          </div>
          <h2 class="text-lg font-semibold text-gray-800">Konfirmasi Hapus</h2>
        </div>
        
        <p class="text-sm text-gray-600 text-center mb-6">Apakah Anda yakin ingin menghapus <span id="deleteItemName" class="font-semibold text-gray-800"></span>?</p>
        
        <div class="flex justify-center gap-3">
          <button
            onclick="closeDeleteModal()"
            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition-colors duration-150"
          >Batal</button>
          <button
            onclick="confirmDelete()"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium text-sm transition-colors duration-150"
          >Hapus</button>
        </div>
      </div>
    </div>
  </div>


@endsection

@push('scripts')
<script>
  // Fungsi untuk membuka modal tambah paket
  // Fungsi untuk membuka modal tambah paket
  function openAddPackageModal() {
    const form = document.getElementById('packageForm');
    form.reset();
    form.action = '{{ route("admin.paket.store") }}';
    // pastikan jika ada hidden input _method, di‐reset:
    form.querySelector('input[name="_method"]')?.remove();

    document.getElementById('packageModalTitle').textContent = 'Tambah Paket Baru';
    closeAllModals(); // sembunyikan modal lain
    showModal('packageModal');
  }

  // Fungsi untuk membuka modal edit paket
  function openEditPackageModal(id) {
    const paketData = @json($pakets->keyBy('id'));
    const data = paketData[id];
    const form = document.getElementById('packageForm');

    form.reset();
    form.action = `/admin/paket/${id}`;

    // tambahkan hidden _method PUT jika belum ada
    let methodInput = form.querySelector('input[name="_method"]');
    if (!methodInput) {
      methodInput = document.createElement('input');
      methodInput.type = 'hidden';
      methodInput.name = '_method';
      form.prepend(methodInput);
    }
    methodInput.value = 'PUT';

    document.getElementById('packageModalTitle').textContent = 'Edit Paket';

    form.elements.name.value  = data.name;
    form.elements.price.value = data.price;

    // isi multi‐select barang
    const selectedItems = data.items.map(item => item.id);
    const selectEl = document.getElementById('package_items');
    Array.from(selectEl.options).forEach(opt => {
      opt.selected = selectedItems.includes(+opt.value);
    });

    closeAllModals();
    showModal('packageModal');
  }

  // Fungsi untuk menutup modal paket
  function closePackageModal() {
    hideModal('packageModal');
  }

  // Fungsi untuk membuka modal tambah/edit barang
  function openAddModal() {
    const form = document.getElementById('itemForm');
    form.reset();
    form.action = '{{ route("admin.databarang.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalTitle').textContent = 'Tambah Barang Baru';
    document.getElementById('previewImage').innerHTML = '';

    closeAllModals();
    showModal('itemModal');
  }

  // Utility: sembunyikan semua modal yang kita pakai
  function closeAllModals() {
    ['itemModal','packageModal','deleteModal'].forEach(id => {
      document.getElementById(id)?.classList.add('hidden');
      document.getElementById(id)?.classList.remove('flex');
    });
  }

  // Utility: tampilkan satu modal
  function showModal(id) {
    const m = document.getElementById(id);
    m.classList.remove('hidden');
    m.classList.add('flex');
  }

  // Konfirmasi hapus (barang atau paket)
  let deleteItemId = null;
  function showDeleteModal(id, name) {
    deleteItemId = id;
    document.getElementById('deleteItemName').textContent = name;
    closeAllModals();
    showModal('deleteModal');
  }
  function closeDeleteModal() {
    deleteItemId = null;
    hideModal('deleteModal');
  }
  function confirmDelete() {
    if (!deleteItemId) return;
    document.getElementById('delete-form-' + deleteItemId).submit();
  }

  // Utility: sembunyikan modal
  function hideModal(id) {
    const m = document.getElementById(id);
    m.classList.add('hidden');
    m.classList.remove('flex');
  }

  // Auto‐close alert sukses
  function closeSuccessAlert() {
    document.getElementById('successAlert')?.remove();
  }
  @if(session('success'))
    setTimeout(closeSuccessAlert, 3000);
  @endif

  // Fungsi untuk mengedit barang
  function editItem(id) {
    const barangs = @json($barangs->keyBy('id'));
    const data = barangs[id];
    const form = document.getElementById('itemForm');
    form.reset();
    form.action = `/admin/databarang/${id}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('modalTitle').textContent = 'Edit Barang';
    document.getElementById('itemId').value = id;
    form.elements.name.value = data.name;
    form.elements.category.value = data.category;
    form.elements.stock.value = data.stock;
    form.elements.price.value = data.price;
    form.elements.description.value = data.description;
    
    // Simpan referensi gambar saat ini (jika ada)
    if (data.image) {
      document.getElementById('current_image').value = data.image;
      document.getElementById('previewImage').innerHTML =
        `<div class="mt-2">
          <img src="${getImageUrl(data.image)}" alt="Gambar Barang" class="h-20 w-20 object-cover rounded border border-gray-300">
          <p class="text-xs text-gray-500 mt-1">Gambar saat ini</p>
        </div>`;
    } else {
      document.getElementById('current_image').value = '';
      document.getElementById('previewImage').innerHTML = '';
    }
    
    const modal = document.getElementById('itemModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Memastikan tombol simpan dan batal terlihat
    setTimeout(() => {
      window.scrollTo(0, document.body.scrollHeight);
    }, 100);
  }

  // Fungsi untuk mendapatkan URL gambar dari storage
  function getImageUrl(path) {
    if (path.startsWith('/storage/')) {
      return path;
    } else {
      return '/storage/' + path;
    }
  }

  // Preview gambar sebelum upload
  document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    
    imageInput.addEventListener('change', function(e) {
      const preview = document.getElementById('previewImage');
      preview.innerHTML = '';
      
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.innerHTML = `
            <div class="mt-2">
              <img src="${e.target.result}" alt="Preview" class="h-20 w-20 object-cover rounded border border-gray-300">
              <p class="text-xs text-gray-500 mt-1">Gambar baru yang akan diunggah</p>
            </div>`;
            
          // Pastikan tombol-tombol tetap terlihat setelah preview
          setTimeout(() => {
            window.scrollTo(0, document.body.scrollHeight);
          }, 100);
        }
        reader.readAsDataURL(this.files[0]);
      }
    });
  });

  function closeModal() {
    const modal = document.getElementById('itemModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    // Reset form setelah modal ditutup
    document.getElementById('itemForm').reset();
    document.getElementById('previewImage').innerHTML = '';
  }

  // Alert sukses auto-close
  function closeSuccessAlert() {
    const alert = document.getElementById('successAlert');
    if(alert) alert.style.display = 'none';
  }

  // Bulk delete
  document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes    = document.querySelectorAll('.rowCheckbox');
    const bulkDeleteBtn    = document.getElementById('bulkDeleteBtn');
    const bulkCountSpan    = document.getElementById('bulkCount');

    // Fungsi untuk update jumlah ter‑check dan enable/disable tombol bulk
    function updateBulkState() {
      const checkedBoxes = document.querySelectorAll('.rowCheckbox:checked');
      const count = checkedBoxes.length;
      bulkCountSpan.textContent = count;
      bulkDeleteBtn.disabled = (count === 0);
    }

    // Event: saat master checkbox (selectAll) diklik
    selectAllCheckbox.addEventListener('change', function() {
      const isChecked = this.checked;
      rowCheckboxes.forEach(cb => {
        cb.checked = isChecked;
      });
      updateBulkState();
    });

    // Event: saat satu row checkbox diklik
    rowCheckboxes.forEach(cb => {
      cb.addEventListener('change', function() {
        // Kalau ada yang unchecked, auto uncheck master
        if (!this.checked) {
          selectAllCheckbox.checked = false;
        } else {
          // Jika semua row sudah dicek, maka set master jadi checked
          const allChecked = Array.from(rowCheckboxes).every(c => c.checked);
          selectAllCheckbox.checked = allChecked;
        }
        updateBulkState();
      });
    });
  });

  // Fungsi saat user klik “Hapus Terpilih”
  function submitBulkDelete() {
    // Ambil semua checkbox yang ter‐cek
    const checkedBoxes = document.querySelectorAll('.rowCheckbox:checked');
    if (checkedBoxes.length === 0) return;

    // Minta konfirmasi sederhana
    if (!confirm(`Anda yakin ingin menghapus ${checkedBoxes.length} barang terpilih?`)) {
      return;
    }

    // Isi form bulkDeleteForm dengan hidden input untuk tiap id
    const form = document.getElementById('bulkDeleteForm');
    // Hapus dulu input sebelumnya, kalau ada
    form.querySelectorAll('input[name="ids[]"]').forEach(i => i.remove());

    checkedBoxes.forEach(cb => {
      const hidden = document.createElement('input');
      hidden.type = 'hidden';
      hidden.name = 'ids[]';
      hidden.value = cb.value;
      form.appendChild(hidden);
    });

    form.submit();
  }

  @if(session('success'))
    setTimeout(() => closeSuccessAlert(), 3000);
  @endif
</script>
@endpush