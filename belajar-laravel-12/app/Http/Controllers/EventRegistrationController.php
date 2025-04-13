<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Transaction;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventRegistrationController extends Controller
{
    protected $walletService;

    /**
     * Create a new controller instance.
     */
    public function __construct(WalletService $walletService)
    {
        // $this->middleware('auth');
        $this->walletService = $walletService;
    }

    /**
     * Register for an event.
     */
    public function register(Request $request, Event $event)
    {
        // Check if registration is open
        if (!$event->is_registration_open) {
            return back()->with('error', 'Registration is not open for this event.');
        }

        // Check if the event is full
        if ($event->is_full) {
            return back()->with('error', 'Sorry, this event is already at full capacity.');
        }

        // Check if user is already registered
        $existingRegistration = Auth::user()->eventRegistrations()
            ->where('event_id', $event->id)
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->first();

        if ($existingRegistration) {
            return back()->with('error', 'You are already registered for this event.');
        }

        // If event is paid, verify user has enough balance
        if ($event->is_paid && $event->price > 0) {
            $user = Auth::user();
            $wallet = $user->wallet;

            if (!$wallet || $wallet->balance < $event->price) {
                return back()->with('error', 'Insufficient wallet balance. Please add funds to your wallet.');
            }

            // Process payment using a transaction
            DB::beginTransaction();
            try {
                // Create transaction and update wallet balance
                $balanceAfter = $wallet->balance - $event->price;
                $transaction = $this->walletService->createTransaction(
                    $user->id,
                    $user->id,
                    $event->user_id,
                    'payment',
                    $event->price,
                    $balanceAfter,
                    "Payment for event: {$event->title}"
                );

                // Create registration with transaction ID
                $registration = EventRegistration::create([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'registration_number' => EventRegistration::generateRegistrationNumber(),
                    'amount_paid' => $event->price,
                    'transaction_id' => $transaction->id,
                    'status' => 'confirmed',
                ]);

                // If the event organizer has a different ID than the current user,
                // transfer the funds to the organizer
                if ($user->id !== $event->user_id) {
                    $organizer = User::find($event->user_id);
                    if ($organizer) {
                        $this->walletService->createTransaction(
                            $organizer->id,
                            $user->id,
                            $organizer->id,
                            'receive',
                            $event->price,
                            ($organizer->wallet ? $organizer->wallet->balance : 0) + $event->price,
                            "Payment received for event: {$event->title}"
                        );
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Transaction failed: ' . $e->getMessage());
            }
        } else {
            // Free event - just create registration
            $registration = EventRegistration::create([
                'event_id' => $event->id,
                'user_id' => Auth::id(),
                'registration_number' => EventRegistration::generateRegistrationNumber(),
                'amount_paid' => 0,
                'status' => 'confirmed',
            ]);
        }

        // Send event registration confirmation notification
        // This could be implemented in a notification class
        
        return redirect()->route('events.show', $event)
            ->with('success', 'Successfully registered for this event!');
    }

    /**
     * Cancel a registration.
     */
    public function cancel(EventRegistration $registration)
    {
        // Check if user owns this registration
        if ($registration->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Check if registration can be cancelled (e.g., event hasn't started yet)
        $event = $registration->event;
        if ($event->start_time->isPast()) {
            return back()->with('error', 'Cannot cancel registration for an event that has already started.');
        }

        DB::beginTransaction();
        try {
            // If this was a paid registration, process refund
            if ($registration->amount_paid > 0 && $registration->transaction_id) {
                $originalTransaction = $registration->transaction;
                $user = Auth::user();
                $wallet = $user->wallet;
                
                // Process refund
                $balanceAfter = $wallet->balance + $registration->amount_paid;
                $refundTransaction = $this->walletService->createTransaction(
                    $user->id,
                    $event->user_id,
                    $user->id,
                    'refund',
                    $registration->amount_paid,
                    $balanceAfter,
                    "Refund for cancelled event registration: {$event->title}",
                    $originalTransaction ? $originalTransaction->reference_id : null
                );

                // If the event organizer has a different ID than the current user,
                // deduct the funds from the organizer
                if ($user->id !== $event->user_id) {
                    $organizer = User::find($event->user_id);
                    if ($organizer && $organizer->wallet) {
                        $organizerBalanceAfter = $organizer->wallet->balance - $registration->amount_paid;
                        if ($organizerBalanceAfter >= 0) {
                            $this->walletService->createTransaction(
                                $organizer->id,
                                $organizer->id,
                                $user->id,
                                'refund_sent',
                                $registration->amount_paid,
                                $organizerBalanceAfter,
                                "Refund issued for cancelled event registration: {$event->title}",
                                $originalTransaction ? $originalTransaction->reference_id : null
                            );
                        }
                    }
                }
            }

            // Update registration status
            $registration->update([
                'status' => 'cancelled',
                'notes' => 'Cancelled by user on ' . now()->format('Y-m-d H:i:s'),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel registration: ' . $e->getMessage());
        }

        return redirect()->route('events.registrations.my')
            ->with('success', 'Registration cancelled successfully.');
    }

    /**
     * Check in a participant (for event organizers only).
     */
    public function checkIn(Request $request, EventRegistration $registration)
    {
        // Make sure the user is the event organizer
        $event = $registration->event;
        if ($event->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Update registration status
        $registration->update([
            'status' => 'attended',
            'checked_in_at' => now(),
            'notes' => $request->input('notes', 'Checked in by organizer'),
        ]);

        return back()->with('success', 'Participant checked in successfully.');
    }
}
