@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">My Products</h2>
            <p class="text-secondary">Manage your inventory</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add New Product
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('products.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search products..." name="search" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="category" onchange="this.form.submit()">
                        <option value="all" {{ request('category') == 'all' ? 'selected' : '' }}>All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex justify-content-end">
                    @if(request('search') || request('category') && request('category') != 'all')
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if(count($products) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="p-3" style="width: 70px"></th>
                                <th class="p-3">Name</th>
                                <th class="p-3">SKU</th>
                                <th class="p-3">Category</th>
                                <th class="p-3">Price</th>
                                <th class="p-3">Quantity</th>
                                <th class="p-3">Status</th>
                                <th class="p-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td class="p-3">
                                        @if($product->image_path)
                                            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 4px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-secondary"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        <a href="{{ route('products.show', $product) }}" class="text-decoration-none fw-medium">{{ $product->name }}</a>
                                    </td>
                                    <td class="p-3">{{ $product->sku }}</td>
                                    <td class="p-3">{{ $product->category ?? '-' }}</td>
                                    <td class="p-3">Rp {{ number_format($product->price, 2) }}</td>
                                    <td class="p-3">
                                        <span class="{{ $product->quantity > 0 ? 'text-success' : 'text-danger' }} fw-medium">
                                            {{ number_format($product->quantity) }}
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" data-bs-toggle="modal" data-bs-target="#quantityModal{{ $product->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20v-6m0 0V4m0 10h6m-6 0H6"></path></svg>
                                        </button>
                                        <!-- Quantity Update Modal -->
                                        <div class="modal fade" id="quantityModal{{ $product->id }}" tabindex="-1" aria-labelledby="quantityModalLabel{{ $product->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="quantityModalLabel{{ $product->id }}">Update Quantity for {{ $product->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('products.update-quantity', $product) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Current quantity: <strong>{{ number_format($product->quantity) }}</strong></p>
                                                            <div class="mb-3">
                                                                <label class="form-label">Adjustment Type</label>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="adjustment_type" id="adjustment_set{{ $product->id }}" value="set" checked>
                                                                    <label class="form-check-label" for="adjustment_set{{ $product->id }}">
                                                                        Set to specific amount
                                                                    </label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="adjustment_type" id="adjustment_add{{ $product->id }}" value="add">
                                                                    <label class="form-check-label" for="adjustment_add{{ $product->id }}">
                                                                        Add to current amount
                                                                    </label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="adjustment_type" id="adjustment_subtract{{ $product->id }}" value="subtract">
                                                                    <label class="form-check-label" for="adjustment_subtract{{ $product->id }}">
                                                                        Subtract from current amount
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="quantity" class="form-label">Quantity</label>
                                                                <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="{{ $product->quantity }}">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Update Quantity</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        @if($product->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $product->id }}">Delete</button>
                                        </div>
                                        
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $product->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $product->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $product->id }}">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete the product "{{ $product->name }}"?</p>
                                                        <p class="text-danger">This action cannot be undone.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center p-3">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-secondary mb-3"><path d="M20 5H8a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Z"></path><path d="M16 2v6"></path><path d="M12 2v6"></path><path d="M2 12h4"></path><path d="M2 16h4"></path><path d="M2 20h4"></path></svg>
                    <h5 class="mb-3">No products found</h5>
                    <p class="text-secondary mb-4">Get started by creating your first product in the inventory.</p>
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Add New Product
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection