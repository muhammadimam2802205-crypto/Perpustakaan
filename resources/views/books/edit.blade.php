@extends('layouts.app')

@section('title', 'Edit Buku')
@section('page-title', 'Edit Buku: ' . $book->judul)

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-8">
                    <!-- Judul -->
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Buku <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                               id="judul" name="judul" value="{{ old('judul', $book->judul) }}" 
                               placeholder="Masukkan judul buku" required>
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Penulis -->
                    <div class="mb-3">
                        <label for="penulis" class="form-label">Penulis <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('penulis') is-invalid @enderror" 
                               id="penulis" name="penulis" value="{{ old('penulis', $book->penulis) }}" 
                               placeholder="Masukkan nama penulis" required>
                        @error('penulis')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Penerbit -->
                    <div class="mb-3">
                        <label for="penerbit" class="form-label">Penerbit</label>
                        <input type="text" class="form-control @error('penerbit') is-invalid @enderror" 
                               id="penerbit" name="penerbit" value="{{ old('penerbit', $book->penerbit) }}" 
                               placeholder="Masukkan nama penerbit">
                        @error('penerbit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <!-- Tahun Terbit -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                                <input type="number" class="form-control @error('tahun_terbit') is-invalid @enderror" 
                                       id="tahun_terbit" name="tahun_terbit" value="{{ old('tahun_terbit', $book->tahun_terbit) }}" 
                                       min="1900" max="{{ date('Y') }}" placeholder="Tahun">
                                @error('tahun_terbit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Stok -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stok" class="form-label">Stok <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('stok') is-invalid @enderror" 
                                       id="stok" name="stok" value="{{ old('stok', $book->stok) }}" 
                                       min="0" placeholder="Jumlah stok" required>
                                @error('stok')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Stok tersedia: <strong>{{ $book->available_stock }}</strong> dari {{ $book->stok }}
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kategori -->
                    <div class="mb-3">
                        <label for="kategori_id" class="form-label">Kategori</label>
                        <select class="form-select @error('kategori_id') is-invalid @enderror" 
                                id="kategori_id" name="kategori_id">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ old('kategori_id', $book->kategori_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                  id="deskripsi" name="deskripsi" rows="4" 
                                  placeholder="Masukkan deskripsi buku">{{ old('deskripsi', $book->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <!-- Current Cover -->
                    <div class="mb-3">
                        <label class="form-label">Cover Saat Ini</label>
                        <div class="text-center p-3 border rounded bg-light">
                            @if($book->cover && file_exists(public_path($book->cover)))
                                <img src="{{ asset($book->cover) }}" 
                                     alt="Cover {{ $book->judul }}" 
                                     class="img-fluid rounded" 
                                     style="max-height: 200px; object-fit: contain; border: 1px solid #ddd;">
                                <br>
                                <button type="button" class="btn btn-sm btn-danger mt-2" 
                                        onclick="confirmRemoveCover({{ $book->id }})">
                                    <i class="fas fa-trash"></i> Hapus Cover
                                </button>
                            @else
                                <div class="py-4">
                                    <i class="fas fa-book fa-3x text-muted"></i>
                                    <p class="text-muted mt-2 mb-0">Belum ada cover</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Upload Cover Baru -->
                    <div class="mb-3">
                        <label for="cover" class="form-label">Ganti Cover</label>
                        <input type="file" class="form-control @error('cover') is-invalid @enderror" 
                               id="cover" name="cover" accept="image/*" 
                               onchange="previewCover(this)">
                        @error('cover')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Format: JPG, PNG, GIF, WEBP (Max 2MB)
                            <br>
                            <i class="fas fa-info-circle"></i> 
                            Kosongkan jika tidak ingin mengganti cover
                        </small>
                    </div>
                    
                    <!-- Preview Cover Baru -->
                    <div id="coverPreviewContainer" class="text-center" style="display: none;">
                        <div class="card">
                            <div class="card-body">
                                <label class="form-label">Preview Cover Baru</label>
                                <img id="coverPreview" 
                                     src="#" 
                                     alt="Preview Cover" 
                                     class="img-fluid rounded" 
                                     style="max-height: 200px; object-fit: contain; border: 1px solid #ddd;">
                                <br>
                                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removePreview()">
                                    <i class="fas fa-times"></i> Hapus Preview
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tips -->
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-lightbulb"></i>
                        <strong>Tips Cover:</strong>
                        <ul class="mb-0 mt-1">
                            <li>Rasio gambar 3:4 untuk tampilan terbaik</li>
                            <li>Ukuran disarankan 300x400 pixel</li>
                            <li>Pastikan gambar jelas dan tidak buram</li>
                            <li>Gunakan cover dengan resolusi tinggi</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Tombol -->
            <div class="d-flex justify-content-end border-top pt-3 mt-3">
                <a href="{{ route('books.index') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Buku
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Form Hapus Cover (tersembunyi) -->
<form id="removeCoverForm" action="{{ route('books.remove-cover', $book) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
    #coverPreview {
        max-width: 100%;
        max-height: 200px;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .text-danger {
        color: #dc3545 !important;
        font-weight: 600;
    }
    
    .card-body .form-label {
        font-weight: 500;
    }
    
    .border-top {
        border-top: 2px solid #e9ecef !important;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Preview cover saat memilih file
    function previewCover(input) {
        const previewContainer = document.getElementById('coverPreviewContainer');
        const previewImage = document.getElementById('coverPreview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            removePreview();
        }
    }
    
    // Hapus preview
    function removePreview() {
        document.getElementById('coverPreviewContainer').style.display = 'none';
        document.getElementById('coverPreview').src = '#';
        document.getElementById('cover').value = '';
    }
    
    // Konfirmasi hapus cover
    function confirmRemoveCover(bookId) {
        if (confirm('Yakin ingin menghapus cover buku ini?\n\nAksi ini tidak dapat dibatalkan!')) {
            document.getElementById('removeCoverForm').submit();
        }
    }
    
    // Auto preview jika ada error (old value)
    document.addEventListener('DOMContentLoaded', function() {
        const coverInput = document.getElementById('cover');
        if (coverInput && coverInput.files && coverInput.files.length > 0) {
            previewCover(coverInput);
        }
    });
</script>
@endpush