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
            $table->string('image')->nullable();
            $table->string('document')->nullable();
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('landlord_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('house_code', 50)->nullable();
            $table->string('zone', 100)->nullable();
            $table->string('house_type', 100)->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->enum('monitoring_status', ['Pending', 'Approved'])->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            $table->foreign('landlord_id')->references('id')->on('landlords')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');

            $table->timestamps();
        });
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
