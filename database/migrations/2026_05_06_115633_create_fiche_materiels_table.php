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
        Schema::create('fiche_materiels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiche_id')->constrained('fiches_journalieres')->onDelete('cascade');
            $table->string('designation'); // ex: "Camion-benne", "Compacteur"...
            $table->unsignedInteger('nombre')->default(1);
            $table->string('etat')->nullable(); // bon, moyen, mauvais
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiche_materiels');
    }
};
