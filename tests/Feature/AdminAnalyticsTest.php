<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $client;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->client = User::factory()->create([
            'role' => 'client',
            'name' => 'Acme Corporation',
        ]);

        $this->project = Project::factory()->create([
            'client_id' => $this->client->id,
            'name' => 'Acme Redesign',
        ]);
    }

    public function test_guests_are_redirected_to_login_when_accessing_analytics(): void
    {
        $response = $this->get(route('admin.analytics.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_clients_cannot_access_analytics_and_receive_403(): void
    {
        $response = $this->actingAs($this->client)->get(route('admin.analytics.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_analytics_dashboard_with_kpis_and_monthly_data(): void
    {
        // Create sample invoices
        Invoice::factory()->create([
            'project_id' => $this->project->id,
            'invoice_number' => 'INV-2026-001',
            'amount' => 50000,
            'status' => 'paid',
            'paid_at' => Carbon::create(2026, 3, 15, 10, 0, 0),
            'issued_at' => Carbon::create(2026, 3, 1),
        ]);

        Invoice::factory()->create([
            'project_id' => $this->project->id,
            'invoice_number' => 'INV-2026-002',
            'amount' => 25000,
            'status' => 'pending',
            'issued_at' => Carbon::create(2026, 4, 1),
        ]);

        Invoice::factory()->create([
            'project_id' => $this->project->id,
            'invoice_number' => 'INV-2026-003',
            'amount' => 15000,
            'status' => 'overdue',
            'due_date' => Carbon::create(2026, 4, 10),
            'issued_at' => Carbon::create(2026, 3, 25),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.analytics.index', ['year' => 2026]));

        $response->assertOk();
        $response->assertSee('Financial Analytics');
        $response->assertSee('Collected');
        $response->assertSee('NPR 50,000'); // Paid amount
        $response->assertSee('NPR 40,000'); // Outstanding amount (25,000 + 15,000)
        $response->assertSee('1 overdue');
        $response->assertSee('Acme Corporation');
        $response->assertSee('Billed vs Collected Trend');
        $response->assertSee('Monthly Financial Ledger');
    }

    public function test_admin_can_filter_analytics_by_specific_year(): void
    {
        // Invoice in 2025
        Invoice::factory()->create([
            'project_id' => $this->project->id,
            'invoice_number' => 'INV-2025-001',
            'amount' => 30000,
            'status' => 'paid',
            'paid_at' => Carbon::create(2025, 5, 20),
            'issued_at' => Carbon::create(2025, 5, 1),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.analytics.index', ['year' => 2025]));

        $response->assertOk();
        $response->assertSee('Year 2025');
        $response->assertSee('May');
    }

    public function test_admin_can_export_analytics_csv_report(): void
    {
        Invoice::factory()->create([
            'project_id' => $this->project->id,
            'invoice_number' => 'INV-2026-010',
            'amount' => 12000,
            'status' => 'paid',
            'paid_at' => Carbon::create(2026, 2, 10),
            'issued_at' => Carbon::create(2026, 2, 1),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.analytics.export', ['year' => 2026]));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', strtolower($response->headers->get('content-type')));
        $this->assertStringContainsString('attachment; filename="clienthub-financial-analytics-2026.csv"', $response->headers->get('content-disposition'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('ClientHub Financial Analytics Report — Year 2026', $content);
        $this->assertStringContainsString('Month', $content);
        $this->assertStringContainsString('February 2026', $content);
        $this->assertStringContainsString('12000.00', $content);
    }
}
