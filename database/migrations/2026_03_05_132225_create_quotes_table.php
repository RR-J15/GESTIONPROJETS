<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('reference')->unique();            // ex: DEV-2026-001
            $table->string('title');
            $table->string('status')->default('brouillon');  // cast → QuoteStatus enum
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->decimal('tax_rate', 5, 2)->default(20.00);
            $table->decimal('discount_global', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};