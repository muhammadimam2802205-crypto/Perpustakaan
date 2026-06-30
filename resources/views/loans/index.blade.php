@extends('layouts.app')

@section('title', 'Daftar Peminjaman')
@section('page-title', '📦 Data Peminjaman')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Daftar Peminjaman</h3>
        <a href="{{ route('loans.create') }}" class="btn btn-primary btn-sm ms-auto">
            <i class="fas fa-plus"></i> Pinjam Buku
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        @if(Auth::user()->isAdmin())
                            <th>Member</th>
                        @endif
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Jatuh Tempo</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $key => $loan)
                        <tr>
                            <td>{{ $loans->firstItem() + $key }}</td>
                            @if(Auth::user()->isAdmin())
                                <td>{{ $loan->user->name }}</td>
                            @endif
                            <td>{{ $loan->book->judul }}</td>
                            <td>{{ $loan->borrow_date->format('d/m/Y') }}</td>
                            <td>{{ $loan->due_date->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge badge-{{ $loan->status == 'dipinjam' ? 'warning' : ($loan->status == 'dikembalikan' ? 'success' : 'danger') }}">
                                    {{ ucfirst($loan->status) }}
                                </span>
                            </td>
                            <td>
                                @if($loan->fine_amount > 0)
                                    Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}
                                    @php
                                        $hariTelat = $loan->getDaysLate();
                                    @endphp
                                    @if($hariTelat > 0)
                                        <br><small class="text-danger">({{ $hariTelat }} hari terlambat)</small>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($loan->status != 'dikembalikan')
                                    <form action="{{ route('loans.return', $loan->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Yakin ingin mengembalikan buku ini?')">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($loan->fine_amount > 0 && $loan->payment_status == 'belum_bayar' && Auth::user()->isMember())
                                    <a href="{{ route('denda.payment', $loan->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-qrcode"></i>
                                    </a>
                                @endif
                                @if(Auth::user()->isAdmin() && $loan->status != 'dipinjam')
                                    <form action="{{ route('loans.destroy', $loan->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data peminjaman?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->isAdmin() ? 8 : 7 }}" class="text-center">Tidak ada data peminjaman</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $loans->links() }}
    </div>
</div>
@endsection
