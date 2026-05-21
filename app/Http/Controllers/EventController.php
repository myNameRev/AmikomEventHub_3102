<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function show($id)
    {
        $event = Event::with('category')->findOrFail($id);
        $categories = \App\Models\Category::all();

        return view('event-detail', compact('event', 'categories'));
    }


    public function ticket(Request $request)
    {
        // Update: tampilkan transaksi terbaru yang baru-baru ini dibuat user melalui query string
        // Jika ada ?transaction_id=... gunakan itu, kalau tidak fallback ke transaction terakhir.
        $transactionId = $request->query('transaction_id');

        $transactionQuery = \App\Models\Transaction::with('event');

        if ($transactionId) {
            $transaction = $transactionQuery->where('id', $transactionId)->latest()->first();
        } else {
            $transaction = $transactionQuery->latest()->first();
        }

        return view('ticket', compact('transaction'));
    }
}

