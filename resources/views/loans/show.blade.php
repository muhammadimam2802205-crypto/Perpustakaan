@extends('layouts.app')

@section('title', 'Detail Peminjaman')
@section('page-title', '📋 Detail Peminjaman')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
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
                                <br>
                                <span class="badge badge-{{ $loan->payment_status == 'belum_bayar' ? 'danger' : 'success' }}">
                                    {{ ucfirst($loan->payment_status) }}
                                </span>
                            @else
                                <span class="text-success">Rp 0</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

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
                    <i class="fas fa-qrcode"></i> Bayar Denda
                </a>
            @endif
        </div>
    </div>
</div>
@endsection