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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->unsignedBigInteger('property_id')->nullable()->nullable();
            $table->unsignedBigInteger('unit_id')->nullable()->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('transaction_type');
            $table->string('description');
            $table->decimal('credit', 10, 2)->nullable()->comment('Amount Paid');
            $table->decimal('debit', 10, 2)->nullable()->comment('Amount Due to Pay');
            $table->enum('status', ['Completed', 'Pending'])->default('Completed');
            $table->timestamps();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
