<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = auth()->user()->products();
        
        // Apply filters if provided
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Default sort by latest
        $query->orderBy('created_at', 'desc');
        
        $products = $query->paginate(10);
        $categories = auth()->user()->products()->distinct('category')->pluck('category')->filter();
        
        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get product categories to populate dropdown
        $categories = auth()->user()->products()->distinct('category')->pluck('category')->filter();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Generate SKU if not provided
        $sku = $request->sku ?? $this->generateSku($request->name);

        $product = Product::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'sku' => $sku,
            'category' => $request->category,
            'image_path' => $imagePath,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        Gate::authorize('view', $product);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        Gate::authorize('update', $product);
        $categories = auth()->user()->products()->distinct('category')->pluck('category')->filter();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        Gate::authorize('update', $product);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $imagePath = $product->image_path;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'sku' => $request->sku ?? $product->sku,
            'category' => $request->category,
            'image_path' => $imagePath,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        Gate::authorize('delete', $product);

        // Delete product image if exists
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Update product quantity
     */
    public function updateQuantity(Request $request, Product $product)
    {
        Gate::authorize('update', $product);

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:0',
            'adjustment_type' => 'required|in:set,add,subtract',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $newQuantity = $product->quantity;
        
        if ($request->adjustment_type === 'set') {
            $newQuantity = $request->quantity;
        } elseif ($request->adjustment_type === 'add') {
            $newQuantity += $request->quantity;
        } elseif ($request->adjustment_type === 'subtract') {
            $newQuantity = max(0, $newQuantity - $request->quantity);
        }

        $product->update(['quantity' => $newQuantity]);

        return redirect()->back()->with('success', 'Product quantity updated successfully.');
    }

    /**
     * Generate a SKU based on the product name
     */
    private function generateSku($name)
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 3));
        $randomString = strtoupper(Str::random(5));
        $timestamp = now()->format('Ymd');
        
        return $prefix . '-' . $randomString . '-' . $timestamp;
    }
}