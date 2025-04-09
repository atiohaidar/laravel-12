@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex align-items-center">
                <a href="{{ url('/users') }}" class="btn btn-sm btn-outline-secondary me-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Back to Users
                </a>
                <h2 class="fw-bold mb-0">User Profile: {{ $user->name }}</h2>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-light p-3 d-inline-flex mb-3" style="width: 100px; height: 100px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#5c6ac4" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3 class="h4 mb-1">{{ $user->name }}</h3>
                    <p class="text-secondary mb-3">{{ $user->email }}</p>
                    
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ url('/users/' . $user->id . '/edit') }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ url('/users/' . $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header">User Information</div>
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-4 text-secondary">ID</div>
                        <div class="col-md-8 fw-medium">{{ $user->id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-secondary">Name</div>
                        <div class="col-md-8 fw-medium">{{ $user->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-secondary">Email</div>
                        <div class="col-md-8 fw-medium">{{ $user->email }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 text-secondary">Telegram ID</div>
                        <div class="col-md-8 fw-medium">
                            @if($user->telegram_id)
                                {{ $user->telegram_id }}
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 text-secondary">Registered</div>
                        <div class="col-md-8 fw-medium">{{ $user->created_at->format('F d, Y - h:i A') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-secondary">Last Updated</div>
                        <div class="col-md-8 fw-medium">{{ $user->updated_at->format('F d, Y - h:i A') }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 text-secondary">Email Verified</div>
                        <div class="col-md-8 fw-medium">
                            @if($user->email_verified_at)
                                <span class="text-success">Verified on {{ $user->email_verified_at->format('F d, Y - h:i A') }}</span>
                            @else
                                <span class="text-danger">Not verified</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">API Tokens</div>
                <div class="card-body p-0">
                    @if($user->tokens->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Token Name</th>
                                        <th>Created</th>
                                        <th>Last Used</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->tokens as $token)
                                    <tr>
                                        <td>{{ $token->name }}</td>
                                        <td>{{ $token->created_at->format('M d, Y') }}</td>
                                        <td>{{ $token->last_used_at ? $token->last_used_at->format('M d, Y') : 'Never' }}</td>
                                        <td>
                                            <form action="{{ url('/tokens/' . $token->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Revoke</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center">
                            <p class="text-secondary mb-0">No API tokens found for this user.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Send Email</div>
                <div class="card-body">
                    <form action="{{ url('/users/' . $user->id . '/send-email') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="message" class="form-label">Message Content</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                id="message" name="message" rows="4" required></textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Send Email</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection