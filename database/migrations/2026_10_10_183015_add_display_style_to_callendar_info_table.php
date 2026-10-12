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
        if (! Schema::hasColumn('callendar_info', 'display_style')) {
            Schema::table('callendar_info', function (Blueprint $table) {
                $table->string('display_style', 20)->default('dot')->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('callendar_info', function (Blueprint $table) {
            $table->dropColumn('display_style');
        });
    }
};
