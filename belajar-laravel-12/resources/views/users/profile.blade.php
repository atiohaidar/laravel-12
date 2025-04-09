@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">My Profile</div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Name</label>
                        <p>{{ $user->name }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <p>{{ $user->email }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Member Since</label>
                        <p>{{ $user->created_at->format('F j, Y') }}</p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">Edit Profile</a>
                        <a href="{{ route('tokens.index') }}" class="btn btn-outline-secondary">Manage API Tokens</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
