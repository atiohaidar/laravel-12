@extends('layouts.app')

@section('title', 'API Tokens')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">API Tokens</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTokenModal">
                        Create New Token
                    </button>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('plain_text_token'))
                        <div class="alert alert-warning">
                            <p><strong>Please copy your new token now. You won't be able to see it again!</strong></p>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ session('plain_text_token') }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="copyToken(this)">Copy</button>
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Token Name</th>
                                    <th>Created At</th>
                                    <th>Last Used</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tokens as $token)
                                    <tr>
                                        <td>{{ $token->name }}</td>
                                        <td>{{ $token->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>{{ $token->last_used_at ? $token->last_used_at->format('Y-m-d H:i:s') : 'Never' }}</td>
                                        <td>
                                            <form action="{{ route('tokens.destroy', $token->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No tokens found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Token Modal -->
<div class="modal fade" id="createTokenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tokens.create') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create New Token</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Token Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                            id="name" name="name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Token</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToken(button) {
    const input = button.previousElementSibling;
    input.select();
    document.execCommand('copy');
    button.textContent = 'Copied!';
    setTimeout(() => button.textContent = 'Copy', 2000);
}
</script>
@endpush
@endsection
