<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('mobile_number')->nullable();
            $table->text('additional_info')->nullable();
            $table->timestamps();

            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_details');
    }
}
