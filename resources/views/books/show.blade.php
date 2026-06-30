@extends('layouts.app')

@section('title', 'Detail Buku: ' . $book->judul)
@section('page-title', 'Detail Buku')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <!-- Cover -->
            <div class="col-md-4">
                <div class="text-center">
                    @if($book->cover && file_exists(public_path($book->cover)))
                        <img src="{{ asset($book->cover) }}" 
                             alt="Cover {{ $book->judul }}" 
                             class="img-fluid rounded" 
                             style="max-height: 450px; object-fit: contain; border: 1px solid #ddd; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    @else
                        <div class="py-5 bg-light rounded" style="border: 1px solid #ddd; min-height: 300px;">
                            <i class="fas fa-book fa-5x text-muted"></i>
                            <p class="text-muted mt-3">Tidak ada cover</p>
                        </div>
                    @endif
                    
                    {{-- Status Badge --}}
                    <div class="mt-3">
                        <span class="badge badge-{{ $book->available_stock > 0 ? 'success' : 'danger' }} px-4 py-2" style="font-size: 16px;">
                            <i class="fas fa-{{ $book->available_stock > 0 ? 'check-circle' : 'times-circle' }}"></i>
                            {{ $book->available_stock > 0 ? 'Tersedia' : 'Dipinjam' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Detail -->
            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-start">
                    <h2 class="mb-0">{{ $book->judul }}</h2>
                    <small class="text-muted">#{{ $book->kode_buku }}</small>
                </div>
                
                <p class="text-muted mt-1">
                    <i class="fas fa-user-edit"></i> oleh {{ $book->penulis }}
                </p>
                
                <hr>
                
                <div class="row">
                    {{-- Info Kiri --}}
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="120"><strong>Kode Buku</strong></td>
                                <td><code>{{ $book->kode_buku }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Penulis</strong></td>
                                <td>{{ $book->penulis }}</td>
                            </tr>
                            <tr>
                                <td><strong>Penerbit</strong></td>
                                <td>{{ $book->penerbit ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tahun Terbit</strong></td>
                                <td>{{ $book->tahun_terbit ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kategori</strong></td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ $book->category->name ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    {{-- Info Kanan --}}
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="120"><strong>Total Stok</strong></td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $book->stok }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tersedia</strong></td>
                                <td>
                                    <span class="badge badge-{{ $book->available_stock > 0 ? 'success' : 'danger' }}">
                                        {{ $book->available_stock }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Dipinjam</strong></td>
                                <td>
                                    <span class="badge badge-warning">
                                        {{ $book->stok - $book->available_stock }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Dibuat</strong></td>
                                <td>{{ $book->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Diupdate</strong></td>
                                <td>{{ $book->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                {{-- Deskripsi --}}
                @if($book->deskripsi)
                    <div class="mt-2">
                        <h6><i class="fas fa-align-left"></i> Deskripsi</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $book->deskripsi }}
                        </div>
                    </div>
                @endif
                
                {{-- Tombol Aksi --}}
                <div class="mt-4">
                    <a href="{{ route('books.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    
                    @if(Auth::user() && Auth::user()->isAdmin())
                        <a href="{{ route('books.edit', $book) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('books.destroy', $book) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Yakin ingin menghapus buku "{{ $book->judul }}"?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    @endif
                    
                    @if(Auth::user() && Auth::user()->isMember() && $book->available_stock > 0)
                        <a href="{{ route('loans.create') }}?book_id={{ $book->id }}" class="btn btn-success">
                            <i class="fas fa-hand-holding-heart"></i> Pinjam Buku
                        </a>
                    @endif
                    
                    @if(Auth::user() && Auth::user()->isMember() && $book->available_stock == 0)
                        <button class="btn btn-secondary" disabled>
                            <i class="fas fa-times"></i> Stok Habis
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Riwayat Peminjaman (Untuk Admin) --}}
@if(Auth::user() && Auth::user()->isAdmin() && $book->loans->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-history"></i> Riwayat Peminjaman
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Member</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th>Denda</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($book->loans->take(10) as $index => $loan)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $loan->member->name ?? '-' }}</td>
                        <td>{{ $loan->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td>{{ $loan->tanggal_kembali ? $loan->tanggal_kembali->format('d/m/Y') : '-' }}</td>
                        <td>
                            @if($loan->status == 'dipinjam')
                                <span class="badge badge-warning">Dipinjam</span>
                            @elseif($loan->status == 'dikembalikan')
                                <span class="badge badge-success">Dikembalikan</span>
                            @else
                                <span class="badge badge-danger">Terlambat</span>
                            @endif
                        </td>
                        <td>
                            @if($loan->denda > 0)
                                <span class="text-danger">Rp {{ number_format($loan->denda, 0, ',', '.') }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($book->loans->count() > 10)
            <p class="text-muted">Menampilkan 10 dari {{ $book->loans->count() }} riwayat</p>
        @endif
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    .table-borderless td {
        padding: 5px 0;
    }
    .table-borderless td:first-child {
        padding-right: 10px;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .badge {
        font-size: 90%;
        padding: 5px 12px;
    }
    .badge-success { background-color: #28a745; color: white; }
    .badge-danger { background-color: #dc3545; color: white; }
    .badge-warning { background-color: #ffc107; color: #212529; }
    .badge-info { background-color: #17a2b8; color: white; }
    .badge-secondary { background-color: #6c757d; color: white; }
    
    .card-body img {
        transition: transform 0.3s ease;
    }
    .card-body img:hover {
        transform: scale(1.02);
    }
    
    @media (max-width: 768px) {
        .col-md-4 {
            margin-bottom: 20px;
        }
        .table-borderless td {
            display: block;
            padding: 3px 0;
        }
        .table-borderless td:first-child {
            width: 100%;
            font-weight: bold;
        }
        .table-borderless td:last-child {
            padding-left: 15px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Tooltip initialization
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush