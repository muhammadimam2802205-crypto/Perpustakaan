@extends('layouts.app')

@section('title', 'Pembayaran Denda')
@section('page-title', '💳 Pembayaran Denda')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Bayar Denda</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th>Member</th>
                        <td>{{ $loan->user->name }}</td>
                    </tr>
                    <tr>
                        <th>Buku</th>
                        <td>{{ $loan->book->judul }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Kembali</th>
                        <td>{{ $loan->return_date ? $loan->return_date->format('d/m/Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Denda</th>
                        <td class="text-danger">Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Status Pembayaran</th>
                        <td>
                            <span class="badge badge-{{ $loan->payment_status == 'belum_bayar' ? 'danger' : 'success' }}">
                                {{ ucfirst(str_replace('_', ' ', $loan->payment_status)) }}
                            </span>
                        </td>
                    </tr>
                </table>

                <div class="mt-3">
                    <a href="{{ route('denda.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    @if(Auth::user()->isAdmin() && $loan->payment_status == 'belum_bayar')
                        <form action="{{ route('denda.confirm', $loan->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Konfirmasi Pembayaran
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="col-md-6 text-center">
                <h5>Scan QR Code</h5>
                <p class="text-muted">Gunakan aplikasi pembayaran untuk membayar denda.</p>
                @if(!empty($qrCodeUrl))
                    <img src="{{ $qrCodeUrl }}" alt="QR Code Pembayaran Denda" class="img-fluid rounded shadow-sm" style="max-width: 280px;">
                    <p class="mt-3"><strong>Total:</strong> Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}</p>
                @else
                    <div class="alert alert-warning">Tidak dapat menghasilkan QR code saat ini.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
