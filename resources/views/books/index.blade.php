@extends('layouts.app')

@section('title', 'Data Buku')
@section('page-title', '📚 Data Buku')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Buku</h3>
        <div class="card-tools">
            {{-- HANYA ADMIN YANG BISA TAMBAH BUKU --}}
            @if(Auth::user() && Auth::user()->isAdmin())
                <a href="{{ route('books.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Buku
                </a>
                <a href="{{ route('books.import') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-cloud-download-alt"></i> Import API
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Tersedia</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $key => $book)
                    <tr>
                        <td>{{ $books->firstItem() + $key }}</td>
                        <td>{{ $book->kode_buku }}</td>
                        <td>{{ $book->judul }}</td>
                        <td>{{ $book->penulis }}</td>
                        <td>{{ $book->category->name ?? '-' }}</td>
                        <td>{{ $book->stok }}</td>
                        <td>{{ $book->available_stock }}</td>
                        <td>
                            <span class="badge badge-{{ $book->available_stock > 0 ? 'success' : 'danger' }}">
                                {{ $book->available_stock > 0 ? 'Tersedia' : 'Dipinjam' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('books.show', $book) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            {{-- HANYA ADMIN YANG BISA EDIT & HAPUS --}}
                            @if(Auth::user() && Auth::user()->isAdmin())
                                <a href="{{ route('books.edit', $book) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('books.destroy', $book) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                            {{-- MEMBER BISA PINJAM --}}
                            @if(Auth::user() && Auth::user()->isMember() && $book->available_stock > 0)
                                <a href="{{ route('loans.create') }}?book_id={{ $book->id }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-hand-holding"></i> Pinjam
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data buku</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $books->links() }}
    </div>
</div>
@endsection