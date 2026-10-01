<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;


class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Invoice::with('project.client')
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->when($request->query('search'), function ($query, $search) {
                $query->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('project.client', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest('paid_at')
            ->get();

        $totalReceived = $payments->sum('amount');

        return view('admin.payments', [
            'payments' => $payments,
            'totalReceived' => $totalReceived,
            'search' => $request->query('search'),
        ]);
    }
}