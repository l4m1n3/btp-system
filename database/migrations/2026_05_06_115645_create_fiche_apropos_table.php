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
        Schema::create('fiche_appros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiche_id')->constrained('fiches_journalieres')->onDelete('cascade');
            $table->string('designation'); // Sable, Ciment, Gravier, Fer à béton...
            $table->string('unite')->nullable(); // m3, Tonne, paquets, unité
            $table->decimal('quantite', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiche_appros');
    }
};
