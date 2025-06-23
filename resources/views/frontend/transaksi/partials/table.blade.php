<?php

// resources/views/frontend/transaksi/partials/table.blade.php
?>
@if (count($daftarBarang) > 0)
    <div class="table-responsive mb-3">
        <table class="table table-bordered table-hover align-middle shadow-sm">
            <thead class="table-primary text-center">
                <tr>
                    <th>Nama Barang</th>
                    <th>Kode</th>
                    <th>Gambar</th>
                    <th>Kategori</th>
                    <th>Stok Tersedia</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($daftarBarang as $barang)
                    <tr>
                        <td class="text-center">
                            <input type="text" name="items[{{ $barang['kode'] }}][barang_nama]" class="form-control" value="{{ $barang['nama'] }}">
                            <input type="hidden" name="items[{{ $barang['kode'] }}][barang_kode]" value="{{ $barang['kode'] }}">
                        </td>
                        <td class="text-center">{{ $barang['kode'] }}</td>
                        <td class="text-center">
                            @if (!empty($barang['gambar']))
                                <a href="{{ asset($barang['gambar']) }}" data-lightbox="gambar-{{ $barang['kode'] }}">
                                    <img src="{{ asset($barang['gambar']) }}" alt="{{ $barang['nama'] }}" style="width: 60px; height: 60px; object-fit: cover;">
                                </a>
                            @else
                                <span class="text-muted">Tidak ada gambar</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $barang['kategoribarang'] }}</td>
                        <td class="text-center">{{ $barang['stok_tersedia'] }}</td>
                        <td class="text-center">
                            <input type="number" name="items[{{ $barang['kode'] }}][quantity]" class="form-control" value="{{ $barang['jumlah'] }}" min="1" required>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('transaksi.remove-item', $transactionId) }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="kode" value="{{ $barang['kode'] }}">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center text-muted mb-3">Tidak ada barang dalam daftar.</div>
@endif
