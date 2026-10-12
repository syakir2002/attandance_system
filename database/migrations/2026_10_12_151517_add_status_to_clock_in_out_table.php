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
        if (! Schema::hasColumn('clock_in_out', 'status')) {
            Schema::table('clock_in_out', function (Blueprint $table) {
                $table->string('status', 50)->nullable()->after('period');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('clock_in_out', 'status')) {
            Schema::table('clock_in_out', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
