<?php

namespace App\Observers;

use App\Mail\SendETicket;
use App\Models\Transaction;
use Illuminate\Support\Facades\Mail;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        // Send e-ticket email ketika status menjadi settlement
        if ($transaction->isDirty('status') && $transaction->status === 'settlement') {
            Mail::to($transaction->customer_email)
                ->queue(new SendETicket($transaction));
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }
}
