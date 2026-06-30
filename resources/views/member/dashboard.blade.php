@extends('layouts.app')

@section('title', 'Dashboard Member')

@section('content')
<<<<<<< HEAD
<div class="container-fluid">
    <!-- Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3>Selamat Datang, {{ $user->name }}!</h3>
                    <p class="text-muted">Dashboard peminjaman buku Anda</p>
                </div>
=======
<div class="row">
    <!-- Buku Dipinjam -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalBorrowed }}</h3>
                <p>Buku Dipinjam</p>
            </div>
            <div class="icon">
                <i class="fas fa-book-open"></i>
>>>>>>> d9a3b0e92034a948b299d0a6b30054d3ce569d7b
            </div>
        </div>
    </div>

<<<<<<< HEAD
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalBorrowed ?? 0 }}</h3>
                    <p>Buku Dipinjam</p>
                </div>
                <div class="icon">
                    <i class="fas fa-book-open"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalReturned ?? 0 }}</h3>
                    <p>Buku Dikembalikan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalPending ?? 0 }}</h3>
                    <p>Menunggu Persetujuan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $overdueLoans ?? 0 }}</h3>
                    <p>Terlambat</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Loans -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Aktivitas Peminjaman Terbaru</h3>
                </div>
                <div class="card-body">
                    @if(isset($recentLoans) && $recentLoans->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Judul Buku</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentLoans as $index => $loan)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $loan->book->title ?? 'Tidak Diketahui' }}</td>
                                            <td>{{ $loan->loan_date ? \Carbon\Carbon::parse($loan->loan_date)->format('d/m/Y') : '-' }}</td>
                                            <td>{{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('d/m/Y') : '-' }}</td>
                                            <td>
                                                @if($loan->status == 'borrowed')
                                                    <span class="badge badge-info">Dipinjam</span>
                                                @elseif($loan->status == 'returned')
                                                    <span class="badge badge-success">Dikembalikan</span>
                                                @elseif($loan->status == 'pending')
                                                    <span class="badge badge-warning">Menunggu</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $loan->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Belum ada aktivitas peminjaman.
                        </div>
                    @endif
=======
    <!-- Total Denda -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>Rp {{ number_format($totalFines, 0, ',', '.') }}</h3>
                <p>Total Denda</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>

    <!-- Sudah Dikembalikan (BARU) -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $historyLoans->where('status', 'dikembalikan')->count() }}</h3>
                <p>Sudah Dikembalikan</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <!-- Riwayat Peminjaman -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $historyLoans->count() }}</h3>
                <p>Riwayat Peminjaman</p>
            </div>
            <div class="icon">
                <i class="fas fa-history"></i>
            </div>
        </div>
    </div>
</div>

{{-- Daftar Buku yang Sedang Dipinjam --}}
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📚 Buku yang Sedang Dipinjam</h3>
            </div>
            <div class="card-body">
                @if($activeLoans->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul Buku</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Batas Kembali</th>
                                    <th>Sisa Hari</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeLoans as $key => $loan)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $loan->book->judul }}</td>
                                    <td>{{ $loan->borrow_date->format('d/m/Y') }}</td>
                                    <td>{{ $loan->due_date->format('d/m/Y') }}</td>
                                    <td>
                                        @php
                                            $daysRemaining = $loan->getDaysRemaining();
                                        @endphp
                                        @if($daysRemaining > 0)
                                            <span class="badge badge-success">{{ $daysRemaining }} hari</span>
                                        @elseif($daysRemaining == 0)
                                            <span class="badge badge-warning">Hari ini</span>
                                        @else
                                            <span class="badge badge-danger">{{ abs($daysRemaining) }} hari terlambat</span>
                                            @if($loan->fine_amount > 0)
                                                <br><small class="text-danger">Denda: Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $loan->status == 'dipinjam' ? 'warning' : 'danger' }}">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($loan->status != 'dikembalikan')
                                        <form action="{{ route('loans.return', $loan->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Yakin ingin mengembalikan?')">
                                                <i class="fas fa-undo"></i> Kembali
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Anda belum meminjam buku apapun</p>
                        <a href="{{ route('books.index') }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Cari Buku
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Riwayat Peminjaman --}}
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📜 Riwayat Peminjaman</h3>
            </div>
            <div class="card-body">
                @if($historyLoans->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul Buku</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Denda</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($historyLoans as $key => $loan)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $loan->book->judul }}</td>
                                    <td>{{ $loan->borrow_date->format('d/m/Y') }}</td>
                                    <td>{{ $loan->return_date ? $loan->return_date->format('d/m/Y') : '-' }}</td>
                                    <td>
                                         @if($loan->fine_amount > 0)
                                             <span class="text-danger">Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}</span>
                                             @php
                                                 $hariTelat = $loan->getDaysLate();
                                             @endphp
                                             @if($hariTelat > 0)
                                                 <br><small class="text-muted">({{ $hariTelat }} hari terlambat)</small>
                                             @endif
                                             <br>
                                             @if($loan->payment_status == 'belum_bayar')
                                                 <span class="badge badge-danger">Belum Bayar</span>
                                             @else
                                                 <span class="badge badge-success">Lunas</span>
                                             @endif
                                         @else
                                             <span class="text-success">Rp 0</span>
                                         @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $loan->status == 'dikembalikan' ? 'success' : 'danger' }}">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-3">
                        <p class="text-muted">Belum ada riwayat peminjaman</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Notifikasi Denda --}}
@if($totalFines > 0)
<div class="row">
    <div class="col-md-12">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">⚠️ Tagihan Denda</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Anda memiliki tagihan denda sebesar <strong>Rp {{ number_format($totalFines, 0, ',', '.') }}</strong>
>>>>>>> d9a3b0e92034a948b299d0a6b30054d3ce569d7b
                </div>
            </div>
        </div>
    </div>
</div>
@endsection