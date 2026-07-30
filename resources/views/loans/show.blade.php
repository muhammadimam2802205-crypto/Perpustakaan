@extends('layouts.app')

@section('title', 'Detail Peminjaman')
@section('page-title', '📋 Detail Peminjaman')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>Informasi Buku</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Kode Buku</th>
                        <td>{{ $loan->book->kode_buku }}</td>
                    </tr>
                    <tr>
                        <th>Judul</th>
                        <td>{{ $loan->book->judul }}</td>
                    </tr>
                    <tr>
                        <th>Penulis</th>
                        <td>{{ $loan->book->penulis }}</td>
                    </tr>
                    <tr>
                        <th>Penerbit</th>
                        <td>{{ $loan->book->penerbit ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tahun Terbit</th>
                        <td>{{ $loan->book->tahun_terbit ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Denda</th>
                        <td>
                            @if($loan->fine_amount > 0)
                                <span class="text-danger">Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}</span>
                                @php
                                    $hariTelat = $loan->getDaysLate();
                                @endphp
                                @if($hariTelat > 0)
                                    <span class="text-muted">({{ $hariTelat }} hari terlambat × Rp 1.000)</span>
                                @endif
                                <br>
                                <span class="badge badge-{{ $loan->payment_status == 'belum_bayar' ? 'danger' : ($loan->payment_status == 'lunas' ? 'success' : 'warning') }}">
                                    {{ $loan->payment_status ? ucfirst($loan->payment_status) : 'Belum ada' }}
                                </span>
                            @else
                                <span class="text-success">Rp 0</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                <h5>Informasi Peminjaman</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Kode Transaksi</th>
                        <td>#{{ $loan->id }}</td>
                    </tr>
                    <tr>
                        <th>Member</th>
                        <td>{{ $loan->user->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $loan->user->email }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Pinjam</th>
                        <td>{{ $loan->borrow_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Batas Kembali</th>
                        <td>{{ $loan->due_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Kembali</th>
                        <td>{{ $loan->return_date ? $loan->return_date->format('d/m/Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge badge-{{ $loan->status == 'dipinjam' ? 'warning' : ($loan->status == 'dikembalikan' ? 'success' : 'danger') }}">
                                {{ ucfirst($loan->status) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- ========================================================== --}}
        {{-- ============= FORM UPDATE DENDA (untuk Admin) ============= --}}
        {{-- ========================================================== --}}
        @if(Auth::user()->isAdmin() && $loan->status != 'dikembalikan')
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h5 class="mb-0">💸 Kelola Denda</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('loans.updateFine', $loan->id) }}" method="POST" id="formDenda">
                            @csrf
                            @method('PUT')

                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Status Denda</label>
                                    <div id="statusDendaDisplay">
                                        @if($loan->fine_amount > 0)
                                            <span class="badge badge-danger" id="statusBadge">🔴 Kena Denda</span>
                                        @else
                                            <span class="badge badge-success" id="statusBadge">✅ Tidak Ada Denda</span>
                                        @endif
                                    </div>
                                    <small class="text-muted" id="infoTelat">
                                         @php
                                             $now = \Carbon\Carbon::now()->startOfDay();
                                             $due = \Carbon\Carbon::parse($loan->due_date)->startOfDay();
                                             $hariTelat = $now->greaterThan($due) ? $due->diffInDays($now) : 0;
                                         @endphp
                                         @if($hariTelat > 0 && $loan->status != 'dikembalikan')
                                             ⏰ Terlambat <strong>{{ $hariTelat }}</strong> hari dari batas pengembalian
                                         @endif
                                     </small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Nominal Denda (Rp)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number"
                                               name="fine_amount"
                                               id="fineAmountInput"
                                               class="form-control @error('fine_amount') is-invalid @enderror"
                                               value="{{ old('fine_amount', $loan->fine_amount) }}"
                                               min="0"
                                               step="1000"
                                               placeholder="0">
                                    </div>
                                    @error('fine_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        ✏️ Denda bisa diubah manual oleh petugas
                                    </small>
                                </div>

                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save"></i> Update Denda
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary w-100 mt-2" id="btnAutoDenda">
                                        <i class="fas fa-calculator"></i> Hitung Otomatis
                                    </button>
                                </div>
                            </div>

                            {{-- Info denda otomatis --}}
                            <div class="mt-3 p-2 bg-light rounded">
                                <small>
                                    <i class="fas fa-info-circle text-info"></i>
                                    <strong>Aturan Denda:</strong> Setelah melewati batas pengembalian, denda akan muncul otomatis sebesar Rp 1.000/hari.
                                    Klik <strong>"Hitung Otomatis"</strong> untuk menghitung ulang.
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ========================================================== --}}
        {{-- =================== TOMBOL AKSI =========================== --}}
        {{-- ========================================================== --}}
        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('loans.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            @if($loan->status != 'dikembalikan' && ($loan->user_id == Auth::id() || Auth::user()->isAdmin()))
                <form action="{{ route('loans.return', $loan->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Yakin ingin mengembalikan buku ini?')">
                        <i class="fas fa-undo"></i> Kembalikan Buku
                    </button>
                </form>
            @endif

            @if($loan->fine_amount > 0 && $loan->payment_status == 'belum_bayar' && Auth::user()->isMember())
                <a href="{{ route('denda.payment', $loan->id) }}" class="btn btn-primary ms-2">
                    <i class="fas fa-money-bill-wave"></i> Bayar Denda
                </a>
            @endif
        </div>
    </div>
</div>
@endsection

{{-- ========================================================== --}}
{{-- =================== JAVASCRIPT ============================ --}}
{{-- ========================================================== --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fineInput = document.getElementById('fineAmountInput');
        const statusBadge = document.getElementById('statusBadge');
        const infoTelat = document.getElementById('infoTelat');
        const btnAuto = document.getElementById('btnAutoDenda');
        const formDenda = document.getElementById('formDenda');

        // Data dari server
        const borrowDate = '{{ $loan->borrow_date->format('Y-m-d') }}';
        const dueDate = '{{ $loan->due_date->format('Y-m-d') }}';
        const today = '{{ now()->format('Y-m-d') }}';

        // ============================================================
        // FUNGSI HITUNG DENDA OTOMATIS
        // ============================================================
        function hitungDendaOtomatis() {
            const tglPinjam = new Date(borrowDate + 'T00:00:00');
            const tglKembali = new Date(dueDate + 'T00:00:00');
            const sekarang = new Date(today + 'T00:00:00');

            // Selisih hari dari batas kembali ke sekarang
            const diffTime = sekarang - tglKembali;
            const hariTelat = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            // Update info telat
            if (hariTelat > 0) {
                infoTelat.innerHTML = `⏰ Terlambat <strong>${hariTelat}</strong> hari dari batas pengembalian`;
                infoTelat.style.display = 'block';
            } else {
                infoTelat.innerHTML = '';
                infoTelat.style.display = 'none';
            }

            // Hitung denda otomatis: terlambat = Rp 1.000/hari
            let denda = 0;
            if (hariTelat > 0) {
                const hariKenaDenda = hariTelat < 1 ? 1 : hariTelat;
                denda = hariKenaDenda * 1000;
                statusBadge.textContent = '🔴 Kena Denda';
                statusBadge.className = 'badge badge-danger';
            } else {
                denda = 0;
                statusBadge.textContent = '✅ Tidak Ada Denda';
                statusBadge.className = 'badge badge-success';
            }

            // Isi input denda
            fineInput.value = denda;
            updateStatusBadgeFromInput();
        }

        // ============================================================
        // UPDATE STATUS BADGE DARI INPUT
        // ============================================================
        function updateStatusBadgeFromInput() {
            const val = parseInt(fineInput.value) || 0;
            if (val > 0) {
                statusBadge.textContent = '🔴 Kena Denda (Manual)';
                statusBadge.className = 'badge badge-danger';
            } else {
                statusBadge.textContent = '✅ Tidak Ada Denda';
                statusBadge.className = 'badge badge-success';
            }
        }

        // ============================================================
        // EVENT LISTENER
        // ============================================================

        // Saat user ubah manual
        fineInput.addEventListener('input', function() {
            if (this.value < 0) this.value = 0;
            updateStatusBadgeFromInput();
        });

        // Tombol "Hitung Otomatis"
        btnAuto.addEventListener('click', function() {
            hitungDendaOtomatis();
            // Tampilkan notifikasi
            const toast = document.createElement('div');
            toast.className = 'alert alert-info alert-dismissible fade show position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <i class="fas fa-check-circle"></i> Denda dihitung ulang!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        });

        // ============================================================
        // VALIDASI FORM SEBELUM SUBMIT
        // ============================================================
        formDenda.addEventListener('submit', function(e) {
            if (fineInput.value === '' || parseInt(fineInput.value) < 0) {
                e.preventDefault();
                alert('Nominal denda tidak boleh kosong atau negatif!');
                fineInput.focus();
            }
        });

        // ============================================================
        // JALANKAN OTOMATIS SAAT LOAD
        // ============================================================
        // Cek jika fine_amount masih 0 dan status masih dipinjam → auto hitung
        const currentFine = parseInt('{{ $loan->fine_amount }}');
        if (currentFine === 0 && '{{ $loan->status }}' !== 'dikembalikan') {
            hitungDendaOtomatis();
        } else {
            updateStatusBadgeFromInput();
        }
    });
</script>
@endpush