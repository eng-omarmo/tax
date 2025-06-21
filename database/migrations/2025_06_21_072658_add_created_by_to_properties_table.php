<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            // Add the column (nullable first if you have existing data)
            $table->unsignedBigInteger('created_by')->nullable();

            // Add foreign key constraint
            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict'); // or 'set null' if nullable
        });

        // Optional: Set default user for existing records
        \DB::table('properties')->update(['created_by' => 1]); // Replace 1 with admin user ID
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['created_by']);

            // Then drop the column
            $table->dropColumn('created_by');
        });
    }
};
