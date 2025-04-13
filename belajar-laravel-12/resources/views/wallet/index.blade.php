@extends('layouts.app')

@section('title', 'My Wallet')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold">My Wallet</h2>
            <p class="text-secondary">Manage your money and transactions</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Current Balance</h5>
                    <h2 class="display-4 fw-bold text-primary mb-4">Rp {{ number_format($balance, 2) }}</h2>
                    <div class="d-flex gap-2">
                        <a href="{{ route('wallet.topup.form') }}" class="btn btn-primary">Top Up</a>
                        <a href="{{ route('wallet.transfer.form') }}" class="btn btn-outline-primary">Transfer</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">Recent Transactions</h5>
                        <a href="{{ route('wallet.transactions') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                    </div>
                    
                    @if(count($transactions) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($transactions as $transaction)
                                <a href="{{ route('wallet.transaction.detail', $transaction->id) }}" class="list-group-item list-group-item-action px-0 py-3 d-flex justify-content-between align-items-center border-bottom">
                                    <div>
                                        <p class="mb-0 fw-medium">
                                            @if($transaction->type == 'topup')
                                                Top Up
                                            @elseif($transaction->type == 'transfer')
                                                Transfer to {{ $transaction->recipient->name ?? 'Unknown' }}
                                            @elseif($transaction->type == 'receive')
                                                Received from {{ $transaction->sender->name ?? 'Unknown' }}
                                            @endif
                                        </p>
                                        <small class="text-secondary">{{ $transaction->created_at->format('d M Y, H:i') }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="
                                            @if($transaction->type == 'topup' || $transaction->type == 'receive') 
                                                text-success
                                            @elseif($transaction->type == 'transfer')
                                                text-danger
                                            @endif
                                            fw-medium
                                        ">
                                            @if($transaction->type == 'topup' || $transaction->type == 'receive')
                                                +
                                            @elseif($transaction->type == 'transfer')
                                                -
                                            @endif
                                            Rp {{ number_format($transaction->amount, 2) }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-secondary mb-0">No transactions yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection