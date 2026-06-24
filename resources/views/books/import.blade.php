@extends('layouts.app')

@section('title', 'Import Buku dari API')
@section('page-title', '📥 Import Buku dari API')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cari Buku dari API Eksternal</h3>
    </div>
    <div class="card-body">
        <!-- Form Pencarian -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" id="searchQuery" class="form-control" placeholder="Cari judul buku...">
                    <button class="btn btn-primary" onclick="searchBooks()">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
                <small class="text-muted">Masukkan judul buku untuk mencari dari database eksternal</small>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" id="isbnQuery" class="form-control" placeholder="Cari berdasarkan ISBN...">
                    <button class="btn btn-info" onclick="searchByIsbn()">
                        <i class="fas fa-barcode"></i> Cari ISBN
                    </button>
                </div>
            </div>
        </div>

        <!-- Hasil Pencarian -->
        <div id="searchResults" style="display: none;">
            <h4>Hasil Pencarian</h4>
            <div id="resultsList" class="row"></div>
        </div>

        <!-- Loading -->
        <div id="loading" style="display: none;" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Mencari data buku...</p>
        </div>

        <!-- Form Import -->
        <div id="importForm" style="display: none;" class="mt-4">
            <div class="card card-info">
                <div class="card-header">
                    <h5 class="card-title">Import Buku</h5>
                </div>
                <div class="card-body">
                    <form id="importBookForm" action="{{ route('books.import.store') }}" method="POST">
                        @csrf
                        <input type="hidden" id="api_id" name="api_id">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Judul Buku <span class="text-danger">*</span></label>
                                    <input type="text" id="import_judul" name="judul" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Penulis <span class="text-danger">*</span></label>
                                    <input type="text" id="import_penulis" name="penulis" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Penerbit</label>
                                    <input type="text" id="import_penerbit" name="penerbit" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tahun Terbit</label>
                                    <input type="number" id="import_tahun" name="tahun_terbit" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kategori</label>
                                    <select name="kategori_id" id="import_kategori" class="form-control">
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories ?? [] as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Stok <span class="text-danger">*</span></label>
                                    <input type="number" id="import_stok" name="stok" class="form-control" value="1" required>
                                </div>
                                <input type="hidden" id="import_cover" name="cover_url">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea id="import_deskripsi" name="deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" onclick="cancelImport()">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-cloud-download-alt"></i> Import Buku
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi pencarian buku
function searchBooks() {
    const query = document.getElementById('searchQuery').value;
    if (!query) {
        alert('Masukkan judul buku yang ingin dicari');
        return;
    }

    showLoading();
    
    fetch(`/books/search-api?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            hideLoading();
            displayResults(data);
        })
        .catch(error => {
            hideLoading();
            alert('Error: ' + error.message);
        });
}

function searchByIsbn() {
    const isbn = document.getElementById('isbnQuery').value;
    if (!isbn) {
        alert('Masukkan ISBN buku');
        return;
    }

    showLoading();
    
    fetch(`/books/search-api?isbn=${encodeURIComponent(isbn)}`)
        .then(response => response.json())
        .then(data => {
            hideLoading();
            displayResults(data);
        })
        .catch(error => {
            hideLoading();
            alert('Error: ' + error.message);
        });
}

function showLoading() {
    document.getElementById('loading').style.display = 'block';
    document.getElementById('searchResults').style.display = 'none';
}

function hideLoading() {
    document.getElementById('loading').style.display = 'none';
}

function displayResults(data) {
    const resultsDiv = document.getElementById('resultsList');
    const searchResults = document.getElementById('searchResults');
    
    if (!data || !data.items || data.items.length === 0) {
        resultsDiv.innerHTML = '<div class="col-12"><div class="alert alert-warning">Tidak ditemukan hasil</div></div>';
        searchResults.style.display = 'block';
        return;
    }

    let html = '';
    data.items.forEach((item, index) => {
        const title = item.judul || item.title || 'Judul tidak tersedia';
        const authors = item.penulis || item.authors || '-';
        const publisher = item.penerbit || item.publisher || '-';
        const year = item.tahun_terbit || item.publishedDate || '';
        const description = item.deskripsi || item.description || '';
        const cover = item.cover || item.imageLinks?.thumbnail || '';

        html += `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    ${cover ? `<img src="${cover}" class="card-img-top" style="height:200px;object-fit:cover;">` : ''}
                    <div class="card-body">
                        <h5 class="card-title">${title}</h5>
                        <p class="card-text">
                            <strong>Penulis:</strong> ${Array.isArray(authors) ? authors.join(', ') : authors}<br>
                            <strong>Penerbit:</strong> ${publisher}<br>
                            <strong>Tahun:</strong> ${year || '-'}
                        </p>
                        <button class="btn btn-primary btn-sm" onclick="showImportForm('${item.id}', `${escapeJs(title)}`, `${escapeJs(Array.isArray(authors) ? authors.join(', ') : authors)}`, `${escapeJs(publisher)}`, `${escapeJs(year)}`, `${escapeJs(description)}`, `${escapeJs(cover)}`)">
                            <i class="fas fa-cloud-download-alt"></i> Import
                        </button>
                    </div>
                </div>
            </div>
        `;
    });

    resultsDiv.innerHTML = html;
    searchResults.style.display = 'block';
}

function showImportForm(id, judul, penulis, penerbit, tahun, deskripsi, cover) {
    document.getElementById('api_id').value = id;
    document.getElementById('import_judul').value = judul;
    document.getElementById('import_penulis').value = penulis;
    document.getElementById('import_penerbit').value = penerbit;
    document.getElementById('import_tahun').value = tahun ? parseInt(tahun) : '';
    document.getElementById('import_deskripsi').value = deskripsi || '';
    document.getElementById('import_cover').value = cover || '';
    
    document.getElementById('importForm').style.display = 'block';
    document.getElementById('importForm').scrollIntoView({ behavior: 'smooth' });
}

// helper to escape string for inlined JS template
function escapeJs(str) {
    if (!str) return '';
    return str.replace(/`/g, '\\`').replace(/\$/g, '\\$').replace(/\n/g, ' ');
}

function cancelImport() {
    document.getElementById('importForm').style.display = 'none';
    document.getElementById('searchResults').style.display = 'none';
    document.getElementById('searchQuery').value = '';
    document.getElementById('isbnQuery').value = '';
}
</script>
@endsection