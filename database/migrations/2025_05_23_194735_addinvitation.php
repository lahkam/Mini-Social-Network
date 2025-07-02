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
        Schema::create('invitations', function (Blueprint $table) {
    $table->id();

    $table->foreignId('inviter_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('invitee_id')->constrained('users')->onDelete('cascade');
    $table->date('date')->nullable();
     $table->boolean('etat')->nullable();

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
