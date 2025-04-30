<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained()->onDelete('cascade');
            $table->string('account_number');
            $table->float('balance', 10, 2);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
}
