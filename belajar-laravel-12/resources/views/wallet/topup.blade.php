@extends('layouts.app')

@section('title', 'Top Up Wallet')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold">Top Up Wallet</h2>
            <p class="text-secondary">Add funds to your wallet</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('wallet.topup') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" step="0.01" min="1" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', 100000) }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">This is a Demo</h6>
                                <p class="mb-0">In a real application, this would connect to a payment gateway. For this demo, we'll simulate a successful payment.</p>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Top Up Now</button>
                            <a href="{{ route('wallet.index') }}" class="btn btn-link text-decoration-none">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Top Up Methods</h5>
                    
                    <div class="list-group">
                        <div class="list-group-item d-flex gap-3 align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="bank" checked>
                                <label class="form-check-label" for="bank"></label>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><rect x="3" y="8" width="18" height="12" rx="2"></rect><path d="M7 8V6a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2"></path><path d="M12 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"></path></svg>
                            </div>
                            <div>
                                <span>Bank Transfer</span>
                                <small class="d-block text-secondary">Top up from your bank account</small>
                            </div>
                        </div>
                        
                        <div class="list-group-item d-flex gap-3 align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="credit">
                                <label class="form-check-label" for="credit"></label>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                            </div>
                            <div>
                                <span>Credit/Debit Card</span>
                                <small class="d-block text-secondary">Use your credit or debit card</small>
                            </div>
                        </div>

                        <div class="list-group-item d-flex gap-3 align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="ewallet">
                                <label class="form-check-label" for="ewallet"></label>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M20.42 4.58A5.4 5.4 0 0 0 16.5 3c-1.74 0-3.41.81-4.5 2.09A5.99 5.99 0 0 0 7.5 3C4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5c0-1.43-.5-2.76-1.58-3.92Z"></path></svg>
                            </div>
                            <div>
                                <span>E-Wallet</span>
                                <small class="d-block text-secondary">Top up via supported e-wallets</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection