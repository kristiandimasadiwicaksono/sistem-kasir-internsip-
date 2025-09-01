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
        </div>

        <!-- Restock List Tab -->
        <div id="restock-list-view" class="tab-content">
            <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">ID Restock</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Supplier</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Jumlah Produk Dipesan</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Pembayaran</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($restocks as $restock)
                        <tr class="hover:bg-gray-100 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{ $restock->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ \Carbon\Carbon::parse($restock->tanggal)->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $restock->supplier->nama }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $restock->details->sum('jumlah_dipesan') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $restock->metode_pembayaran }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($restock->status_penerimaan == 'Sudah Diterima Semua')
                                        bg-green-100 text-green-800
                                    @elseif($restock->status_penerimaan == 'Penerimaan Parsial')
                                        bg-yellow-100 text-yellow-800
                                    @else
                                        bg-red-100 text-red-800
                                    @endif">
                                    {{ $restock->status_penerimaan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                <button onclick="openReceiveModal({{ json_encode($restock->details) }})" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-100 text-blue-800 hover:bg-blue-200 disabled:opacity-50 disabled:pointer-events-none">
                                    Terima Barang
                                </button>
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
                            <td colspan="7" class="text-center py-4 text-gray-500">Tidak ada data restock.</td>
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
                    <label for="metode_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                    <select id="metode_pembayaran" name="metode_pembayaran" required class="py-3 px-4 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Metode Pembayaran</option>
                        <option value="Tunai">Tunai</option>
                        <option value="Kredit">Kredit</option>
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

                <!-- Notes/Keterangan -->
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan (Opsional)</label>
                    <textarea id="keterangan" name="keterangan" rows="3" class="py-3 px-4 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-x-2">
                    <button type="submit" id="submit-btn" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none shadow-md">
                        <span id="submit-text">Simpan Pesanan Restock</span>
                        <div id="loading-spinner" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                    </button>
                </div>
            </form>
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
        <p class="text-gray-700 mb-6">Pilih produk dan masukkan jumlah yang diterima.</p>
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

        let productCounter = 0;

        // Function to show custom message modal
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

        // Function to switch tabs
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

        // Add a new product row to the form
        addProductBtn.addEventListener('click', () => {
            productCounter++;
            const newProductItem = document.createElement('div');
            newProductItem.classList.add('product-item', 'flex', 'items-center', 'gap-4', 'mt-4');
            newProductItem.innerHTML = `
                <select name="products[${productCounter}][id_produk]" required class="flex-1 py-3 px-4 border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Produk</option>
                    @foreach($produk as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_produk }}</option>
                    @endforeach
                </select>
                <input type="number" name="products[${productCounter}][jumlah_dipesan]" min="1" required placeholder="Jumlah Dipesan" class="w-24 py-3 px-4 border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="button" class="remove-product-btn py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-red-100 text-red-800 hover:bg-red-200">
                    Hapus
                </button>
            `;
            productsContainer.appendChild(newProductItem);

            // Add event listener to the new remove button
            newProductItem.querySelector('.remove-product-btn').addEventListener('click', (e) => {
                e.target.closest('.product-item').remove();
            });
        });

        // Event listeners for tab buttons
        tabListBtn.addEventListener('click', () => switchTab('list'));
        tabAddBtn.addEventListener('click', () => switchTab('add'));

        // Handle form submission with loading state
        restockForm.addEventListener('submit', (e) => {
            submitBtn.disabled = true;
            submitText.textContent = 'Menyimpan...';
            loadingSpinner.classList.remove('hidden');
        });

        // Handle delete button click with custom confirmation modal
        window.handleDelete = (event) => {
            event.preventDefault();
            formToDelete = event.target.closest('form');
            confirmationModal.classList.remove('hidden');
            confirmationModal.classList.add('flex');
        };

        confirmDeleteBtn.addEventListener('click', () => {
            if (formToDelete) {
                formToDelete.submit();
            }
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

        // Handle receive modal
        window.openReceiveModal = (details) => {
            receiveProductsContainer.innerHTML = '';
            details.forEach(detail => {
                const remaining = detail.jumlah_dipesan - detail.jumlah_diterima;
                const productReceiveItem = document.createElement('div');
                productReceiveItem.classList.add('product-receive-item', 'space-y-2');
                productReceiveItem.innerHTML = `
                    <p class="text-sm font-semibold text-gray-700">${detail.produk.nama_produk} (Sisa: ${remaining} unit)</p>
                    <div class="flex items-center gap-4">
                        <input type="hidden" name="id_produk" value="${detail.id_produk}">
                        <input type="number" name="jumlah_diterima" min="1" max="${remaining}" required placeholder="Jumlah Diterima" class="flex-1 py-3 px-4 border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                `;
                receiveProductsContainer.appendChild(productReceiveItem);
            });
            receiveModal.classList.remove('hidden');
            receiveModal.classList.add('flex');
        };

        closeReceiveBtn.addEventListener('click', () => {
            receiveModal.classList.add('hidden');
            receiveModal.classList.remove('flex');
        });

        // You'll need to update the form action for the receive form dynamically
        // or handle it via AJAX in a real application. This is a placeholder.
        receiveForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const restockId = document.getElementById('receive-restock-id').value;
            receiveForm.action = `/restock/${restockId}/receive`;
            receiveForm.submit();
        });
    });
</script>
@endsection
