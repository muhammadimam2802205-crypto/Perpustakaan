@extends('layouts.app')

@section('title', 'Tambah Buku')
@section('page-title', 'Tambah Buku')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-8">
                    <!-- Judul -->
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Buku <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                               id="judul" name="judul" value="{{ old('judul') }}" 
                               placeholder="Masukkan judul buku" required>
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Penulis -->
                    <div class="mb-3">
                        <label for="penulis" class="form-label">Penulis <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('penulis') is-invalid @enderror" 
                               id="penulis" name="penulis" value="{{ old('penulis') }}" 
                               placeholder="Masukkan nama penulis" required>
                        @error('penulis')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Penerbit -->
                    <div class="mb-3">
                        <label for="penerbit" class="form-label">Penerbit</label>
                        <input type="text" class="form-control @error('penerbit') is-invalid @enderror" 
                               id="penerbit" name="penerbit" value="{{ old('penerbit') }}" 
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
                                       id="tahun_terbit" name="tahun_terbit" value="{{ old('tahun_terbit', date('Y')) }}" 
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
                                       id="stok" name="stok" value="{{ old('stok', 1) }}" 
                                       min="1" placeholder="Jumlah stok" required>
                                @error('stok')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                <option value="{{ $category->id }}" {{ old('kategori_id') == $category->id ? 'selected' : '' }}>
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
                                  placeholder="Masukkan deskripsi buku">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <!-- Cover Upload -->
                    <div class="mb-3">
                        <label for="cover" class="form-label">Cover Buku</label>
                        <input type="file" class="form-control @error('cover') is-invalid @enderror" 
                               id="cover" name="cover" accept="image/*" 
                               onchange="previewCover(this)">
                        @error('cover')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Format: JPG, PNG, GIF, WEBP (Max 2MB)
                        </small>
                    </div>
                    
                    <!-- Preview Cover -->
                    <div id="coverPreviewContainer" class="text-center" style="display: none;">
                        <div class="card">
                            <div class="card-body">
                                <img id="coverPreview" 
                                     src="#" 
                                     alt="Preview Cover" 
                                     class="img-fluid rounded" 
                                     style="max-height: 250px; object-fit: cover;">
                                <br>
                                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeCover()">
                                    <i class="fas fa-times"></i> Hapus
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
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Tombol -->
            <div class="d-flex justify-content-end border-top pt-3">
                <a href="{{ route('books.index') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Buku
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    #coverPreview {
        max-width: 100%;
        max-height: 250px;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .card-body .form-label {
        font-weight: 500;
    }
    
    .text-danger {
        color: #dc3545 !important;
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
            removeCover();
        }
    }
    
    // Hapus preview dan reset input file
    function removeCover() {
        document.getElementById('coverPreviewContainer').style.display = 'none';
        document.getElementById('coverPreview').src = '#';
        document.getElementById('cover').value = '';
        
        // Reset custom file input label (jika menggunakan Bootstrap 5)
        const fileInput = document.getElementById('cover');
        if (fileInput && fileInput.nextElementSibling) {
            const label = fileInput.nextElementSibling;
            if (label.classList.contains('form-label')) {
                label.textContent = 'Pilih file...';
            }
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