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
        Schema::table('breakdown_services', function (Blueprint $table) {
          $table->text('breakdown_technician_problem_action')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breakdown_services', function (Blueprint $table) {
            $table->dropColumn('breakdown_technician_problem_action');
        });
    }
};
