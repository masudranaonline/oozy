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
        Schema::create('machine_tags', function (Blueprint $table) {
          $table->id();
          $table->uuid('uuid')->unique();
          $table->string('name');
          $table->text('note')->nullable();
          $table->enum('status',['Active','Inactive'])->default("Inactive");
          $table->morphs('creator');
          $table->morphs('updater');
          $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_tags');
    }
};