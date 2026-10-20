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

        if ($request->wantsJson()) {
            return response()->json([
                'projects' => $projects->map(fn ($p) => [
                    'id' => $p->id,
                    'title' => $p->name,
                    'subtitle' => $p->client->name ?? 'No client',
                    'url' => route('admin.projects.edit', $p),
                    'status' => $p->status,
                ]),
                'invoices' => $invoices->map(fn ($inv) => [
                    'id' => $inv->id,
                    'title' => $inv->invoice_number,
                    'subtitle' => ($inv->project->client->name ?? 'N/A') . ' — ' . ($inv->project->name ?? 'N/A'),
                    'url' => $inv->project ? route('admin.invoices.create', $inv->project) : route('admin.invoices.index'),
                    'status' => $inv->status,
                    'amount' => 'NPR ' . number_format($inv->amount, 2),
                ]),
                'clients' => $clients->map(fn ($c) => [
                    'id' => $c->id,
                    'title' => $c->name,
                    'subtitle' => $c->email,
                    'url' => route('admin.clients.create'),
                ]),
            ]);
        }

        return view('admin.search-results', compact('q', 'projects', 'invoices', 'clients'));
    }
}
