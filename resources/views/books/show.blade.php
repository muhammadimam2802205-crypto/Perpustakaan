@extends('layouts.app')

@section('title', 'Detail Buku')
@section('page-title', 'Detail Buku')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                @if($book->cover)
                    <img src="{{ asset($book->cover) }}" alt="{{ $book->judul }}" class="img-fluid">
                @else
                    <div class="text-center py-5 bg-light">
                        <i class="fas fa-book fa-5x text-muted"></i>
                        <p class="text-muted">Tidak ada cover</p>
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <h2>{{ $book->judul }}</h2>
                <hr>
                <dl class="row">
                    <dt class="col-sm-3">Kode Buku</dt>
                    <dd class="col-sm-9">{{ $book->kode_buku }}</dd>
                    
                    <dt class="col-sm-3">Penulis</dt>
                    <dd class="col-sm-9">{{ $book->penulis }}</dd>
                    
                    <dt class="col-sm-3">Penerbit</dt>
                    <dd class="col-sm-9">{{ $book->penerbit ?? '-' }}</dd>
                    
                    <dt class="col-sm-3">Tahun Terbit</dt>
                    <dd class="col-sm-9">{{ $book->tahun_terbit ?? '-' }}</dd>
                    
                    <dt class="col-sm-3">Kategori</dt>
                    <dd class="col-sm-9">{{ $book->category->name ?? '-' }}</dd>
                    
                    <dt class="col-sm-3">Stok</dt>
                    <dd class="col-sm-9">{{ $book->stok }}</dd>
                    
                    <dt class="col-sm-3">Tersedia</dt>
                    <dd class="col-sm-9">{{ $book->available_stock }}</dd>
                    
                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        <span class="badge badge-{{ $book->available_stock > 0 ? 'success' : 'danger' }}">
                            {{ $book->available_stock > 0 ? 'Tersedia' : 'Dipinjam' }}
                        </span>
                    </dd>
                    
                    <dt class="col-sm-3">Deskripsi</dt>
                    <dd class="col-sm-9">{{ $book->deskripsi ?? '-' }}</dd>
                </dl>
                
                <div class="mt-3">
                    <a href="{{ route('books.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('books.edit', $book) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    @endif
                    @if($book->available_stock > 0 && Auth::user()->isMember())
                        <a href="{{ route('loans.create') }}?book_id={{ $book->id }}" class="btn btn-primary">
                            <i class="fas fa-hand-holding"></i> Pinjam
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection