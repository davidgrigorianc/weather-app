@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Admin Dashboard</h2>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.dashboard.users.store') }}" class="mb-4 mt-4">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label>Name</label>
                    <input name="name" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Email</label>
                    <input name="email" type="email" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary mt-2">Create User</button>
                </div>
            </div>
        </form>

        <h4>Users List</h4>
        <table class="table table-bordered mt-3">
            <thead><tr><th>#</th><th>Name</th><th>Email</th></tr></thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <form action="{{ route('admin.users.resend', $user) }}" method="POST" style="display:inline">
                            @csrf
                            <button class="btn btn-sm btn-warning">Resend Email</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">No users found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
