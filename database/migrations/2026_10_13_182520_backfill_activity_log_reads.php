<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('activity_log_reads')->insertUsing(
            ['activity_log_id', 'user_id', 'read_at'],
            DB::table('activity_logs')
                ->crossJoin('users')
                ->where('users.role', 'admin')
                ->where('activity_logs.is_read', true)
                ->select('activity_logs.id', 'users.id', DB::raw('CURRENT_TIMESTAMP'))
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
