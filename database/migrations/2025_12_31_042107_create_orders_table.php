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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['pending', 'completed'])->default('pending'); // In production we will have more status type
            $table->decimal('subtotal_amount')->default(0);

            /**
             * The reason this column below not referenced to users table,
             * because in production, there will be scenario where user wants to delete their account permanently,
             * and also to maintain data integrity and transaction history,
             * we will not cascade it when user delete their account.
             * 
             * Usually, we will soft delete the user account first,
             * and give time range within X days to restore their account before cron scheduler perform permanent deletion.
             * 
             */
            $table->unsignedBigInteger('user_id');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'created_at']); // For user dashboard
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
