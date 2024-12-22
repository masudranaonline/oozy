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
        Schema::table('mechine_types', function (Blueprint $table) {
            $table->string('partial_maintenance_day')->nullable();
            $table->string('full_maintenance_day')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mechine_types', function (Blueprint $table) {
        $table->dropColumn('partial_maintenance_day');
        $table->dropColumn('full_maintenance_day');
        });
    }
};