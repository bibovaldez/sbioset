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
    {   // team_user table for poulry building and user
        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id');//building id
            $table->foreignId('user_id');//user id
            $table->string('role')->nullable();//admin, member
            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_user');
    }
};
