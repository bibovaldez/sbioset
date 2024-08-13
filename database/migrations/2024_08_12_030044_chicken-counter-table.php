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
        Schema::create('chicken_counter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams', 'id')->onUpdate('cascade');
            $table->integer('total_chicken');
            $table->integer('total_healthy_chicken');
            $table->integer('total_unhealthy_chicken');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
