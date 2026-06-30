@extends('layouts.app')

@section('title', 'Data Denda')
@section('page-title', '💰 Data Denda')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Denda</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Member</th>
                        <th>Buku</th>
                        <th>Status</th>
                        <th>Jumlah Denda</th>
                        <th>Status Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $key => $loan)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $loan->user->name }}</td>
                        <td>{{ $loan->book->judul }}</td>
                        <td>
                            <span class="badge badge-{{ $loan->status == 'terlambat' ? 'danger' : 'warning' }}">
                                {{ $loan->status == 'terlambat' ? 'Terlambat' : 'Aktif' }}
                            </span>
                        </td>
                        <td class="text-danger">
                            Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}
                            @php
                                $hariTelat = $loan->getDaysLate();
                            @endphp
                            @if($hariTelat > 0)
                                <br><small class="text-muted">({{ $hariTelat }} hari terlambat)</small>
                            @endif
                        </td>
                        <td>
                            @if($loan->payment_status == 'belum_bayar')
                                <span class="badge badge-danger">Belum Bayar</span>
                            @elseif($loan->payment_status == 'lunas')
                                <span class="badge badge-success">Lunas</span>
                            @else
                                <span class="badge badge-secondary">Belum Ada</span>
                            @endif
                        </td>
                        <td>
                            @if($loan->payment_status == 'belum_bayar')
                                @if(Auth::user()->isMember())
                                    <a href="{{ route('denda.payment', $loan->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-qrcode"></i> Bayar
                                    </a>
                                @endif
                                @if(Auth::user()->isAdmin())
                                    <form action="{{ route('denda.confirm', $loan->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Konfirmasi pembayaran?')">
                                            <i class="fas fa-check"></i> Konfirmasi
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data denda</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection