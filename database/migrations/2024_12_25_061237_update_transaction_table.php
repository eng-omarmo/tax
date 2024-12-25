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
        Schema::table('transactions', function (Blueprint $table) {
            // Add new columns for credit and debit
            $table->decimal('credit', 10, 2)->nullable()->after('amount');
            $table->decimal('debit', 10, 2)->nullable()->after('credit');

            // Drop the transaction_type column
            $table->dropColumn('transaction_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Add the transaction_type column back
            $table->enum('transaction_type', ['Credit', 'Debit'])->after('amount');

            // Drop the credit and debit columns
            $table->dropColumn(['credit', 'debit']);
        });
    }
};
