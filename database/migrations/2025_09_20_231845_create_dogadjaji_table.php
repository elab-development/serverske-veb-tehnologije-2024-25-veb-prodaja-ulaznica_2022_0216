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
        Schema::create('dogadjaji', function (Blueprint $table) {
            $table->id();
            $table->string('naziv');
            $table->text('opis')->nullable();
            $table->foreignId('mesto_id')->constrained('mesta')->cascadeOnDelete();
            $table->dateTime('datum_pocetka');
            $table->dateTime('datum_zavrsetka')->nullable();
            $table->string('kategorija')->nullable(); 
            $table->timestamps();

             $table->index(['mesto_id', 'datum_pocetka']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dogadjaji');
    }
};
