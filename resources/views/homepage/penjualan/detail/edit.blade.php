@extends('layouts.main')

@section('title', 'Edit Item Penjualan #' . ($penjualan->id ?? ''))

@section('content')
<div class="max-w-3xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="text-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800">
                Edit Item Penjualan
            </h2>
            <p class="text-sm text-gray-600">
                Untuk transaksi <strong>#{{ $penjualan->id }}</strong>
            </p>
        </div>

        {{-- Tampilkan error umum --}}
        @if ($errors->any())
            <div class="mb-4 text-sm text-red-600">
                <ul>
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('penjualan.detail.update', [$penjualan->id, $detail->id]) }}" id="form-edit-item">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="id_produk" class="block text-sm font-medium text-gray-700">Produk</label>
                    <select id="id_produk" name="id_produk"
                        class="mt-1 block w-full border border-gray-200 rounded-lg py-2 px-3 focus:border-blue-500 focus:ring-blue-500 produk-select" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($produk as $item)
                            @php
                                $isSelected = (old('id_produk', $detail->id_produk) == $item->id);
                                $disabled = (($item->stok ?? 0) <= 0) && !$isSelected;
                            @endphp
                            <option value="{{ $item->id }}"
                                data-stok="{{ $item->stok ?? 0 }}"
                                {{ $isSelected ? 'selected' : '' }}
                                {{ $disabled ? 'disabled' : '' }}>
                                {{ $item->nama_produk }} (stok: {{ $item->stok ?? 0 }}){{ $disabled ? ' — STOK HABIS' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_produk')
                        <div class="mt-1 text-xs text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah</label>
                    <input type="number" id="jumlah" name="jumlah" min="1"
                        value="{{ old('jumlah', $detail->jumlah) }}"
                        class="mt-1 block w-full border border-gray-200 rounded-lg py-2 px-3 focus:border-blue-500 focus:ring-blue-500 jumlah-input" required>
                    @error('jumlah')
                        <div class="mt-1 text-xs text-red-500">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-x-2">
                <a href="{{ route('penjualan.detail', $penjualan->id) }}"
                   class="py-2 px-3 rounded-lg border border-gray-200 bg-white text-gray-800 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                   class="py-2 px-3 rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('form-edit-item').addEventListener('submit', function (e) {
    const sel = document.getElementById('id_produk');
    const qtyInput = document.getElementById('jumlah');

    if (!sel || !qtyInput) return;

    const selectedOption = sel.options[sel.selectedIndex];
    const stok = parseInt(selectedOption?.dataset?.stok ?? 0, 10);
    const jumlah = parseInt(qtyInput.value ?? 0, 10);
    const jumlahLama = {{ $detail->jumlah ?? 0 }}; // jumlah sebelumnya

    let error = null;

    if (!sel.value) {
        error = 'Pilih produk.';
    } else if (isNaN(jumlah) || jumlah < 1) {
        error = 'Masukkan jumlah yang valid (>= 1).';
    } else if (jumlah > jumlahLama) {
        error = 'Jumlah baru tidak boleh lebih besar dari jumlah sebelumnya (' + jumlahLama + ').';
    }

    if (error) {
        e.preventDefault();
        alert(error);
        return false;
    }
});
</script>
@endpush

@endsection
