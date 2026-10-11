<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->query('q', ''));

        $projects = collect();
        $invoices = collect();
        $clients = collect();

        if (strlen($q) >= 2) {
            $projects = Project::with('client')
                ->where('name', 'like', "%{$q}%")
                ->orWhereHas('client', fn ($query) => $query->where('name', 'like', "%{$q}%"))
                ->latest()
                ->limit(15)
                ->get();

            $invoices = Invoice::with('project.client')
                ->where('invoice_number', 'like', "%{$q}%")
                ->orWhereHas('project', fn ($query) => $query->where('name', 'like', "%{$q}%"))
                ->orWhereHas('project.client', fn ($query) => $query->where('name', 'like', "%{$q}%"))
                ->latest()
                ->limit(15)
                ->get();

            $clients = User::where('role', 'client')
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                })
                ->limit(15)
                ->get();
        }

        return view('admin.search-results', compact('q', 'projects', 'invoices', 'clients'));
    }
}