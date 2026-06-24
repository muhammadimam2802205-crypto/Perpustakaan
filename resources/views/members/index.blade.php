@extends('layouts.app')

@section('title', 'Data Member')
@section('page-title', 'Data Member')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Member</h3>
        <div class="card-tools">
            <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Member
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
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $key => $member)
                    <tr>
                        <td>{{ $members->firstItem() + $key }}</td>
                        <td>{{ $member->kode_member }}</td>
                        <td>{{ $member->nama }}</td>
                        <td>{{ $member->email }}</td>
                        <td>{{ $member->no_telepon }}</td>
                        <td>
                            <span class="badge badge-{{ $member->status == 'aktif' ? 'success' : 'danger' }}">
                                {{ $member->status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('members.edit', $member) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('members.destroy', $member) }}" method="POST" class="d-inline">
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
        {{ $members->links() }}
    </div>
</div>
@endsection