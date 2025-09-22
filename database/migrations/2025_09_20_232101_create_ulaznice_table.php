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
        Schema::create('ulaznice', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dogadjaj_id')->constrained('dogadjaji')->cascadeOnDelete();
            $table->foreignId('kupovina_id')->nullable()->constrained('kupovine')->nullOnDelete();
            $table->string('kod')->unique();                 // npr. QR/Bar kod
            $table->string('tip')->default('standard');      // npr. standard, vip
            $table->string('sediste')->nullable();           // oznaka sedišta (ako postoji)
            $table->decimal('cena', 10, 2);
            $table->enum('status', ['dostupna','rezervisana','prodata'])->default('dostupna');
            $table->timestamps();

            $table->index(['dogadjaj_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ulaznice');
    }
};
