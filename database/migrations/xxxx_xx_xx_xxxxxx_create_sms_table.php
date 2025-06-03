<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sms', function (Blueprint $table) {
            $table->id();
            $table->string('gateway');
            $table->string('mode');
            $table->boolean('status');
            $table->string('username');
            $table->string('password');
            $table->string('grant_type');
            $table->string('otp_template');
            $table->timestamps();
        });

        // Insert the initial record
        DB::table('sms')->insert([
            'gateway' => 'hormuud_sms',
            'mode' => 'live',
            'status' => 1,
            'username' => 'somxchange',
            'password' => 'cLo++Fh0jnd4w2GppDOPGA==',
            'grant_type' => 'password',
            'otp_template' => 'Your OTP code is:',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms');
    }
};
