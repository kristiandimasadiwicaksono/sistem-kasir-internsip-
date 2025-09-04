@extends('layouts.main')

@section('title', 'Restock')

@section('content')
<div class="container mx-auto mt-10 px-4 md:px-8">
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Manajemen Restock</h1>

        <!-- Tab Navigation -->
        <div class="flex justify-center gap-4 mb-8">
            <button id="tab-list" class="tab-button py-2 px-4 rounded-lg font-semibold transition-colors duration-300 bg-blue-600 text-white shadow-lg">Daftar Restock</button>
            <button id="tab-add" class="tab-button py-2 px-4 rounded-lg font-semibold transition-colors duration-300">Buat Pesanan Restock</button>
            <button id="tab-history" class="tab-button py-2 px-4 rounded-lg font-semibold transition-colors duration-300">Riwayat Restock</button>
        </div>

        <!-- Restock List Tab -->
        <div id="restock-list-view" class="tab-content">
            <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Supplier</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Jumlah Dipesan</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Jumlah Diterima</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Pembayaran</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Detail Produk</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($restocks as $restock)
                        <tr class="hover:bg-gray-100 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ \Carbon\Carbon::parse($restock->tanggal)->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $restock->supplier->nama }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $restock->details->sum('jumlah_dipesan') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $restock->details->sum('jumlah_diterima') }}</td>                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                @if($restock->status_pembayaran === 'LUNAS')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full">Lunas</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full">Belum Lunas</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                @php
                                    // Hitung status berdasarkan restock_detail
                                    $totalOrdered = $restock->details->sum('jumlah_dipesan');
                                    $totalReceived = $restock->details->sum('jumlah_diterima');
                                    
                                    if ($totalReceived == 0) {
                                        $overallStatus = 'BELUM_DITERIMA';
                                        $statusClass = 'bg-red-100 text-red-800';
                                    } elseif ($totalReceived < $totalOrdered) {
                                        $overallStatus = 'SEBAGIAN';
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                    } else {
                                        $overallStatus = 'SELESAI';
                                        $statusClass = 'bg-green-100 text-green-800';
                                    }
                                @endphp
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ $overallStatus }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800">
                                <ul class="list-disc pl-4 space-y-1">
                                    @foreach($restock->details as $detail)
                                        <li>
                                            {{ $detail->produk->nama_produk }}
                                            <span class="text-xs text-gray-500">
                                                ({{ $detail->jumlah_diterima }}/{{ $detail->jumlah_dipesan }})
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                <button 
                                    onclick="openReceiveModal({{ json_encode($restock->details) }}, {{ $restock->id }})" 
                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-100 text-blue-800 hover:bg-blue-200 disabled:opacity-50 disabled:pointer-events-none">
                                    Terima Barang
                                </button>

                                @if($restock->details->sum('jumlah_diterima') > 0)
                                <button onclick="openReturnModal({{ json_encode($restock->details->where('jumlah_diterima', '>', '0')) }}, {{ $restock->id }})" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent
                                    bg-orange-100 text-orange-800 hover:bg-orange-200 disabled:pointer-events-none ml-2">
                                    retur
                                </button>
                                @endif

                                <form action="{{ route('restock.destroy', $restock->id) }}" method="POST" class="inline-block" onsubmit="handleDelete(event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-red-100 text-red-800 hover:bg-red-200 disabled:opacity-50 disabled:pointer-events-none ml-2">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-gray-500">Tidak ada data restock.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex justify-center mt-6">
                {{ $restocks->links() }}
            </div>
        </div>

        <!-- Add Restock Tab -->
        <div id="restock-add-view" class="tab-content hidden">
            <form id="restock-form" action="{{ route('restock.store') }}" method="POST" class="space-y-6">
                @csrf
                <!-- Supplier Selection -->
                <div>
                    <label for="supplier" class="block text-sm font-medium text-gray-700 mb-2">Pilih Supplier</label>
                    <select id="supplier" name="id_supplier" required class="py-3 px-4 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Method -->
                <div>
                    <label for="status_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                    <select id="status_pembayaran" name="status_pembayaran" required 
                        class="py-3 px-4 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="BELUM_LUNAS">Belum Lunas</option>
                        <option value="LUNAS">Lunas</option>
                    </select>
                </div>

                <!-- Products to Restock -->
                <div id="products-container" class="space-y-4 border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">Produk yang Dipesan</h3>
                    <div class="product-item flex items-center gap-4">
                        <select name="products[0][id_produk]" required class="flex-1 py-3 px-4 border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Produk</option>
                            @foreach($produk as $item)
                            <option value="{{ $item->id }}">{{ $item->nama_produk }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="products[0][jumlah_dipesan]" min="1" required placeholder="Jumlah Dipesan" class="w-24 py-3 px-4 border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <button type="button" id="add-product-btn" class="w-full py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-100 text-blue-800 hover:bg-blue-200 disabled:opacity-50 disabled:pointer-events-none">
                    Tambah Produk
                </button>

                <div class="flex justify-end gap-x-2">
                    <button type="submit" id="submit-btn" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none shadow-md">
                        <span id="submit-text">Simpan Pesanan Restock</span>
                        <div id="loading-spinner" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                    </button>
                </div>
            </form>
        </div>

        <!-- History Restock Tab -->
        <div id="restock-history-view" class="tab-content hidden">
            <div class="flex justify-end mb-4">
                <a href="{{ route('restock.export') }}"
                class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg 
                        bg-green-600 text-white hover:bg-green-700 shadow-md">
                    Export Excel
                </a>
            </div>
            <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Supplier</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Produk</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Dipesan</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Diterima</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Retur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($history ?? [] as $item)
                        <tr class="hover:bg-gray-100 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ \Carbon\Carbon::parse($item['tanggal'])->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $item['supplier'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $item['produk'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 text-center">{{ $item['jumlah_dipesan'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 text-center">{{ $item['jumlah_diterima'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 text-center">{{ $item['jumlah_retur'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500">Belum ada riwayat restock.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div id="confirmation-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm">
        <h3 class="text-xl font-bold mb-4 text-gray-800">Konfirmasi Hapus</h3>
        <p class="text-gray-700 mb-6">Apakah Anda yakin ingin menghapus restock ini? Tindakan ini tidak bisa dibatalkan.</p>
        <div class="flex justify-end gap-x-4">
            <button id="cancel-btn" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50">
                Batal
            </button>
            <button id="confirm-delete-btn" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 shadow-md">
                Hapus
            </button>
        </div>
    </div>
</div>

<!-- Custom Message Modal -->
<div id="message-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm">
        <h3 class="text-xl font-bold mb-4 text-gray-800" id="message-title"></h3>
        <p class="text-gray-700 mb-6" id="message-body"></p>
        <div class="flex justify-end">
            <button id="close-message-btn" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Receive Goods Modal -->
<div id="receive-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
        <h3 class="text-xl font-bold mb-4 text-gray-800">Terima Barang</h3>
        <p class="text-gray-700 mb-6">Pilih produk yang ingin diterima dan masukkan jumlahnya.</p>
        <form id="receive-form" action="" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" id="receive-restock-id" name="restock_id">
            <div id="receive-products-container">
                <!-- Products will be added dynamically here -->
            </div>
            <div class="flex justify-end gap-x-2 mt-6">
                <button type="button" id="close-receive-btn" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 shadow-md">
                    Simpan Penerimaan
                </button>
            </div>
        </form>
    </div>
</div>

<div id="retur-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
        <h3 class="text-xl font-bold mb-4 text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
            </svg>
            Return Barang ke Supplier
        </h3>
        <p class="text-gray-700 mb-6">Pilih produk yang ingin dikembalikan ke supplier.</p>
        <form id="retur-form" action="" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" id="retur-restock-id" name="restock_id">
            {{-- Alasan --}}
            <div>
                <label for="retur-reason" class="block text-sm font-medium text-gray-700 mb-2">Alasan Return</label>
                <select id="retur-reason" name="alasan_retur" required class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Alasan</option>
                    <option value="RUSAK">Barang Rusak</option>
                    <option value="KADALUARSA">Kadaluarsa</option>
                    <option value="TIDAK_SESUAI">Tidak Sesuai</option>
                    <option value="KELEBIHAN">Kadaluarsa</option>
                    <option value="LAINNYA">Lainnya</option>
                </select>
            </div>
            {{-- Catatan Tambahan --}}
            <div>
                <label for="retur-notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan</label>
                <textarea id="retur-notes" name="catatan" rows="3" 
                    class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Tambahkan catatan jika diperlukan..."></textarea> <!-- FIXED: ID dan format -->
            </div>

            {{-- Jumlah --}}
            <div id="retur-products-container" class="border-t pt-4">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Produk yang Dapat Dikembalikan</h4>
            </div>

            <div class="flex justify-end gap-x-2 mt-6">
                <button type="button" id="close-retur-btn" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-orange-600 text-white hover:bg-orange-700 shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                    Proses Return
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabListBtn = document.getElementById('tab-list');
    const tabAddBtn = document.getElementById('tab-add');
    const restockListView = document.getElementById('restock-list-view');
    const restockAddView = document.getElementById('restock-add-view');
    const productsContainer = document.getElementById('products-container');
    const addProductBtn = document.getElementById('add-product-btn');
    const restockForm = document.getElementById('restock-form');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const loadingSpinner = document.getElementById('loading-spinner');

    const confirmationModal = document.getElementById('confirmation-modal');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    let formToDelete = null;

    const messageModal = document.getElementById('message-modal');
    const messageTitle = document.getElementById('message-title');
    const messageBody = document.getElementById('message-body');
    const closeMessageBtn = document.getElementById('close-message-btn');

    const receiveModal = document.getElementById('receive-modal');
    const receiveForm = document.getElementById('receive-form');
    const receiveProductsContainer = document.getElementById('receive-products-container');
    const closeReceiveBtn = document.getElementById('close-receive-btn');

    const returModal = document.getElementById('retur-modal');
    const returForm = document.getElementById('retur-form');
    const returProductsContainer = document.getElementById('retur-products-container');
    const closeReturnBtn = document.getElementById('close-retur-btn');

    const tabHistoryBtn = document.getElementById('tab-history');
    const restockHistoryView = document.getElementById('restock-history-view');

    let productCounter = 0;

    // 🔹 Function to show custom message
    function showMessage(title, body, type = 'success') {
        messageTitle.textContent = title;
        messageBody.textContent = body;
        messageModal.classList.remove('hidden');
        messageModal.classList.add('flex');

        if (type === 'success') {
            setTimeout(() => {
                messageModal.classList.add('hidden');
                messageModal.classList.remove('flex');
            }, 3000);
        }
    }

    // 🔹 Switch tabs
    function switchTab(view) {
        if (view === 'list') {
            restockListView.classList.remove('hidden');
            restockAddView.classList.add('hidden');
            tabListBtn.classList.add('bg-blue-600', 'text-white', 'shadow-lg');
            tabAddBtn.classList.remove('bg-blue-600', 'text-white', 'shadow-lg');
        } else {
            restockListView.classList.add('hidden');
            restockAddView.classList.remove('hidden');
            tabListBtn.classList.remove('bg-blue-600', 'text-white', 'shadow-lg');
            tabAddBtn.classList.add('bg-blue-600', 'text-white', 'shadow-lg');
        }
    }

    // 🔹 Add product row
    addProductBtn.addEventListener('click', () => {
        productCounter++;
        const newProductItem = document.createElement('div');
        newProductItem.classList.add('product-item', 'flex', 'items-center', 'gap-4', 'mt-4');
        newProductItem.innerHTML = `
            <select name="products[${productCounter}][id_produk]" required class="flex-1 py-3 px-4 border border-gray-300 rounded-lg text-sm">
                <option value="">Pilih Produk</option>
                @foreach($produk as $item)
                <option value="{{ $item->id }}">{{ $item->nama_produk }}</option>
                @endforeach
            </select>
            <input type="number" name="products[${productCounter}][jumlah_dipesan]" min="1" required placeholder="Jumlah Dipesan" class="w-24 py-3 px-4 border border-gray-300 rounded-lg text-sm">
            <button type="button" class="remove-product-btn py-2 px-3 text-sm font-semibold rounded-lg bg-red-100 text-red-800 hover:bg-red-200">Hapus</button>
        `;
        productsContainer.appendChild(newProductItem);

        newProductItem.querySelector('.remove-product-btn').addEventListener('click', (e) => {
            e.target.closest('.product-item').remove();
        });
    });

    // 🔹 Tab listeners
    tabListBtn.addEventListener('click', () => switchTab('list'));
    tabAddBtn.addEventListener('click', () => switchTab('add'));

    // 🔹 Form submit with loading state
    restockForm.addEventListener('submit', () => {
        submitBtn.disabled = true;
        submitText.textContent = 'Menyimpan...';
        loadingSpinner.classList.remove('hidden');
    });

    // 🔹 Delete confirm modal
    window.handleDelete = (event) => {
        event.preventDefault();
        formToDelete = event.target.closest('form');
        confirmationModal.classList.remove('hidden');
        confirmationModal.classList.add('flex');
    };

    confirmDeleteBtn.addEventListener('click', () => {
        if (formToDelete) formToDelete.submit();
        confirmationModal.classList.add('hidden');
        confirmationModal.classList.remove('flex');
        formToDelete = null;
    });

    cancelBtn.addEventListener('click', () => {
        confirmationModal.classList.add('hidden');
        confirmationModal.classList.remove('flex');
        formToDelete = null;
    });

    closeMessageBtn.addEventListener('click', () => {
        messageModal.classList.add('hidden');
        messageModal.classList.remove('flex');
    });

    // 🔹 Open receive modal
    window.openReceiveModal = (details, restockId) => {
        receiveProductsContainer.innerHTML = '';
        document.getElementById('receive-restock-id').value = restockId;

        details.forEach(detail => {
            const remaining = detail.jumlah_dipesan - detail.jumlah_diterima;
            if (remaining > 0) {
                const productReceiveItem = document.createElement('div');
                productReceiveItem.classList.add('product-receive-item', 'border', 'p-4', 'rounded-lg', 'space-y-3');
                productReceiveItem.innerHTML = `
                    <div class="flex items-center gap-3">
                        <input type="checkbox" class="product-checkbox w-4 h-4" data-product-id="${detail.id_produk}" data-remaining="${remaining}">
                        <label class="text-sm font-semibold">${detail.produk.nama_produk} <span class="text-xs text-gray-500">(Sisa: ${remaining})</span></label>
                    </div>
                    <div class="receive-input-section hidden">
                        <input type="hidden" name="id_produk[]" value="${detail.id_produk}" disabled>
                        <input type="number" name="jumlah_diterima[]" min="1" max="${remaining}" placeholder="Jumlah Diterima" disabled class="py-2 px-3 border rounded-lg text-sm">
                        <button type="button" class="auto-fill-btn py-2 px-3 text-xs font-medium rounded-lg border border-gray-300 bg-orange-50 text-orange-700 hover:bg-orange-100" data-max="${remaining}">Isi Semua (${remaining})</button>
                    </div>
                `;
                receiveProductsContainer.appendChild(productReceiveItem);
            }
        });

        document.querySelectorAll('.product-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const section = this.closest('.product-receive-item').querySelector('.receive-input-section');
                const hid = section.querySelector('input[name="id_produk[]"]');
                const num = section.querySelector('input[name="jumlah_diterima[]"]');
                if (this.checked) {
                    section.classList.remove('hidden');
                    hid.disabled = false;
                    num.disabled = false;
                    num.required = true;
                } else {
                    section.classList.add('hidden');
                    hid.disabled = true;
                    num.disabled = true;
                    num.required = false;
                    num.value = '';
                }
            });
        });

        document.querySelectorAll('.auto-fill-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const maxValue = this.getAttribute('data-max');
                this.closest('.receive-input-section').querySelector('input[name="jumlah_diterima[]"]').value = maxValue;
            });
        });

        receiveModal.classList.remove('hidden');
        receiveModal.classList.add('flex');
    };

    closeReceiveBtn.addEventListener('click', () => {
        receiveModal.classList.add('hidden');
        receiveModal.classList.remove('flex');
    });

    receiveForm.addEventListener('submit', (e) => {
        const restockId = document.getElementById('receive-restock-id').value;
        receiveForm.action = `/restock/${restockId}/receive`;
        if (document.querySelectorAll('.product-checkbox:checked').length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu produk untuk diterima.');
        }
    });

    // 🔹 Open retur modal
    window.openReturnModal = (receivedDetails, restockId) => {
        returProductsContainer.innerHTML = '';
        document.getElementById('retur-restock-id').value = restockId;

        receivedDetails.forEach(detail => {
            if (detail.jumlah_diterima > 0) {
                const productReturnItem = document.createElement('div');
                productReturnItem.classList.add('product-retur-item', 'border', 'p-4', 'rounded-lg', 'bg-gray-50');
                productReturnItem.innerHTML = `
                    <div class="product-retur-item border border-gray-200 rounded-lg p-4 space-y-3 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" class="retur-checkbox w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500" data-product-id="${detail.id_produk}" data-available="${detail.jumlah_diterima}">
                                <label class="text-sm font-semibold text-gray-700">
                                    ${detail.produk.nama_produk} 
                                    <span class="text-xs text-gray-500">(Tersedia: ${detail.jumlah_diterima} unit)</span>
                                </label>
                            </div>
                        </div>
                        <div class="retur-input-section hidden flex items-center gap-4 pt-2">
                            <input type="hidden" name="id_produk[]" value="${detail.id_produk}" disabled>
                            <input type="number" name="jumlah_retur[]" min="1" max="${detail.jumlah_diterima}" placeholder="Jumlah Return" disabled class="flex-1 py-2 px-3 border border-gray-300 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                            <button type="button" class="auto-retur-btn py-2 px-3 text-xs font-medium rounded-lg border border-gray-300 bg-orange-50 text-orange-700 hover:bg-orange-100" data-max="${detail.jumlah_diterima}">
                                Isi Semua (${detail.jumlah_diterima})
                            </button>
                        </div>
                    </div>
                `;
                returProductsContainer.appendChild(productReturnItem);
            }
        });

        document.querySelectorAll('.retur-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const section = this.closest('.product-retur-item').querySelector('.retur-input-section');
                const hid = section.querySelector('input[name="id_produk[]"]');
                const num = section.querySelector('input[name="jumlah_retur[]"]');
                if (this.checked) {
                    section.classList.remove('hidden');
                    hid.disabled = false;
                    num.disabled = false;
                    num.required = true;
                } else {
                    section.classList.add('hidden');
                    hid.disabled = true;
                    num.disabled = true;
                    num.required = false;
                    num.value = '';
                }
            });
        });

        document.querySelectorAll('.auto-retur-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const maxValue = this.getAttribute('data-max');
                this.closest('.retur-input-section').querySelector('input[name="jumlah_retur[]"]').value = maxValue;
            });
        });

        returModal.classList.remove('hidden');
        returModal.classList.add('flex');
    };

    closeReturnBtn.addEventListener('click', () => {
        returModal.classList.add('hidden');
        returModal.classList.remove('flex');
    });

    returForm.addEventListener('submit', (e) => {
        const restockId = document.getElementById('retur-restock-id').value;
        returForm.action = `/restock/${restockId}/retur`;
        if (document.querySelectorAll('.retur-checkbox:checked').length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu produk untuk dikembalikan.');
        }
    });

    function switchTab(view) {
        if (view === 'list') {
            restockListView.classList.remove('hidden');
            restockAddView.classList.add('hidden');
            restockHistoryView.classList.add('hidden');
            tabListBtn.classList.add('bg-blue-600', 'text-white', 'shadow-lg');
            tabAddBtn.classList.remove('bg-blue-600', 'text-white', 'shadow-lg');
            tabHistoryBtn.classList.remove('bg-blue-600', 'text-white', 'shadow-lg');
        } else if (view === 'add') {
            restockListView.classList.add('hidden');
            restockAddView.classList.remove('hidden');
            restockHistoryView.classList.add('hidden');
            tabListBtn.classList.remove('bg-blue-600', 'text-white', 'shadow-lg');
            tabAddBtn.classList.add('bg-blue-600', 'text-white', 'shadow-lg');
            tabHistoryBtn.classList.remove('bg-blue-600', 'text-white', 'shadow-lg');
        } else if (view === 'history') {
            restockListView.classList.add('hidden');
            restockAddView.classList.add('hidden');
            restockHistoryView.classList.remove('hidden');
            tabListBtn.classList.remove('bg-blue-600', 'text-white', 'shadow-lg');
            tabAddBtn.classList.remove('bg-blue-600', 'text-white', 'shadow-lg');
            tabHistoryBtn.classList.add('bg-blue-600', 'text-white', 'shadow-lg');
        }
    }

    tabHistoryBtn.addEventListener('click', () => switchTab('history'));
});
</script>

@endsection