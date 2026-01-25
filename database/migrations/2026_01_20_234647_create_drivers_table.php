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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fleet_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('age');
            $table->date('sim_expiry');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['fleet_id', 'is_active']);
            $table->index('sim_expiry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};