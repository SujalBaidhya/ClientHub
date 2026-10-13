<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       if (! Schema::hasColumn('activity_logs', 'client_visible')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->boolean('client_visible')->default(false)->after('url');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn('client_visible');
        });
    }
};
