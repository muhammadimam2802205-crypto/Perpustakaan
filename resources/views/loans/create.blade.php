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
                        <label for="borrow_date" class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('borrow_date') is-invalid @enderror" 
                               id="borrow_date" name="borrow_date" 
                               value="{{ old('borrow_date', date('Y-m-d')) }}" required>
                        @error('borrow_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Pilih tanggal peminjaman</small>
                    </div>

                    <div class="mb-3">
                        <label for="due_date" class="form-label">Batas Pengembalian <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                               id="due_date" name="due_date" 
                               value="{{ old('due_date', date('Y-m-d', strtotime('+7 days'))) }}" required>
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Batas pengembalian bisa lebih singkat atau lebih lama dari 7 hari. Denda akan muncul otomatis hanya setelah tenggat yang Anda pilih terlewati.</small>
                        <div id="dateWarning" class="text-warning mt-1" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <span id="dateWarningText"></span>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Peraturan:</strong>
                        <ul class="mb-0 mt-1">
                            <li>Batas pengembalian bisa disesuaikan, termasuk lebih pendek atau lebih lama dari 7 hari</li>
                            <li>Denda keterlambatan Rp 1.000/hari akan muncul setelah batas tenggat yang dipilih terlewati</li>
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
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const bookId = urlParams.get('book_id');
        if (bookId) {
            const select = document.getElementById('book_id');
            if (select) {
                for (let option of select.options) {
                    if (option.value == bookId) {
                        option.selected = true;
                        break;
                    }
                }
            }
        }

        const borrowDateInput = document.getElementById('borrow_date');
        const dueDateInput = document.getElementById('due_date');
        const dateWarning = document.getElementById('dateWarning');
        const dateWarningText = document.getElementById('dateWarningText');

        if (!borrowDateInput || !dueDateInput || !dateWarning || !dateWarningText) {
            return;
        }

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function parseDateInput(value) {
            if (!value) {
                return null;
            }

            const [year, month, day] = value.split('-').map(Number);
            return new Date(year, month - 1, day);
        }

        function setMinDates() {
            const today = new Date();
            borrowDateInput.setAttribute('min', formatDate(today));

            const borrowDate = parseDateInput(borrowDateInput.value);
            if (borrowDate) {
                const minDueDate = new Date(borrowDate);
                minDueDate.setDate(minDueDate.getDate() + 1);
                dueDateInput.setAttribute('min', formatDate(minDueDate));
            }
        }

        function validateDates() {
            const borrowDate = parseDateInput(borrowDateInput.value);
            const dueDate = parseDateInput(dueDateInput.value);

            if (!borrowDate || !dueDate) {
                return;
            }

            const diffTime = dueDate - borrowDate;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (dueDate < borrowDate) {
                dateWarning.style.display = 'block';
                dateWarningText.textContent = 'Tanggal pengembalian harus setelah tanggal pinjam!';
                dateWarning.className = 'text-danger mt-1';
                dueDateInput.setCustomValidity('Tanggal pengembalian harus setelah tanggal pinjam');
            } else {
                dateWarning.style.display = 'block';
                dateWarningText.textContent = `✅ Batas pengembalian diatur selama ${diffDays} hari dari tanggal pinjam.`;
                dateWarning.className = 'text-success mt-1';
                dueDateInput.setCustomValidity('');
            }
        }

        function setDefaultDueDate() {
            const borrowDate = parseDateInput(borrowDateInput.value);
            if (!borrowDate) {
                return;
            }

            const defaultDueDate = new Date(borrowDate);
            defaultDueDate.setDate(defaultDueDate.getDate() + 7);
            dueDateInput.value = formatDate(defaultDueDate);
            validateDates();
            updateFine();
        }

        function updateFine() {
            const dueDate = parseDateInput(dueDateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const existingWarning = document.getElementById('fineWarning');
            if (existingWarning) {
                existingWarning.remove();
            }

            if (!dueDate) {
                return;
            }

            if (dueDate < today) {
                const diffTime = today - dueDate;
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                const diffHours = Math.floor((diffTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const fine = Math.ceil(diffTime / (1000 * 60 * 60 * 24) * 1000);

                const fineWarning = document.createElement('div');
                fineWarning.className = 'alert alert-danger mt-2';
                fineWarning.id = 'fineWarning';
                fineWarning.innerHTML = `
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>Peringatan:</strong> Tenggat waktu telah lewat ${diffDays} hari ${diffHours} jam.
                    Denda saat ini: Rp ${fine.toLocaleString('id-ID')}
                `;

                dueDateInput.closest('.mb-3').appendChild(fineWarning);
            }
        }

        borrowDateInput.addEventListener('change', setDefaultDueDate);
        dueDateInput.addEventListener('change', function() {
            validateDates();
            updateFine();
        });

        setMinDates();
        setDefaultDueDate();
        validateDates();
        updateFine();
        setInterval(updateFine, 1000);
    });
</script>
@endpush