@extends('layouts.app')

@section('title', 'Dashboard Member')

@section('content')
<div class="container-fluid">
    <!-- Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3>Selamat Datang, {{ $user->name }}!</h3>
                    <p class="text-muted">Dashboard peminjaman buku Anda</p>
                </div>
            </div>
        </div>
    </div>

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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection