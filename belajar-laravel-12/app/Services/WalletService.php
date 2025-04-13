<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletService
{
    /**
     * Create a wallet for a user
     *
     * @param User $user
     * @return Wallet
     */
    public function createWallet(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );
    }

    /**
     * Top up wallet balance
     *
     * @param User $user
     * @param float $amount
     * @param string|null $description
     * @return Transaction
     */
    public function topUp(User $user, float $amount, ?string $description = null): Transaction
    {
        return DB::transaction(function () use ($user, $amount, $description) {
            // Get or create wallet
            $wallet = $this->createWallet($user);
            
            // Update wallet balance
            $wallet->balance += $amount;
            $wallet->save();
            
            // Create transaction record
            return Transaction::create([
                'user_id' => $user->id,
                'type' => 'topup',
                'amount' => $amount,
                'balance_after' => $wallet->balance,
                'description' => $description ?? 'Top up',
                'reference_id' => 'TOP' . Str::random(8),
            ]);
        });
    }

    /**
     * Transfer money to another user
     *
     * @param User $sender
     * @param User $recipient
     * @param float $amount
     * @param string|null $description
     * @return array
     * @throws \Exception
     */
    public function transfer(User $sender, User $recipient, float $amount, ?string $description = null): array
    {
        if ($sender->id === $recipient->id) {
            throw new \Exception("Cannot transfer to yourself");
        }

        return DB::transaction(function () use ($sender, $recipient, $amount, $description) {
            // Get or create wallet for both users
            $senderWallet = $this->createWallet($sender);
            $recipientWallet = $this->createWallet($recipient);
            
            // Check if sender has enough balance
            if ($senderWallet->balance < $amount) {
                throw new \Exception("Insufficient balance");
            }
            
            // Generate reference ID for this transaction
            $referenceId = 'TRF' . Str::random(8);
            
            // Update sender wallet
            $senderWallet->balance -= $amount;
            $senderWallet->save();
            
            // Create transaction record for sender
            $senderTransaction = Transaction::create([
                'user_id' => $sender->id,
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'type' => 'transfer',
                'amount' => $amount,
                'balance_after' => $senderWallet->balance,
                'description' => $description ?? "Transfer to {$recipient->name}",
                'reference_id' => $referenceId,
            ]);
            
            // Update recipient wallet
            $recipientWallet->balance += $amount;
            $recipientWallet->save();
            
            // Create transaction record for recipient
            $recipientTransaction = Transaction::create([
                'user_id' => $recipient->id,
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'type' => 'receive',
                'amount' => $amount,
                'balance_after' => $recipientWallet->balance,
                'description' => $description ?? "Received from {$sender->name}",
                'reference_id' => $referenceId,
            ]);
            
            return [
                'sender_transaction' => $senderTransaction,
                'recipient_transaction' => $recipientTransaction,
            ];
        });
    }

    /**
     * Get user wallet balance
     *
     * @param User $user
     * @return float
     */
    public function getBalance(User $user): float
    {
        $wallet = $this->createWallet($user);
        return $wallet->balance;
    }

    /**
     * Get transaction history for a user
     * 
     * @param User $user
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTransactionHistory(User $user, int $limit = 10)
    {
        return Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Create a transaction record and update wallet balance
     *
     * @param int $userId
     * @param int|null $senderId
     * @param int|null $recipientId
     * @param string $type
     * @param float $amount
     * @param float $balanceAfter
     * @param string|null $description
     * @param string|null $referenceId
     * @return Transaction
     */
    public function createTransaction(
        int $userId, 
        ?int $senderId, 
        ?int $recipientId, 
        string $type, 
        float $amount, 
        float $balanceAfter, 
        ?string $description = null, 
        ?string $referenceId = null
    ): Transaction 
    {
        // Get the user and update their wallet balance
        $user = User::findOrFail($userId);
        $wallet = $this->createWallet($user);
        $wallet->balance = $balanceAfter;
        $wallet->save();
        
        // Generate reference ID if not provided
        if (!$referenceId) {
            $prefix = match($type) {
                'purchase' => 'PUR',
                'sale' => 'SAL',
                'topup' => 'TOP',
                'transfer' => 'TRF',
                'receive' => 'RCV',
                default => 'TXN'
            };
            $referenceId = $prefix . Str::random(8);
        }
        
        // Create and return transaction record
        return Transaction::create([
            'user_id' => $userId,
            'sender_id' => $senderId,
            'recipient_id' => $recipientId,
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $balanceAfter,
            'description' => $description ?? "Transaction: {$type}",
            'reference_id' => $referenceId,
        ]);
    }
}