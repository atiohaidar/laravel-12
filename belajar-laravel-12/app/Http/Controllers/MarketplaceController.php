<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketplaceController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Display a listing of all products available for purchase.
     */
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)
            ->where('quantity', '>', 0)
            ->where('user_id', '!=', auth()->id()); // Exclude user's own products
        
        // Apply filters if provided
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }
        
        // Sort options
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->paginate(12);
        $categories = Product::where('is_active', true)
            ->where('quantity', '>', 0)
            ->distinct('category')
            ->pluck('category')
            ->filter();
        
        return view('marketplace.index', compact('products', 'categories'));
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        // Only show active products with available quantity
        if (!$product->is_active || $product->quantity <= 0) {
            return redirect()->route('marketplace.index')
                ->with('error', 'Product is not available for purchase.');
        }
        
        // Get seller information
        $seller = $product->user;
        
        // Get user's wallet balance
        $walletBalance = auth()->user()->wallet->balance ?? 0;
        
        return view('marketplace.show', compact('product', 'seller', 'walletBalance'));
    }

    /**
     * Show purchase confirmation page.
     */
    public function confirmPurchase(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->quantity,
        ]);

        $quantity = $request->quantity;
        $totalPrice = $product->price * $quantity;
        $walletBalance = auth()->user()->wallet->balance ?? 0;
        
        // Check if product is available
        if (!$product->is_active || $product->quantity < $quantity) {
            return redirect()->route('marketplace.show', $product)
                ->with('error', 'Product quantity not available.');
        }
        
        // Check if it's user's own product
        if ($product->user_id == auth()->id()) {
            return redirect()->route('marketplace.show', $product)
                ->with('error', 'You cannot purchase your own product.');
        }
        
        // Check if user has enough balance
        if ($walletBalance < $totalPrice) {
            return redirect()->route('marketplace.show', $product)
                ->with('error', 'Insufficient wallet balance. Please top up your wallet first.');
        }
        
        return view('marketplace.confirm', compact('product', 'quantity', 'totalPrice', 'walletBalance'));
    }

    /**
     * Process the purchase.
     */
    public function purchase(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->quantity,
        ]);

        $quantity = $request->quantity;
        $totalPrice = $product->price * $quantity;
        $buyer = auth()->user();
        $seller = $product->user;
        
        // Check product availability again
        if (!$product->is_active || $product->quantity < $quantity) {
            return redirect()->route('marketplace.show', $product)
                ->with('error', 'Product quantity not available.');
        }
        
        // Check if it's user's own product
        if ($product->user_id == $buyer->id) {
            return redirect()->route('marketplace.show', $product)
                ->with('error', 'You cannot purchase your own product.');
        }
        
        // Begin transaction to ensure data integrity
        return DB::transaction(function () use ($product, $quantity, $totalPrice, $buyer, $seller) {
            // Check wallet balance
            if ($buyer->wallet->balance < $totalPrice) {
                return redirect()->route('marketplace.show', $product)
                    ->with('error', 'Insufficient wallet balance. Please top up your wallet first.');
            }
            
            // Process payment - deduct from buyer
            $buyerTransaction = $this->walletService->createTransaction(
                $buyer->id,
                $buyer->id,
                $seller->id,
                'purchase',
                -$totalPrice,
                $buyer->wallet->balance - $totalPrice,
                "Purchase of {$quantity} {$product->name}",
                null
            );
            
            // Add to seller's wallet
            $sellerTransaction = $this->walletService->createTransaction(
                $seller->id,
                $buyer->id,
                $seller->id,
                'sale',
                $totalPrice,
                $seller->wallet->balance + $totalPrice,
                "Sale of {$quantity} {$product->name}",
                null
            );
            
            // Update product quantity
            $product->decrement('quantity', $quantity);
            
            // Create order record
            $order = Order::create([
                'buyer_id' => $buyer->id,
                'seller_id' => $seller->id,
                'product_id' => $product->id,
                'transaction_id' => $buyerTransaction->id,
                'quantity' => $quantity,
                'total_price' => $totalPrice,
                'status' => 'completed',
            ]);
            
            return redirect()->route('marketplace.purchased')
                ->with('success', "You've successfully purchased {$quantity} {$product->name} for " . number_format($totalPrice, 2));
        });
    }

    /**
     * Display user's purchased products.
     */
    public function purchased()
    {
        $purchases = Order::where('buyer_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('marketplace.purchased', compact('purchases'));
    }

    /**
     * Display user's sold products.
     */
    public function sold()
    {
        $sales = Order::where('seller_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('marketplace.sold', compact('sales'));
    }

    /**
     * Display the details of a specific order.
     */
    public function orderDetail(Order $order)
    {
        // Ensure user can only view their own orders
        if ($order->buyer_id != auth()->id() && $order->seller_id != auth()->id()) {
            return redirect()->route('marketplace.purchased')
                ->with('error', 'You are not authorized to view this order.');
        }
        
        // Determine if user is buyer or seller
        $isBuyer = $order->buyer_id == auth()->id();
        
        return view('marketplace.order-detail', compact('order', 'isBuyer'));
    }
}