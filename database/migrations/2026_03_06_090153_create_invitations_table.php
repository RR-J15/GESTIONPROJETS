<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('email')->unique();
            $table->string('role')->default('membre');        // chef_projet | membre
            $table->string('token', 64)->unique();            // token unique
            $table->timestamp('expires_at');                  // +48h à la création
            $table->timestamp('used_at')->nullable();         // null = pas encore utilisé
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
