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
        if (! Schema::hasTable('user')) {
            Schema::create('user', function (Blueprint $table) {
                $table->string('staff_id', 50)->primary();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('phone_number')->nullable();
                $table->string('img')->nullable();
                $table->enum('role', ['admin', 'staff'])->default('staff');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('clock_in_out')) {
            Schema::create('clock_in_out', function (Blueprint $table) {
                $table->string('id', 50)->primary();
                $table->string('staff_id', 50);
                $table->time('check_in');
                $table->time('check_out')->nullable();
                $table->date('date');
                $table->string('keterangan_in')->nullable();
                $table->string('keterangan_out')->nullable();
                $table->string('keterangan_staff')->nullable();
                $table->string('keterangan_admin')->nullable();
                $table->string('keterangan_system')->nullable();
                $table->time('period')->nullable();

                $table->foreign('staff_id')->references('staff_id')->on('user');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe down: do not drop user or clock_in_out
    }
};
