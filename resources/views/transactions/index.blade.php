@extends('layouts.app')

@section('title', 'Data Transaksi')
@section('page-title', 'Data Transaksi')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Transaksi</h3>
        <div class="card-tools">
            <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Peminjaman Baru
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Buku</th>
                        <th>Member</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $key => $transaction)
                    <tr>
                        <td>{{ $transactions->firstItem() + $key }}</td>
                        <td>{{ $transaction->kode_transaksi }}</td>
                        <td>{{ $transaction->book->judul }}</td>
                        <td>{{ $transaction->member->nama }}</td>
                        <td>{{ $transaction->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td>{{ $transaction->tanggal_kembali->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $transaction->status == 'dipinjam' ? 'warning' : ($transaction->status == 'dikembalikan' ? 'success' : 'danger') }}">
                                {{ $transaction->status }}
                            </span>
                        </td>
                        <td>Rp {{ number_format($transaction->denda, 0, ',', '.') }}</td>
                        <td>
                            @if($transaction->status == 'dipinjam')
                                <form action="{{ route('transactions.return', $transaction) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Yakin kembalikan?')">
                                        <i class="fas fa-undo"></i> Kembali
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $transactions->links() }}
    </div>
</div>
@endsection