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
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->unsignedBigInteger('rent_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['Cash', 'Bank Transfer', 'Mobile Money'])->default('Cash');
            $table->string('reference')->nullable();
            $table->enum('status', ['Completed', 'Pending', 'Failed'])->default('Completed');
            $table->timestamps();
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade');
            $table->foreign('rent_id')->references('id')->on('rents')->onDelete('cascade');
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
