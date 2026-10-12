<?php

namespace App\Http\Controllers\Admin;

use App\Console\Commands\MarkOverdueInvoices;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_clients' => User::where('role', 'client')->count(),
            'total_projects' => Project::count(),
            'pending_projects' => Project::where('status', 'pending')->count(),
            'in_progress_projects' => Project::where('status', 'in_progress')->count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
            'total_revenue' => Invoice::where('status', 'paid')->sum('amount'),
            'outstanding_amount' => Invoice::whereIn('status', ['pending', 'overdue'])->sum('amount'),
            'overdue_invoices' => Invoice::where('status', 'overdue')->count(),
        ];

        $recentProjects = Project::with('client')->latest()->take(5)->get();
        $monthlyRevenue = collect(range(5, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            return [
                'label' => $date->format('M'),
                'total' => Invoice::where('status', 'paid')
                    ->whereYear('paid_at', $date->year)
                    ->whereMonth('paid_at', $date->month)
                    ->sum('amount'),
            ];
        });

        return view('admin.dashboard', compact('stats', 'recentProjects', 'monthlyRevenue'));
    }

    public function checkOverdueInvoices()
    {
        Artisan::call(MarkOverdueInvoices::class);

        return back()->with('success', 'Overdue check complete. ' . trim(Artisan::output()));
    }
}