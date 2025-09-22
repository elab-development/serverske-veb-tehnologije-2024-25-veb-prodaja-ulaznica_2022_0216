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
        Schema::create('kupovine', function (Blueprint $table) {
            $table->id();
            $table->string('broj_porudzbine')->unique();
            $table->foreignId('korisnik_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('ukupno', 10, 2)->default(0);
            $table->string('valuta', 3)->default('EUR');
            $table->enum('nacin_placanja', ['kartica','paypal','gotovina'])->default('kartica');
            $table->enum('stanje', ['novo','placeno','otkazano'])->default('novo');
            $table->timestamps();

            $table->index(['korisnik_id', 'stanje']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kupovine');
    }
};
