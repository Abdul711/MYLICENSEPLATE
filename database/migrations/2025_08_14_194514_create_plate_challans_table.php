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
        Schema::create('plate_challans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('licenseplate_id');
            $table->string('pdf_path');
            $table->string('image_path');
            $table->string('invoice_number');
            $table->foreign('licenseplate_id')
                ->references('id')
                ->on('licenseplates')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plate_challans');
    }
};
