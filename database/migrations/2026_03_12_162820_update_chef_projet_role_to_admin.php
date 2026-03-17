<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // ← ajouter cette ligne

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
          ->where('role', 'chef_projet')
          ->update(['role' => 'admin']);
    }

    public function down(): void
    {
        // irreversible
    }
};
