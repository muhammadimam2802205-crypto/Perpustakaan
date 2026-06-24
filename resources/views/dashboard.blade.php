@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalBooks ?? 0 }}</h3>
                <p>Total Buku</p>
            </div>
            <div class="icon">
                <i class="fas fa-book"></i>
            </div>
            <a href="{{ route('books.index') }}" class="small-box-footer">Lihat <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalMembers ?? 0 }}</h3>
                <p>Total Member</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('members.index') }}" class="small-box-footer">Lihat <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalTransactions ?? 0 }}</h3>
                <p>Buku Dipinjam</p>
            </div>
            <div class="icon">
                <i class="fas fa-book-open"></i>
            </div>
            <a href="{{ route('loans.index') }}" class="small-box-footer">Lihat <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalOverdue ?? 0 }}</h3>
                <p>Terlambat</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            <a href="{{ route('loans.index') }}" class="small-box-footer">Lihat <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $totalAvailable ?? 0 }}</h3>
                <p>Buku Tersedia</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <a href="{{ route('books.index') }}" class="small-box-footer">Lihat <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>Rp {{ number_format($totalUnpaid ?? 0, 0, ',', '.') }}</h3>
                <p>Total Denda Belum Bayar</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <a href="{{ route('denda.index') }}" class="small-box-footer">Lihat <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Statistik Peminjaman {{ date('Y') }}</h3>
            </div>
            <div class="card-body">
                <canvas id="loanChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📚 Buku Terpopuler</h3>
            </div>
            <div class="card-body">
                @if($popularBooks->count() > 0)
                    <ul class="list-group">
                        @foreach($popularBooks as $book)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $book->judul }}
                                <span class="badge bg-primary rounded-pill">{{ $book->loans_count }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted text-center">Belum ada data peminjaman</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">👥 Member Teraktif</h3>
            </div>
            <div class="card-body">
                @if($activeMembers->count() > 0)
                    <ul class="list-group">
                        @foreach($activeMembers as $member)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $member->name }}
                                <span class="badge bg-success rounded-pill">{{ $member->loans_count }} buku</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted text-center">Belum ada data member aktif</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📊 Ringkasan</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <td>Total Buku</td>
                        <td><strong>{{ $totalBooks ?? 0 }}</strong></td>
                    </tr>
                    <tr>
                        <td>Total Member</td>
                        <td><strong>{{ $totalMembers ?? 0 }}</strong></td>
                    </tr>
                    <tr>
                        <td>Buku Dipinjam</td>
                        <td><strong>{{ $totalTransactions ?? 0 }}</strong></td>
                    </tr>
                    <tr>
                        <td>Buku Tersedia</td>
                        <td><strong>{{ $totalAvailable ?? 0 }}</strong></td>
                    </tr>
                    <tr>
                        <td>Terlambat</td>
                        <td><strong class="text-danger">{{ $totalOverdue ?? 0 }}</strong></td>
                    </tr>
                    <tr>
                        <td>Total Denda</td>
                        <td><strong class="text-danger">Rp {{ number_format($totalUnpaid ?? 0, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('loanChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labels ?? []),
            datasets: [{
                label: 'Peminjaman',
                data: @json($data ?? []),
                backgroundColor: 'rgba(0,123,255,0.5)',
                borderColor: 'rgba(0,123,255,1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection