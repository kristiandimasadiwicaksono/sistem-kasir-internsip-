@extends('layouts.main')

@section('title', 'Import Produk')

@section('content')

<main class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-gray-100 font-sans">
    <div class="max-w-2xl mx-auto bg-white border border-gray-200 rounded-3xl shadow-2xl p-5">

        <div class="text-center mb-8">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-2 leading-tight">Import Data Produk</h2>
            <p class="text-base text-gray-500">Unggah file Excel atau CSV Anda untuk memperbarui data produk.</p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        <!-- Client-side error message container -->
        <div id="file-error" class="hidden mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-xl"></div>

        <!-- Format Guide -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl shadow-inner">
            <h3 class="font-bold text-blue-800 mb-2">Panduan Format File</h3>
            <p class="text-sm text-blue-700 mb-3">File Excel/CSV Anda harus memiliki dua kolom berikut:</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div class="bg-white p-2 rounded-lg border border-gray-200 shadow-sm">
                    <span class="font-medium text-blue-800 block">nama_produk</span>
                    <p class="text-xs text-gray-600">Contoh: Buku Tulis Sinar Dunia</p>
                </div>
                <div class="bg-white p-2 rounded-lg border border-gray-200 shadow-sm">
                    <span class="font-medium text-blue-800 block">harga</span>
                    <p class="text-xs text-gray-600">Contoh: 5000</p>
                </div>
            </div>
            <p class="text-xs text-blue-600 mt-2">* Stok akan otomatis diatur ke 0 untuk produk baru.</p>
        </div>
        
        <form action="{{ route('produk.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="flex flex-col items-center justify-center w-full">
                <!-- Hidden file input -->
                <input type="file" name="file" id="file" class="hidden" accept=".xlsx,.xls,.csv" required>

                <!-- Drop zone styled with a label -->
                <label for="file" id="drop-zone" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer relative overflow-hidden group">
                    <div id="drop-content" class="flex flex-col items-center justify-center text-center transition-all duration-300 group-hover:scale-105 group-hover:text-green-600">
                        <svg id="upload-icon" class="w-12 h-12 text-gray-400 mb-2 transform transition-all duration-300 group-hover:scale-110 group-hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <span id="drop-text" class="text-gray-600 font-semibold text-sm">
                            Klik untuk upload atau <span class="font-extrabold text-green-600">drag & drop</span> file<br>
                            <span class="text-xs text-gray-500 font-normal">Format: Excel atau CSV (Maksimal 2MB)</span>
                        </span>
                    </div>
                    
                    <!-- File selected indicator -->
                    <div id="file-selected" class="hidden items-center justify-center flex-col transition-all duration-300">
                        <svg class="w-14 h-14 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span id="file-name" class="text-green-700 font-semibold text-base max-w-full truncate px-4"></span>
                        <button type="button" id="cancel-file" 
                            class="mt-3 px-4 py-1 text-sm bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition">
                            ❌ Cancel
                        </button>
                    </div>
                </label>

                @error('file')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('produk.index') }}" class="px-6 py-2 rounded-full bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-colors duration-200 text-sm">
                    Batal
                </a>
                <button type="submit" id="submit-btn" class="px-6 py-2 rounded-full bg-green-600 text-white font-semibold shadow-lg hover:bg-green-700 hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    Import Data
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ asset('template_produk.xlsx') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200 font-medium text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m5 8h.01M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Download Template Excel
            </a>
        </div>
    </div>
</main>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file');
    const dropText = document.getElementById('drop-text');
    const dropContent = document.getElementById('drop-content');
    const fileSelected = document.getElementById('file-selected');
    const fileName = document.getElementById('file-name');
    const fileError = document.getElementById('file-error');
    const cancelBtn = document.getElementById('cancel-file');

    if (!dropZone) return;

    // 🛑 Cegah browser buka file saat drag & drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        window.addEventListener(eventName, e => {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    // highlight saat dragover
    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        e.stopPropagation();
        highlight();
    });

    dropZone.addEventListener('dragleave', e => {
        e.preventDefault();
        e.stopPropagation();
        unhighlight();
    });

    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        e.stopPropagation();

        unhighlight();

        // Ambil file
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleDrop(files[0]); // cuma ambil 1 file pertama
        }
    });

    function highlight() {
        dropZone.classList.add('border-green-500', 'bg-green-50');
        dropZone.classList.remove('border-gray-300');
        dropText.innerHTML = '<span class="font-semibold text-green-600">📁 Lepaskan file di sini</span>';
        hideErrorMessage();
    }

    function unhighlight() {
        dropZone.classList.remove('border-green-500', 'bg-green-50');
        dropZone.classList.add('border-gray-300');
        if (!fileInput.files.length) {
            dropText.innerHTML =
                '<span class="font-semibold">Klik untuk upload</span> atau drag & drop file<br>' +
                '<span class="text-sm text-gray-500">Excel atau CSV (Max 2MB)</span>';
        }
    }

    function showErrorMessage(message) {
        fileError.textContent = message;
        fileError.classList.remove('hidden');
    }

    function hideErrorMessage() {
        fileError.classList.add('hidden');
    }

    function handleDrop(file) {
        const name = file.name.toLowerCase();
        const valid = name.endsWith('.xlsx') || name.endsWith('.xls') || name.endsWith('.csv');

        if (valid) {
            if (file.size <= 2097152) { // 2MB
                const dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files; // update input hidden
                updateFileDisplay(file.name);
            } else {
                showErrorMessage('File terlalu besar! Maksimal 2MB.');
                resetFileDisplay();
            }
        } else {
            showErrorMessage('Format file tidak didukung! Gunakan .xlsx, .xls, atau .csv');
            resetFileDisplay();
        }
    }

    function updateFileDisplay(name) {
        dropContent.classList.add('hidden');
        fileSelected.classList.remove('hidden');
        fileSelected.classList.add('flex');
        fileName.textContent = name;
        dropZone.classList.remove('border-gray-300');
        dropZone.classList.add('border-green-500', 'bg-green-50');
    }

    function resetFileDisplay() {
        dropContent.classList.remove('hidden');
        fileSelected.classList.add('hidden');
        fileSelected.classList.remove('flex');
        dropZone.classList.remove('border-green-500', 'bg-green-50');
        dropZone.classList.add('border-gray-300');
        dropText.innerHTML =
            '<span class="font-semibold">Klik untuk upload</span> atau drag & drop file<br>' +
            '<span class="text-sm text-gray-500">Excel atau CSV (Max 2MB)</span>';
    }

    fileInput.addEventListener('change', function () {
        if (this.files.length > 0) {
            hideErrorMessage();
            updateFileDisplay(this.files[0].name);
        } else {
            resetFileDisplay();
        }
    });

    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            fileInput.value = "";
            resetFileDisplay();
            hideErrorMessage();
        })
    }
});
</script>

