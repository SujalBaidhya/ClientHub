<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = (int) $request->query('year', now()->year);

        // Fetch all available years from invoices in a DB-agnostic manner
        $oldestInvoice = Invoice::oldest('created_at')->first();
        $minYear = now()->year;
        if ($oldestInvoice) {
            foreach ([$oldestInvoice->issued_at, $oldestInvoice->paid_at, $oldestInvoice->created_at] as $d) {
                if ($d instanceof Carbon && $d->year < $minYear) {
                    $minYear = $d->year;
                }
            }
        }

        $availableYears = range(now()->year, $minYear);

        // Overall KPIs
        $totalCollected = Invoice::where('status', 'paid')->sum('amount');
        $totalInvoiced = Invoice::sum('amount');
        $outstandingAmount = Invoice::whereIn('status', ['pending', 'overdue'])->sum('amount');
        $overdueAmount = Invoice::where('status', 'overdue')->sum('amount');
        $overdueCount = Invoice::where('status', 'overdue')->count();
        $paidCount = Invoice::where('status', 'paid')->count();
        $totalInvoiceCount = Invoice::count();

        $collectionRate = $totalInvoiced > 0 ? round(($totalCollected / $totalInvoiced) * 100, 1) : 0;
        $avgInvoiceValue = $totalInvoiceCount > 0 ? round($totalInvoiced / $totalInvoiceCount, 2) : 0;

        // Month-over-Month Growth for Current Month vs Previous Month
        $currentMonthRevenue = Invoice::where('status', 'paid')
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');

        $prevMonth = now()->subMonth();
        $prevMonthRevenue = Invoice::where('status', 'paid')
            ->whereYear('paid_at', $prevMonth->year)
            ->whereMonth('paid_at', $prevMonth->month)
            ->sum('amount');

        if ($prevMonthRevenue > 0) {
            $momGrowth = round((($currentMonthRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100, 1);
        } else {
            $momGrowth = $currentMonthRevenue > 0 ? 100.0 : null;
        }

        // Monthly breakdown for selected year (Jan - Dec)
        $monthlyData = [];
        $previousMonthCollected = null;

        for ($m = 1; $m <= 12; $m++) {
            $monthDate = Carbon::createFromDate($selectedYear, $m, 1);
            $monthLabel = $monthDate->format('M');
            $fullLabel = $monthDate->format('F Y');
            $isFutureMonth = ($selectedYear === now()->year && $m > now()->month) || ($selectedYear > now()->year);

            // Billed in this month (issued_at or created_at)
            $billedQuery = Invoice::where(function ($q) use ($selectedYear, $m) {
                $q->where(function ($sub) use ($selectedYear, $m) {
                    $sub->whereNotNull('issued_at')
                        ->whereYear('issued_at', $selectedYear)
                        ->whereMonth('issued_at', $m);
                })->orWhere(function ($sub) use ($selectedYear, $m) {
                    $sub->whereNull('issued_at')
                        ->whereYear('created_at', $selectedYear)
                        ->whereMonth('created_at', $m);
                });
            });

            $billedAmount = (clone $billedQuery)->sum('amount');
            $billedCount = (clone $billedQuery)->count();

            // Cash collected in this month (actual cashflow received in month $m)
            $collectedQuery = Invoice::where('status', 'paid')
                ->whereYear('paid_at', $selectedYear)
                ->whereMonth('paid_at', $m);

            $collectedAmount = (clone $collectedQuery)->sum('amount');
            $collectedCount = (clone $collectedQuery)->count();

            // Cohort Collection Rate: Of the invoices billed in this month, how much was paid vs unpaid?
            $cohortPaidAmount = (clone $billedQuery)->where('status', 'paid')->sum('amount');
            $unpaidAmount = (clone $billedQuery)->whereIn('status', ['pending', 'overdue'])->sum('amount');

            // Proper mathematically bounded collection rate (0% - 100%)
            $monthRate = null;
            if ($billedAmount > 0) {
                $monthRate = min(100.0, round(($cohortPaidAmount / $billedAmount) * 100, 1));
            }

            // Month-over-month cash revenue growth
            $monthMom = null;
            if (!$isFutureMonth) {
                if ($previousMonthCollected !== null) {
                    if ($previousMonthCollected > 0 && $collectedAmount > 0) {
                        $monthMom = round((($collectedAmount - $previousMonthCollected) / $previousMonthCollected) * 100, 1);
                    } elseif ($previousMonthCollected > 0 && $collectedAmount == 0) {
                        $monthMom = -100.0;
                    } elseif ($previousMonthCollected == 0 && $collectedAmount > 0) {
                        $monthMom = 100.0;
                    } else {
                        // Both 0: no change, show dash
                        $monthMom = null;
                    }
                }
                $previousMonthCollected = $collectedAmount;
            }

            $monthlyData[] = [
                'month' => $monthLabel,
                'full_month' => $fullLabel,
                'month_num' => $m,
                'is_future' => $isFutureMonth,
                'billed_amount' => (float) $billedAmount,
                'billed_count' => $billedCount,
                'collected_amount' => (float) $collectedAmount,
                'collected_count' => $collectedCount,
                'unpaid_amount' => (float) $unpaidAmount,
                'collection_rate' => $monthRate,
                'mom_growth' => $monthMom,
            ];
        }

        // Status Breakdown Totals
        $statusBreakdown = [
            'paid' => [
                'count' => $paidCount,
                'amount' => (float) $totalCollected,
                'color' => '#10b981',
            ],
            'pending' => [
                'count' => Invoice::where('status', 'pending')->count(),
                'amount' => (float) Invoice::where('status', 'pending')->sum('amount'),
                'color' => '#f59e0b',
            ],
            'overdue' => [
                'count' => $overdueCount,
                'amount' => (float) $overdueAmount,
                'color' => '#ef4444',
            ],
        ];

        // Top 5 Clients by Revenue
        $topClients = User::where('role', 'client')
            ->with(['projects.invoices' => function ($q) {
                $q->where('status', 'paid');
            }])
            ->get()
            ->map(function ($client) use ($totalCollected) {
                $clientRevenue = $client->projects->flatMap->invoices->sum('amount');
                $paidInvoicesCount = $client->projects->flatMap->invoices->count();
                $sharePercent = $totalCollected > 0 ? round(($clientRevenue / $totalCollected) * 100, 1) : 0;

                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'projects_count' => $client->projects->count(),
                    'paid_invoices_count' => $paidInvoicesCount,
                    'revenue' => (float) $clientRevenue,
                    'share_percent' => $sharePercent,
                ];
            })
            ->filter(fn ($c) => $c['revenue'] > 0)
            ->sortByDesc('revenue')
            ->take(5)
            ->values();

        return view('admin.analytics', compact(
            'selectedYear',
            'availableYears',
            'totalCollected',
            'totalInvoiced',
            'outstandingAmount',
            'overdueAmount',
            'overdueCount',
            'collectionRate',
            'avgInvoiceValue',
            'currentMonthRevenue',
            'prevMonthRevenue',
            'momGrowth',
            'monthlyData',
            'statusBreakdown',
            'topClients'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $selectedYear = (int) $request->query('year', now()->year);

        $filename = "clienthub-financial-analytics-{$selectedYear}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($selectedYear) {
            $file = fopen('php://output', 'w');

            // Header summary
            fputcsv($file, ['ClientHub Financial Analytics Report — Year ' . $selectedYear]);
            fputcsv($file, ['Generated At', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []);

            // Column Headers
            fputcsv($file, [
                'Month',
                'Invoices Billed (Count)',
                'Billed Amount (NPR)',
                'Invoices Collected (Count)',
                'Collected Revenue (NPR)',
                'Outstanding Amount (NPR)',
                'Collection Rate (%)',
            ]);

            $totalBilled = 0;
            $totalCollected = 0;
            $totalOutstanding = 0;
            $totalBilledCount = 0;
            $totalCollectedCount = 0;

            for ($m = 1; $m <= 12; $m++) {
                $monthDate = Carbon::createFromDate($selectedYear, $m, 1);
                $monthName = $monthDate->format('F Y');
                $isFutureMonth = ($selectedYear === now()->year && $m > now()->month) || ($selectedYear > now()->year);

                $billedQuery = Invoice::where(function ($q) use ($selectedYear, $m) {
                    $q->where(function ($sub) use ($selectedYear, $m) {
                        $sub->whereNotNull('issued_at')
                            ->whereYear('issued_at', $selectedYear)
                            ->whereMonth('issued_at', $m);
                    })->orWhere(function ($sub) use ($selectedYear, $m) {
                        $sub->whereNull('issued_at')
                            ->whereYear('created_at', $selectedYear)
                            ->whereMonth('created_at', $m);
                    });
                });

                $billedAmount = (clone $billedQuery)->sum('amount');
                $billedCount = (clone $billedQuery)->count();

                $collectedQuery = Invoice::where('status', 'paid')
                    ->whereYear('paid_at', $selectedYear)
                    ->whereMonth('paid_at', $m);

                $collectedAmount = (clone $collectedQuery)->sum('amount');
                $collectedCount = (clone $collectedQuery)->count();

                $cohortPaidAmount = (clone $billedQuery)->where('status', 'paid')->sum('amount');
                $unpaidAmount = (clone $billedQuery)->whereIn('status', ['pending', 'overdue'])->sum('amount');

                $rateStr = '—';
                if (!$isFutureMonth && $billedAmount > 0) {
                    $rateStr = min(100.0, round(($cohortPaidAmount / $billedAmount) * 100, 1)) . '%';
                }

                $totalBilled += $billedAmount;
                $totalCollected += $collectedAmount;
                $totalOutstanding += $unpaidAmount;
                $totalBilledCount += $billedCount;
                $totalCollectedCount += $collectedCount;

                fputcsv($file, [
                    $monthName,
                    $billedCount,
                    number_format($billedAmount, 2, '.', ''),
                    $collectedCount,
                    number_format($collectedAmount, 2, '.', ''),
                    number_format($unpaidAmount, 2, '.', ''),
                    $rateStr,
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, [
                'Total / Year Summary',
                $totalBilledCount,
                number_format($totalBilled, 2, '.', ''),
                $totalCollectedCount,
                number_format($totalCollected, 2, '.', ''),
                number_format($totalOutstanding, 2, '.', ''),
                ($totalBilled > 0 ? round(($totalCollected / $totalBilled) * 100, 1) : 0) . '%',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
