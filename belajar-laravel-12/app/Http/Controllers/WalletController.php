<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Display wallet dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $balance = $this->walletService->getBalance($user);
        $transactions = $this->walletService->getTransactionHistory($user, 5);
        
        return view('wallet.index', compact('balance', 'transactions'));
    }

    /**
     * Show top-up form
     */
    public function showTopUpForm()
    {
        return view('wallet.topup');
    }

    /**
     * Process top-up
     */
    public function topUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $user = auth()->user();
            $transaction = $this->walletService->topUp(
                $user, 
                $request->amount, 
                $request->description
            );

            return redirect()->route('wallet.index')->with('success', 'Successfully topped up ' . number_format($request->amount, 2));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show transfer form
     */
    public function showTransferForm()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return view('wallet.transfer', compact('users'));
    }

    /**
     * Process transfer
     */
    public function transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipient_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $sender = auth()->user();
            $recipient = User::findOrFail($request->recipient_id);
            
            $this->walletService->transfer(
                $sender, 
                $recipient, 
                $request->amount, 
                $request->description
            );

            return redirect()->route('wallet.index')->with('success', 'Successfully transferred ' . number_format($request->amount, 2) . ' to ' . $recipient->name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show transaction history
     */
    public function transactions()
    {
        $user = auth()->user();
        $transactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('wallet.transactions', compact('transactions'));
    }

    /**
     * Show transaction details
     */
    public function transactionDetail($id)
    {
        $transaction = Transaction::where('user_id', auth()->id())
            ->findOrFail($id);
        
        return view('wallet.transaction-detail', compact('transaction'));
    }
}