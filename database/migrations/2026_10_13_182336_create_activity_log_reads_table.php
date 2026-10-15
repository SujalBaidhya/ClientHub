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
        if (! Schema::hasColumn('activity_logs', 'project_id')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->foreignId('project_id')->nullable()->after('user_id')
                      ->constrained()->nullOnDelete();
            });
        }

        Schema::create('activity_log_reads', function (Blueprint $table) {
            $table->foreignId('activity_log_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at')->useCurrent();
            $table->primary(['activity_log_id', 'user_id']);
        });
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->boolean('client_visible')->default(false)->after('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log_reads');
    }
};
