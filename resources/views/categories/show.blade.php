@extends('layouts.app')

@section('title', 'Detail Kategori')
@section('page-title', '📂 Detail Kategori')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Kategori</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th>Nama</th>
                        <td>{{ $category->name }}</td>
                    </tr>
                    <tr>
                        <th>Slug</th>
                        <td>{{ $category->slug }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $category->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah Buku</th>
                        <td>{{ $category->books_count ?? $category->books->count() }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="mt-3">
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
