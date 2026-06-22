{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.admin')
@section('title','Manajemen User')
@section('page-title','Manajemen User')
@section('content')
<div class="card">
    <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <div><i class="bi bi-person-gear text-primary me-2"></i>Daftar User</div>
        <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah User
        </a>
    </div>
    <div class="table-responsive">
        <table class="table user-table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">#</th><th>Nama</th><th>Email</th><th>Role</th>
                    <th>Terdaftar</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $u)
                <tr>
                    <td class="ps-3 text-muted">{{ $users->firstItem() + $i }}</td>
                    <td class="fw-semibold">
                        {{ $u->name }}
                        @if($u->id === auth()->id())
                        <span class="badge bg-success ms-1">Anda</span>
                        @endif
                    </td>
                    <td>{{ $u->email }}</td>
                    <td>
                        @foreach($u->roles as $role)
                        <span class="badge {{ $role->name=='super_admin' ? 'bg-danger' : ($role->name=='admin' ? 'bg-primary' : 'bg-secondary') }}">
                            {{ ucfirst(str_replace('_',' ',$role->name)) }}
                        </span>
                        @endforeach
                    </td>
                    <td class="text-muted small">{{ $u->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('users.edit',$u) }}" class="btn btn-xs btn-outline-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy',$u) }}"
                                  onsubmit="return confirm('Hapus user {{ $u->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada user</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer">{{ $users->links() }}</div>
    @endif
</div>
@push('styles')
<style>
    .btn-xs { padding: 3px 8px; font-size: .75rem; }
    .user-table td { word-break: break-word; }
    .user-table .badge { white-space: nowrap; }
</style>
@endpush
@endsection
