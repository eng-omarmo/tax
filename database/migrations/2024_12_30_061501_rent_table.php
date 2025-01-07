<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('unit_id');
            $table->string('rent_code');
            $table->decimal('rent_amount', 10, 2);
            $table->date('rent_start_date');
            $table->date('rent_end_date')->nullable();
            $table->enum('status', ['active', 'terminated'])->default('active');
            $table->timestamps();
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');

            $table->index(['property_id', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rents');
    }
};
