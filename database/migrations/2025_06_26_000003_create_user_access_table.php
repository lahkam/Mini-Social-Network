<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viewer_id')->constrained('users')->onDelete('cascade'); // User who can view
            $table->foreignId('content_owner_id')->constrained('users')->onDelete('cascade'); // User whose content can be viewed
            $table->timestamp('granted_at')->useCurrent();
            $table->timestamps();
            
            // Ensure unique access relationships
            $table->unique(['viewer_id', 'content_owner_id']);
            $table->index(['content_owner_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_access');
    }
};
