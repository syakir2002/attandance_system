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
        if (! Schema::hasTable('callendar_info')) {
            Schema::create('callendar_info', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->dateTime('start_at');
                $table->dateTime('end_at');
                $table->timestamp('timestamps')->useCurrent();
                $table->tinyInteger('status')->default(0);
                $table->nullableTimestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('callendar_info');
    }
};
