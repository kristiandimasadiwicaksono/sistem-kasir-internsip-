@extends('layouts.main')

@section('title', 'Tambah Supplier')

@section('content')

<div class="container mx-auto mt-10 px-4 md:px-8">
<div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
<h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Tambah Supplier Baru</h1>

    <!-- Supplier Form -->
    <form id="supplier-form" action="{{ route('suppliers.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Supplier Name -->
        <div>
            <label for="nama_supplier" class="block text-sm font-medium text-gray-700 mb-2">Nama Supplier</label>
            <input type="text" id="nama_supplier" name="nama_supplier" required class="py-3 px-4 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
        </div>

        <!-- Contact Person -->
        <div>
            <label for="nama_kontak" class="block text-sm font-medium text-gray-700 mb-2">Nama Kontak</label>
            <input type="text" id="nama_kontak" name="nama_kontak" required class="py-3 px-4 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
        </div>

        <!-- Phone Number -->
        <div>
            <label for="telepon" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
            <input type="tel" id="telepon" name="telepon" required class="py-3 px-4 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
        </div>

        <!-- Address -->
        <div>
            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
            <textarea id="alamat" name="alamat" rows="3" class="py-3 px-4 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email (Opsional)</label>
            <input type="email" id="email" name="email" class="py-3 px-4 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
        </div>

        <div class="flex justify-end">
            <button type="submit" id="submit-btn" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none shadow-md">
                <span id="submit-text">Simpan Supplier</span>
                <div id="loading-spinner" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
            </button>
        </div>
    </form>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
const supplierForm = document.getElementById('supplier-form');
const submitBtn = document.getElementById('submit-btn');
const submitText = document.getElementById('submit-text');
const loadingSpinner = document.getElementById('loading-spinner');
const messageModal = document.getElementById('message-modal');
const messageTitle = document.getElementById('message-title');
const messageBody = document.getElementById('message-body');
const closeMessageBtn = document.getElementById('close-message-btn');

    // Function to show custom message modal
    function showMessage(title, body) {
        messageTitle.textContent = title;
        messageBody.textContent = body;
        messageModal.classList.remove(&#39;hidden&#39;);
        messageModal.classList.add(&#39;flex&#39;);
    }

    // Handle form submission with loading state
    supplierForm.addEventListener(&#39;submit&#39;, (e) =&gt; {
        submitBtn.disabled = true;
        submitText.textContent = &#39;Menyimpan...&#39;;
        loadingSpinner.classList.remove(&#39;hidden&#39;);

        // In a real application, you would perform an AJAX POST request here
        // For this example, we&#39;ll just simulate a successful submission.
        // e.preventDefault();
        // setTimeout(() =&gt; {
        //     submitBtn.disabled = false;
        //     submitText.textContent = &#39;Simpan Supplier&#39;;
        //     loadingSpinner.classList.add(&#39;hidden&#39;);
        //     showMessage(&#39;Berhasil!&#39;, &#39;Data supplier berhasil disimpan.&#39;);
        //     supplierForm.reset();
        // }, 2000);
    });

    closeMessageBtn.addEventListener(&#39;click&#39;, () =&gt; {
        messageModal.classList.add(&#39;hidden&#39;);
        messageModal.classList.remove(&#39;flex&#39;);
    });
});

</script>

@endsection