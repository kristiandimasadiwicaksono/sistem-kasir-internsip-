@extends('layouts.main')

@section('title', 'Daftar Supplier')

@section('content')

<div class="container mx-auto mt-10 px-4 md:px-8">
<div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
<div class="flex flex-col md:flex-row justify-between items-center mb-6">
<h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Daftar Supplier</h1>
<a href="{{ route('suppliers.create') }}" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none shadow-md">
Tambah Supplier
</a>
</div>

    <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
        <table class="min-w-full bg-white divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Nama Supplier</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Kontak</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Alamat</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($suppliers as $supplier)
                <tr class="hover:bg-gray-100 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{ $supplier->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $supplier->nama }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $supplier->kontak }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $supplier->alamat }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="inline-block" onsubmit="handleDelete(event)">
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
                    <td colspan="7" class="text-center py-4 text-gray-500">Tidak ada data supplier.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>

<!-- Custom Confirmation Modal -->

<div id="confirmation-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
<div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm">
<h3 class="text-xl font-bold mb-4 text-gray-800">Konfirmasi Hapus</h3>
<p class="text-gray-700 mb-6">Apakah Anda yakin ingin menghapus supplier ini? Tindakan ini tidak bisa dibatalkan.</p>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
const confirmationModal = document.getElementById('confirmation-modal');
const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
const cancelBtn = document.getElementById('cancel-btn');
let formToDelete = null;

    window.handleDelete = (event) =&gt; {
        event.preventDefault();
        formToDelete = event.target.closest(&#39;form&#39;);
        confirmationModal.classList.remove(&#39;hidden&#39;);
        confirmationModal.classList.add(&#39;flex&#39;);
    };

    confirmDeleteBtn.addEventListener(&#39;click&#39;, () =&gt; {
        if (formToDelete) {
            formToDelete.submit();
        }
        confirmationModal.classList.add(&#39;hidden&#39;);
        confirmationModal.classList.remove(&#39;flex&#39;);
        formToDelete = null;
    });

    cancelBtn.addEventListener(&#39;click&#39;, () =&gt; {
        confirmationModal.classList.add(&#39;hidden&#39;);
        confirmationModal.classList.remove(&#39;flex&#39;);
        formToDelete = null;
    });
});

</script>

@endsection