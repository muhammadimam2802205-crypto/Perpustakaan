@extends('layouts.app')

@section('title', 'Peminjaman Buku')
@section('page-title', '📖 Peminjaman Buku')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Peminjaman Buku</h3>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('loans.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="book_id" class="form-label">Pilih Buku <span class="text-danger">*</span></label>
                        <select class="form-control @error('book_id') is-invalid @enderror" 
                                id="book_id" name="book_id" required>
                            <option value="">-- Pilih Buku --</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}" 
                                    {{ request('book_id') == $book->id ? 'selected' : '' }}
                                    {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                    {{ $book->kode_buku }} - {{ $book->judul }} 
                                    (Tersedia: {{ $book->available_stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('book_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($books->count() == 0)
                            <small class="text-danger">Tidak ada buku yang tersedia saat ini.</small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Member</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                        <small class="text-muted">Anda meminjam sebagai member</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="borrow_date" class="form-label">Tanggal Pinjam</label>
                        <input type="date" class="form-control" 
                               id="borrow_date" value="{{ date('Y-m-d') }}" disabled>
                        <small class="text-muted">Tanggal pinjam otomatis hari ini</small>
                    </div>

                    <div class="mb-3">
                        <label for="due_date" class="form-label">Batas Pengembalian</label>
                        <input type="date" class="form-control" 
                               id="due_date" value="{{ date('Y-m-d', strtotime('+7 days')) }}" disabled>
                        <small class="text-muted">Batas pengembalian 7 hari dari sekarang</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Peraturan:</strong>
                        <ul class="mb-0 mt-1">
                            <li>Maksimal pinjam 7 hari</li>
                            <li>Denda keterlambatan Rp 1.000/hari</li>
                            <li>Pastikan buku dalam kondisi baik saat dikembalikan</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('loans.index') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary" {{ $books->count() == 0 ? 'disabled' : '' }}>
                    <i class="fas fa-hand-holding"></i> Pinjam Buku
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h5 class="card-title">📋 Buku yang Tersedia</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Tersedia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                    <tr>
                        <td>{{ $book->kode_buku }}</td>
                        <td>{{ $book->judul }}</td>
                        <td>{{ $book->penulis }}</td>
                        <td>
                            <span class="badge badge-success">{{ $book->available_stock }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-danger">
                            <i class="fas fa-exclamation-circle"></i> Tidak ada buku yang tersedia
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto select book from URL parameter
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const bookId = urlParams.get('book_id');
        if (bookId) {
            const select = document.getElementById('book_id');
            for (let option of select.options) {
                if (option.value == bookId) {
                    option.selected = true;
                    break;
                }
            }
        }
    });
</script>
@endpush