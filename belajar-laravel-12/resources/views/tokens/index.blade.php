@extends('layouts.app')

@section('title', 'API Tokens')

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

            @if (session('plain_text_token'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <p><strong>Your new token:</strong></p>
                    <p class="text-break">{{ session('plain_text_token') }}</p>
                    <p class="mb-0"><small class="text-muted">Please save this token somewhere safe. You won't be able to see it again!</small></p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">API Tokens</div>

                <div class="card-body">
                    <form action="{{ route('tokens.create') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="name" class="form-control" placeholder="Token Name" required>
                            <button class="btn btn-primary" type="submit">Create Token</button>
                        </div>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </form>

                    @if($tokens->count() > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Created</th>
                                    <th>Last Used</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tokens as $token)
                                    <tr>
                                        <td>{{ $token->name }}</td>
                                        <td>{{ $token->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $token->last_used_at ? $token->last_used_at->format('Y-m-d H:i') : 'Never' }}</td>
                                        <td>
                                            <form action="{{ route('tokens.destroy', $token->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to revoke this token?')">Revoke</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center">No tokens found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
