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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();


            $table->decimal('offer', 10, 2);
            $table->enum('status', ['Accepted', 'Pending', "Rejected"])->default('Pending');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('licenseplate_id');

            $table->foreign('licenseplate_id')->references('id')->on('licenseplates')->onDelete('cascade');



            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
