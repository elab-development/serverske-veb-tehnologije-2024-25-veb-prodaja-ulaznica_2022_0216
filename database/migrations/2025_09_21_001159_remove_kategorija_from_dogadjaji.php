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
        Schema::table('dogadjaji', function (Blueprint $table) {
            if (Schema::hasColumn('dogadjaji', 'kategorija')) {
                $table->dropColumn('kategorija');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dogadjaji', function (Blueprint $table) {
            $table->string('kategorija')->nullable()->after('opis');
        });
    }
};
