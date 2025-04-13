@extends('layouts.app')

@section('title', 'Transaction History')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold">Transaction History</h2>
            <p class="text-secondary">View your complete transaction history</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            @if(count($transactions) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Details</th>
                                <th>Amount</th>
                                <th>Balance</th>
                                <th>Reference</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($transaction->type == 'topup')
                                                bg-success
                                            @elseif($transaction->type == 'transfer')
                                                bg-danger
                                            @elseif($transaction->type == 'receive')
                                                bg-primary
                                            @endif
                                        ">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($transaction->type == 'topup')
                                            Wallet Top Up
                                        @elseif($transaction->type == 'transfer')
                                            To: {{ $transaction->recipient->name ?? 'Unknown' }}
                                        @elseif($transaction->type == 'receive')
                                            From: {{ $transaction->sender->name ?? 'Unknown' }}
                                        @endif
                                    </td>
                                    <td class="
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
                                    </td>
                                    <td>Rp {{ number_format($transaction->balance_after, 2) }}</td>
                                    <td>
                                        <span class="small text-secondary">{{ $transaction->reference_id }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('wallet.transaction.detail', $transaction->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $transactions->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-secondary mb-3"><path d="M20.9 11.6c.1-.6.2-1.1.2-1.7 0-3.1-3.1-5.7-6.9-5.9h-.3c-2.5-2.9-5.6-3.1-7.3-3.1C4.4 1 2 3.4 2 6.5c0 .7.1 1.3.3 1.9C1.1 10 0 12 0 14.1c0 3.5 2.9 6.4 6.5 6.4.5 0 1-.3 1.2-.8.2-.5 0-1.1-.4-1.4-.1-.1-.3-.2-.5-.2-2.2 0-4-1.8-4-4 0-1.6.9-2.9 2.2-3.6.3-.2.5-.5.5-.9 0-.3-.1-.7-.3-.9-.3-.3-.5-.7-.7-1.1-.1-.5-.2-.9-.2-1.4 0-2 1.6-3.6 3.6-3.6 1.5 0 3.7.2 5.4 2.4.2.3.5.4.9.4h1.4c3 0 5.5 1.9 5.5 4.3 0 .7-.2 1.3-.5 1.9-.2.5-.3.8-.1 1.1.2.2.5.4.7.6.6.5 1.1 1.2 1.3 1.9.1.2.4.3.6.3.1 0 .3 0 .4-.1.4-.2.7-.6.6-1-.4-.9-1-1.9-1.8-2.5z"></path><path d="M12.8 14c-1.1 0-2.1.9-2.1 2.1 0 1.1.9 2.1 2.1 2.1 1.1 0 2.1-.9 2.1-2.1 0-1.1-1-2.1-2.1-2.1zm0 3.4c-.7 0-1.2-.6-1.2-1.2 0-.7.6-1.2 1.2-1.2.7 0 1.2.6 1.2 1.2 0 .6-.5 1.2-1.2 1.2z"></path><path d="M14.8 22.9c.3 0 .7-.1.9-.5l3.7-6.7c.3-.6.5-1.2.5-1.9 0-2.2-1.8-4-4-4-2.2 0-4 1.8-4 4 0 .7.2 1.3.5 1.9l3.7 6.7c.2.3.6.5.9.5h.3c-.1 0-.1 0 0 0zm.2-1c-.2 0-.4-.1-.5-.2l-3.7-6.7c-.2-.4-.3-.8-.3-1.2 0-1.7 1.3-3 3-3s3 1.3 3 3c0 .4-.1.9-.3 1.2l-3.7 6.7c-.1.1-.3.2-.5.2z"></path></svg>
                    <p class="text-secondary">No transactions found</p>
                    <a href="{{ route('wallet.topup.form') }}" class="btn btn-primary mt-2">Top Up Your Wallet</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection