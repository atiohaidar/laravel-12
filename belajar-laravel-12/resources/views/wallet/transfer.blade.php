@extends('layouts.app')

@section('title', 'Transfer Money')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold">Transfer Money</h2>
            <p class="text-secondary">Send money to other users</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('wallet.transfer') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="recipient_id" class="form-label">Select Recipient</label>
                            <select class="form-select @error('recipient_id') is-invalid @enderror" id="recipient_id" name="recipient_id" required>
                                <option value="" selected disabled>Choose a recipient</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('recipient_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('recipient_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" step="0.01" min="1" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="What's this transfer for?">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Send Money</button>
                            <a href="{{ route('wallet.index') }}" class="btn btn-link text-decoration-none">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Transfer Info</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2 d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2 text-primary"><path d="M12 22V8"></path><path d="m5 12 7-4 7 4"></path><path d="M5 16l7-4 7 4"></path><path d="M5 4h14"></path></svg>
                            Instant transfer to other users
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2 text-primary"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            Safe and secure transactions
                        </li>
                        <li class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2 text-primary"><path d="M5 22h14"></path><path d="M5 2h14"></path><rect x="1" y="6" width="22" height="12" rx="2"></rect></svg>
                            No transfer fees
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 bg-light">
                <div class="card-body p-4">
                    <h6 class="card-title">Recent Recipients</h6>
                    <p class="text-secondary small mb-0">Your transfer history will appear here once you've made transfers.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection