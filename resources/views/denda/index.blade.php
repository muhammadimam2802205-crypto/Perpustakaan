@extends('layouts.app')

@section('title', 'Daftar Peminjaman')
@section('page-title', '📦 Data Peminjaman')

@section('content')
<div class="card">

    <!-- Card Header -->
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Daftar Peminjaman</h3>

        <div class="ml-auto">
            <a href="{{ route('loans.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Pinjam Buku
            </a>
        </div>
    </div>

    <!-- Card Body -->
    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">

                <thead class="thead-dark">
                    <tr>
                        <th width="60">No</th>

                        @if(Auth::user()->isAdmin())
                            <th>Member</th>
                        @endif

                        <th>Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($loans as $key => $loan)

                    <tr>

                        <td>
                            {{ $loans->firstItem() + $key }}
                        </td>

                        @if(Auth::user()->isAdmin())
                            <td>{{ $loan->user->name }}</td>
                        @endif

                        <td>{{ $loan->book->judul }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($loan->borrow_date)->format('d/m/Y') }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($loan->due_date)->format('d/m/Y') }}
                        </td>

                        <td>

                            @if($loan->status == 'dipinjam')
                                <span class="badge badge-warning">
                                    Dipinjam
                                </span>

                            @elseif($loan->status == 'dikembalikan')
                                <span class="badge badge-success">
                                    Dikembalikan
                                </span>

                            @else
                                <span class="badge badge-danger">
                                    Terlambat
                                </span>
                            @endif

                        </td>

                        <td>

                            @if($loan->fine_amount > 0)

                                <strong>
                                    Rp {{ number_format($loan->fine_amount,0,',','.') }}
                                </strong>

                                @php
                                    $hariTelat = $loan->getDaysLate();
                                @endphp

                                @if($hariTelat > 0)
                                    <br>
                                    <small class="text-danger">
                                        {{ $hariTelat }} hari terlambat
                                    </small>
                                @endif

                            @else

                                -

                            @endif

                        </td>

                        <td>

                            <a href="{{ route('loans.show',$loan->id) }}"
                               class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>

                            @if($loan->status != 'dikembalikan')

                                <form action="{{ route('loans.return',$loan->id) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf

                                    <button
                                        class="btn btn-success btn-sm"
                                        onclick="return confirm('Yakin mengembalikan buku?')">

                                        <i class="fas fa-undo"></i>

                                    </button>

                                </form>

                            @endif


                            @if(
                                $loan->fine_amount > 0 &&
                                ($loan->payment_status == 'belum_bayar' || $loan->payment_status == null) &&
                                Auth::user()->isMember()
                            )

                                <a href="{{ route('denda.payment',$loan->id) }}"
                                   class="btn btn-primary btn-sm">

                                    <i class="fas fa-qrcode"></i>

                                </a>

                            @endif


                            @if(Auth::user()->isAdmin() && $loan->status != 'dipinjam')

                                <form action="{{ route('loans.destroy',$loan->id) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Hapus data?')">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="{{ Auth::user()->isAdmin() ? 8 : 7 }}"
                            class="text-center">

                            Tidak ada data peminjaman.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $loans->links() }}
        </div>

    </div>

</div>
@endsection