<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function index(){
        return view('admin.dashboard');
    }

    function indexEvent(){
        return view('admin.events');
    }

    function indexTransaction(){
        $transactions = \App\Models\Transaction::with('event')->latest()->paginate(20);
        return view('admin.transactions', compact('transactions'));
    }

}
