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
        Schema::table('plate_challans', function (Blueprint $table) {
            if (!Schema::hasColumn('plate_challans', 'due_date')) {
                $table->timestamp('due_date')
                       ->useCurrent()  
                    ->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('=plate_challans', function (Blueprint $table) {
            //
        });
    }
};
