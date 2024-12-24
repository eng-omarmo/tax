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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('property_name');
            $table->string('property_phone', 25)->nullable();
            $table->string('nbr', 50)->nullable();
            $table->unsignedBigInteger('district_id');
            $table->string('house_code', 50)->nullable();
            $table->string('tenant_name')->nullable();
            $table->string('tenant_phone', 25)->nullable();
            $table->string('branch')->nullable();
            $table->string('zone', 100)->nullable();
            $table->string('designation', 100)->nullable();
            $table->string('house_type', 100)->nullable();
            $table->decimal('house_rent', 10, 2)->nullable();
            $table->decimal('quarterly_tax_fee', 10, 2)->nullable();
            $table->decimal('yearly_tax_fee', 10, 2)->nullable();
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->string('dalal_company_name')->nullable();
            $table->enum('is_owner', ['Yes', 'No'])->default('No');
            $table->enum('monitoring_status', ['Pending', 'Approved'])->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            $table->timestamps();
        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
                //
    }
};
