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
        // by day chicken counter
        Schema::create('calendar_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id');
            $table->tinyText('total_chicken');
            $table->tinyText('total_healthy_chicken');
            $table->tinyText('total_unhealthy_chicken');
            $table->tinyText('total_unknown_chicken');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendardata');
    }
};
