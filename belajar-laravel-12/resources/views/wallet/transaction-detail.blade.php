@extends('layouts.app')

@section('title', 'Transaction Detail')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex align-items-center">
                <a href="{{ route('wallet.transactions') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <div>
                    <h2 class="fw-bold mb-0">Transaction Detail</h2>
                    <p class="text-secondary mb-0">{{ $transaction->reference_id }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="
                            @if($transaction->type == 'topup')
                                bg-success
                            @elseif($transaction->type == 'transfer')
                                bg-danger
                            @elseif($transaction->type == 'receive')
                                bg-primary
                            @endif
                            text-white rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3
                        " style="width: 60px; height: 60px;">
                            @if($transaction->type == 'topup')
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
                            @elseif($transaction->type == 'transfer')
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
                            @elseif($transaction->type == 'receive')
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            @endif
                        </div>
                        <h3 class="
                            @if($transaction->type == 'topup' || $transaction->type == 'receive') 
                                text-success
                            @elseif($transaction->type == 'transfer')
                                text-danger
                            @endif
                            fw-bold display-5
                        ">
                            @if($transaction->type == 'topup' || $transaction->type == 'receive')
                                +
                            @elseif($transaction->type == 'transfer')
                                -
                            @endif
                            Rp {{ number_format($transaction->amount, 2) }}
                        </h3>
                        <h5 class="text-secondary mb-0">
                            @if($transaction->type == 'topup')
                                Top Up
                            @elseif($transaction->type == 'transfer')
                                Transfer
                            @elseif($transaction->type == 'receive')
                                Received
                            @endif
                        </h5>
                    </div>

                    <hr>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <p class="text-secondary small mb-1">Transaction Date</p>
                                <p class="fw-medium mb-0">{{ $transaction->created_at->format('d M Y, H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <p class="text-secondary small mb-1">Status</p>
                                <span class="badge bg-success">Completed</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <p class="text-secondary small mb-1">Balance After Transaction</p>
                                <p class="fw-medium mb-0">Rp {{ number_format($transaction->balance_after, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <p class="text-secondary small mb-1">Reference ID</p>
                                <p class="fw-medium mb-0">{{ $transaction->reference_id }}</p>
                            </div>
                        </div>
                        
                        @if($transaction->type == 'transfer')
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <p class="text-secondary small mb-1">Recipient</p>
                                    <p class="fw-medium mb-0">{{ $transaction->recipient->name ?? 'Unknown' }}</p>
                                </div>
                            </div>
                        @elseif($transaction->type == 'receive')
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <p class="text-secondary small mb-1">Sender</p>
                                    <p class="fw-medium mb-0">{{ $transaction->sender->name ?? 'Unknown' }}</p>
                                </div>
                            </div>
                        @endif
                        
                        @if($transaction->description)
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <p class="text-secondary small mb-1">Description</p>
                                    <p class="fw-medium mb-0">{{ $transaction->description }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Transaction Summary</h5>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Type</span>
                        <span class="fw-medium">{{ ucfirst($transaction->type) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Amount</span>
                        <span class="fw-medium">Rp {{ number_format($transaction->amount, 2) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Fee</span>
                        <span class="fw-medium">Rp 0.00</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary">Total</span>
                        <span class="fw-bold">Rp {{ number_format($transaction->amount, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <a href="{{ route('wallet.transactions') }}" class="btn btn-outline-primary mb-2">Back to Transactions</a>
                <a href="{{ route('wallet.index') }}" class="d-block text-decoration-none">Back to Wallet</a>
            </div>
        </div>
    </div>
</div>
@endsection