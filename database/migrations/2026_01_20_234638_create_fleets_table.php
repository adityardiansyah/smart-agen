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
        Schema::create('fleets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->onDelete('cascade');
            $table->string('license_plate')->unique();
            $table->integer('year_manufacture');
            $table->string('keur_number');
            $table->date('keur_expiry');
            $table->date('stnk_expiry');
            $table->date('vehicle_expiry');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['agency_id', 'is_active']);
            $table->index('license_plate');
            $table->index('keur_expiry');
            $table->index('stnk_expiry');
            $table->index('vehicle_expiry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fleets');
    }
};