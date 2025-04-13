@extends('layouts.app')

@section('title', 'My Purchases')

@section('content')
<div class="container">
    <h1 class="mb-4">My Purchases</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Purchase History</h5>
            
            @if($purchases->isEmpty())
                <div class="alert alert-info">
                    You haven't made any purchases yet. 
                    <a href="{{ route('marketplace.index') }}" class="alert-link">Browse the marketplace</a> to find products.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Product</th>
                                <th>Seller</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>
                                        <a href="{{ route('marketplace.show', $order->product) }}">
                                            {{ $order->product->name }}
                                        </a>
                                    </td>
                                    <td>{{ $order->seller->name }}</td>
                                    <td>{{ $order->quantity }}</td>
                                    <td>${{ number_format($order->total_price, 2) }}</td>
                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        @if($order->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($order->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('marketplace.order.detail', $order) }}" class="btn btn-sm btn-primary">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $purchases->links() }}
                </div>
            @endif
        </div>
    </div>
    
    <div class="text-center mt-4">
        <a href="{{ route('marketplace.index') }}" class="btn btn-primary">
            Continue Shopping
        </a>
    </div>
</div>
@endsection