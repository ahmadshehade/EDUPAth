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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->decimal('amount', 12, 2);

            $table->string('currency', 10)->default('USD');

            $table->enum('status', ['pending', 'success', 'failed']);

            $table->string('provider')->nullable(); // stripe, paypal, manual
            $table->string('transaction_id')->nullable(); // من مزود الدفع

            $table->timestamp('paid_at')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
