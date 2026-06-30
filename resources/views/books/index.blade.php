@extends('layouts.app')

@section('title', 'Data Buku')
@section('page-title', '📚 Data Buku')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Buku</h3>
        <div class="card-tools">
            {{-- Search Form --}}
            <form action="{{ route('books.index') }}" method="GET" class="d-inline">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" name="search" class="form-control float-right" 
                           placeholder="Cari buku..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('books.index') }}" class="btn btn-default">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
            
            {{-- HANYA ADMIN YANG BISA TAMBAH BUKU --}}
            @if(Auth::user() && Auth::user()->isAdmin())
                <a href="{{ route('books.create') }}" class="btn btn-primary btn-sm ml-1">
                    <i class="fas fa-plus"></i> Tambah Buku
                </a>
                <a href="{{ route('books.import') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-cloud-download-alt"></i> Import API
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        {{-- Statistik --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $books->total() }}</h3>
                        <p>Total Buku</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $books->where('available_stock', '>', 0)->count() }}</h3>
                        <p>Buku Tersedia</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $books->where('available_stock', '=', 0)->count() }}</h3>
                        <p>Buku Dipinjam</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $books->sum('available_stock') }}</h3>
                        <p>Total Stok Tersedia</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th width="70">Cover</th>
                        <th width="120">Kode</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Kategori</th>
                        <th width="80" class="text-center">Stok</th>
                        <th width="80" class="text-center">Tersedia</th>
                        <th width="100" class="text-center">Status</th>
                        <th width="200" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $key => $book)
                    <tr>
                        <td class="text-center">{{ $books->firstItem() + $key }}</td>
                        <td class="text-center">
                            @if($book->cover && file_exists(public_path($book->cover)))
                                <img src="{{ asset($book->cover) }}" 
                                     alt="Cover" 
                                     style="max-height: 50px; max-width: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                            @else
                                <div style="width: 40px; height: 50px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; display: inline-block; line-height: 50px;">
                                    <i class="fas fa-book text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td><code>{{ $book->kode_buku }}</code></td>
                        <td>
                            <strong>{{ Str::limit($book->judul, 40) }}</strong>
                            @if(strlen($book->judul) > 40)
                                <span class="badge badge-info" data-toggle="tooltip" title="{{ $book->judul }}">...</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($book->penulis, 25) }}</td>
                        <td>
                            <span class="badge badge-secondary">
                                {{ $book->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="text-center">{{ $book->stok }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $book->available_stock > 0 ? 'success' : 'danger' }}">
                                {{ $book->available_stock }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-{{ $book->available_stock > 0 ? 'success' : 'danger' }} px-3 py-2">
                                <i class="fas fa-{{ $book->available_stock > 0 ? 'check' : 'times' }}"></i>
                                {{ $book->available_stock > 0 ? 'Tersedia' : 'Dipinjam' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('books.show', $book) }}" class="btn btn-info btn-sm" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                {{-- HANYA ADMIN YANG BISA EDIT & HAPUS --}}
                                @if(Auth::user() && Auth::user()->isAdmin())
                                    <a href="{{ route('books.edit', $book) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('books.destroy', $book) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Yakin ingin menghapus buku "{{ $book->judul }}"?')" 
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                {{-- MEMBER BISA PINJAM --}}
                                @if(Auth::user() && Auth::user()->isMember() && $book->available_stock > 0)
                                    <a href="{{ route('loans.create') }}?book_id={{ $book->id }}" 
                                       class="btn btn-success btn-sm" 
                                       title="Pinjam Buku">
                                        <i class="fas fa-hand-holding-heart"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4">
                            <i class="fas fa-book fa-3x d-block text-muted mb-2"></i>
                            <p class="text-muted">Tidak ada data buku</p>
                            @if(Auth::user() && Auth::user()->isAdmin())
                                <a href="{{ route('books.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Buku Pertama
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination dengan info --}}
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <small class="text-muted">
                    Menampilkan {{ $books->firstItem() ?? 0 }} - {{ $books->lastItem() ?? 0 }} 
                    dari {{ $books->total() }} data
                </small>
            </div>
            <div>
                {{ $books->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Small Box Styling */
    .small-box {
        border-radius: 4px;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        display: block;
        margin-bottom: 20px;
        position: relative;
        padding: 15px;
        color: #fff;
    }
    .small-box .inner {
        padding: 10px 0;
    }
    .small-box h3 {
        font-size: 38px;
        font-weight: 700;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
    .small-box p {
        font-size: 14px;
        margin: 0;
    }
    .small-box .icon {
        position: absolute;
        top: 5px;
        right: 10px;
        font-size: 70px;
        color: rgba(0,0,0,.15);
        transition: transform .3s ease-in-out;
    }
    .small-box:hover .icon {
        transform: scale(1.1);
    }
    .small-box.bg-info { background-color: #17a2b8 !important; }
    .small-box.bg-success { background-color: #28a745 !important; }
    .small-box.bg-warning { background-color: #ffc107 !important; color: #212529 !important; }
    .small-box.bg-danger { background-color: #dc3545 !important; }
    
    /* Table Styling */
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
        cursor: pointer;
    }
    .table td, .table th {
        vertical-align: middle;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
    
    /* Badge Styling */
    .badge {
        font-size: 90%;
        padding: 5px 10px;
    }
    .badge-success { background-color: #28a745; color: white; }
    .badge-danger { background-color: #dc3545; color: white; }
    .badge-secondary { background-color: #6c757d; color: white; }
    .badge-info { background-color: #17a2b8; color: white; }
    
    /* Cover Placeholder */
    .cover-placeholder {
        width: 40px;
        height: 50px;
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 12px;
        }
        .btn-group .btn {
            padding: 2px 6px;
            font-size: 10px;
        }
        .small-box h3 {
            font-size: 24px;
        }
        .small-box .icon {
            font-size: 40px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Tooltip initialization
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        
        // Konfirmasi hapus dengan nama buku
        $('form.d-inline').on('submit', function(e) {
            var confirmMessage = $(this).find('button[type="submit"]').attr('onclick');
            if (confirmMessage) {
                e.preventDefault();
                if (confirm('Yakin ingin menghapus buku ini?')) {
                    $(this).unbind('submit').submit();
                }
            }
        });
    });
</script>
@endpush