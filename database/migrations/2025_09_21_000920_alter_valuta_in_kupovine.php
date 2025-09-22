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
        Schema::table('kupovine', function (Blueprint $table) {
            // menja samo DEFAULT vrednost (primer: sa 'EUR' na 'RSD')
            $table->string('valuta', 3)->default('RSD')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kupovine', function (Blueprint $table) {
            $table->string('valuta', 3)->default('EUR')->change();
        });
    }
};
