<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Paid invoices that never got a payment date: use the last update time
        DB::table('invoices')
            ->where('status', 'paid')
            ->whereNull('paid_at')
            ->update(['paid_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        //
    }
};