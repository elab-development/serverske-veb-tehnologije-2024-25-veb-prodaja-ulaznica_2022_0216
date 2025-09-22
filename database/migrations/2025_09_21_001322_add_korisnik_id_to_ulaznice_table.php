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
        Schema::table('ulaznice', function (Blueprint $table) {
             $table->foreignId('korisnik_id')
                  ->nullable()
                  ->after('dogadjaj_id')
                  ->constrained('users')
                  ->nullOnDelete(); // ako se obriše korisnik, vrednost postaje NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ulaznice', function (Blueprint $table) {
            $table->dropForeign(['korisnik_id']);
            $table->dropColumn('korisnik_id');
        });
    }
};
