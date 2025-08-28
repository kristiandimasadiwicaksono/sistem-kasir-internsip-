@extends('layouts.main')

@section('title','Tambah Penjualan')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-10">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-gray-800">
                Tambah Penjualan
            </h2>
            <p class="text-sm text-gray-500 mt-2">Pilih produk dan jumlah yang ingin dijual. Atur setiap item sesuai kebutuhan.</p>
        </div>

        {{-- Tampilkan error dari server-side validation --}}
        @if($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-3 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-semibold">Ada kesalahan pada data yang Anda masukkan:</span>
                </div>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="form-penjualan" method="POST" action="{{ route('penjualan.store') }}">
            @csrf

            <!-- Container produk -->
            <div id="produk-container" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 produk-item bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div class="md:col-span-2">
                        <label for="produk-0" class="block text-sm font-medium text-gray-700">Produk</label>
                        {{-- Baris pertama wajib diisi (required attribute) --}}
                        <select name="products[0][id_produk]" id="produk-0" 
                            class="mt-1 block w-full border border-gray-300 rounded-lg py-2.5 px-4 focus:border-blue-500 focus:ring-blue-500 text-sm produk-select" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($produk as $p)
                                <option value="{{ $p->id }}" data-stok="{{ $p->stok ?? 0 }}" {{ ($p->stok ?? 0) <= 0 ? 'disabled' : '' }}>
                                    {{ $p->nama_produk }} (stok: {{ $p->stok ?? 0 }}){{ ($p->stok ?? 0) <= 0 ? ' — STOK HABIS' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="jumlah-0" class="block text-sm font-medium text-gray-700">Jumlah</label>
                        {{-- Baris pertama wajib diisi (required attribute) --}}
                        <input type="number" name="products[0][jumlah]" id="jumlah-0" min="1"
                            class="mt-1 block w-full border border-gray-300 rounded-lg py-2.5 px-4 focus:border-blue-500 focus:ring-blue-500 text-sm jumlah-input" required>
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="removeProduk(this)"
                            class="w-full py-2.5 px-4 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg transition duration-300 hover:bg-red-100">
                            <svg class="h-4 w-4 inline-block mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tombol tambah produk -->
            <div class="mt-6">
                <button type="button" onclick="addProduk()" 
                    class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-800 shadow-sm transition duration-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Produk
                </button>
            </div>

            <!-- Buttons -->
            <div class="mt-8 flex justify-end gap-x-3">
                <a href="{{ route('penjualan.index') }}" 
                   class="py-2.5 px-4 rounded-lg border border-gray-300 bg-white text-gray-800 font-medium transition duration-300 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" 
                   class="py-2.5 px-4 rounded-lg border border-transparent bg-blue-600 text-white font-medium transition duration-300 hover:bg-blue-700">
                    Bayar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Custom Alert Modal -->
<div id="alert-modal" class="hidden fixed inset-0 z-50 overflow-y-auto backdrop-blur-sm" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 py-10 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.536-1.637 1.745-3.033L13.745 4.967c-.791-1.488-2.709-1.488-3.5 0L3.397 15.967c-.791 1.396.205 3.033 1.745 3.033z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Data tidak Valid
                        </h3>
                        <div class="mt-2">
                            <ul id="alert-messages" class="text-sm text-gray-500 space-y-1">
                                <!-- Messages will be inserted here -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeModal()" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let index = 1;

// Build option HTML once to reuse in JS
const produkOptions = `
    <option value="">-- Pilih Produk --</option>
    @foreach($produk as $p)
        <option value="{{ $p->id }}" data-stok="{{ $p->stok ?? 0 }}" {{ ($p->stok ?? 0) <= 0 ? 'disabled' : '' }}>
            {{ addslashes($p->nama_produk) }} (stok: {{ $p->stok ?? 0 }}){{ ($p->stok ?? 0) <= 0 ? ' — STOK HABIS' : '' }}
        </option>
    @endforeach
`;

// helper to escape single quotes inside template (addslashes used above)
function addslashes_js(str) {
    return str.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
}

function addProduk() {
    const container = document.getElementById('produk-container');
    const item = document.createElement('div');
    item.classList.add('grid', 'grid-cols-1', 'md:grid-cols-4', 'gap-4', 'produk-item', 'bg-gray-50', 'p-4', 'rounded-xl', 'border', 'border-gray-100');
    item.innerHTML = `
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Produk</label>
            <select name="products[${index}][id_produk]" class="mt-1 block w-full border border-gray-300 rounded-lg py-2.5 px-4 focus:border-blue-500 focus:ring-blue-500 text-sm produk-select">
                ${produkOptions}
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Jumlah</label>
            <input type="number" name="products[${index}][jumlah]" min="1" class="mt-1 block w-full border border-gray-300 rounded-lg py-2.5 px-4 focus:border-blue-500 focus:ring-blue-500 text-sm jumlah-input">
        </div>
        <div class="flex items-end">
            <button type="button" onclick="removeProduk(this)" class="w-full py-2.5 px-4 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg transition duration-300 hover:bg-red-100">
                <svg class="h-4 w-4 inline-block mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus
            </button>
        </div>
    `;
    container.appendChild(item);
    index++;
}

function removeProduk(button) {
    const el = button.closest('.produk-item');
    if (el) el.remove();
}

function showModal(messages) {
    const modal = document.getElementById('alert-modal');
    const messageList = document.getElementById('alert-messages');

    // Clear existing messages
    messageList.innerHTML = '';
    
    // Add new messages as list items
    messages.forEach(msg => {
        const li = document.createElement('li');
        li.textContent = msg;
        messageList.appendChild(li);
    });

    // Show the modal
    modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('alert-modal');
    modal.classList.add('hidden');
}

// client-side validation before submit: cek stok dan jumlah
document.getElementById('form-penjualan').addEventListener('submit', function (e) {
    const produkSelects = document.querySelectorAll('.produk-select');
    const jumlahInputs = document.querySelectorAll('.jumlah-input');

    const errors = [];
    let hasValidProducts = false;

    // Use a map to track selected products and their total quantities
    const selectedProductsMap = {};

    for (let i = 0; i < produkSelects.length; i++) {
        const sel = produkSelects[i];
        const qty = jumlahInputs[i];
        if (!sel || !qty) continue;

        const selectedProductId = sel.value;
        const quantity = parseInt(qty.value, 10);

        // Hanya validasi baris yang tidak kosong
        if (selectedProductId) {
            hasValidProducts = true;

            // Handle duplicate products
            if (selectedProductsMap[selectedProductId]) {
                selectedProductsMap[selectedProductId].totalQuantity += quantity;
                selectedProductsMap[selectedProductId].rows.push(i + 1);
            } else {
                selectedProductsMap[selectedProductId] = {
                    totalQuantity: quantity,
                    stock: parseInt(sel.options[sel.selectedIndex].dataset.stok ?? 0, 10),
                    productName: sel.options[sel.selectedIndex].text.split('(')[0].trim(),
                    rows: [i + 1]
                };
            }

            if (quantity <= 0 || isNaN(quantity)) {
                errors.push(`Jumlah produk "${selectedProductsMap[selectedProductId].productName}" di baris ke-${i + 1} harus lebih dari 0.`);
            }
        }
    }
    
    // Check total quantities against stock
    for (const productId in selectedProductsMap) {
        const productData = selectedProductsMap[productId];
        if (productData.totalQuantity > productData.stock) {
            errors.push(`Total jumlah produk "${productData.productName}" (${productData.totalQuantity}) melebihi stok yang tersedia (${productData.stock}).`);
        }
    }
    
    // Pastikan setidaknya satu produk yang valid dipilih
    if (!hasValidProducts && produkSelects.length > 0) {
        errors.push('Silakan tambahkan setidaknya satu produk yang valid.');
    }

    if (errors.length > 0) {
        e.preventDefault();
        showModal(errors);
        return;
    }
});
</script>
@endsection
