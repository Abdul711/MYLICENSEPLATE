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
        Schema::create('licenseplates', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();
            $table->string('region');
            $table->string('city');
            $table->string('status')->default('active'); // active, inactive, pending
            $table->timestamps();
        });

        // Add foreign key constraint if needed
        // $table->foreignId('user_id')->constrained()->onDelete('cascade');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
