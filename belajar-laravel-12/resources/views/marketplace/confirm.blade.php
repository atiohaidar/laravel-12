@extends('layouts.app')

@section('title', 'Confirm Purchase')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Confirm Purchase</div>
                
                <div class="card-body">
                    <div class="alert alert-info">
                        Please review your purchase details below before confirming.
                    </div>
                    
                    <div class="mb-4">
                        <h4>{{ $product->name }}</h4>
                        <p class="text-muted">Seller: {{ $product->user->name }}</p>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Price per unit:</span>
                        <span>${{ number_format($product->price, 2) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Quantity:</span>
                        <span>{{ $quantity }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <strong>Total Price:</strong>
                        <strong>${{ number_format($totalPrice, 2) }}</strong>
                    </div>
                    
                    <div class="alert alert-warning mb-4">
                        <div class="d-flex justify-content-between">
                            <span>Your current wallet balance:</span>
                            <span>${{ number_format($walletBalance, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Balance after purchase:</span>
                            <span>${{ number_format($walletBalance - $totalPrice, 2) }}</span>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('marketplace.show', $product) }}" class="btn btn-secondary">Cancel</a>
                        
                        <form action="{{ route('marketplace.purchase', $product) }}" method="POST">
                            @csrf
                            <input type="hidden" name="quantity" value="{{ $quantity }}">
                            <button type="submit" class="btn btn-primary">Confirm Purchase</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection