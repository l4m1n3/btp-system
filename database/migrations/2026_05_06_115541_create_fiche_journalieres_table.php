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
        Schema::create('fiches_journalieres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chantier_id')->constrained('chantiers')->onDelete('cascade');
            $table->date('date');
            $table->text('observations')->nullable();
            $table->text('evenements')->nullable(); // section V du formulaire
            $table->string('controleur_nom')->nullable();
            $table->string('controleur_signature')->nullable();
            $table->string('entreprise_nom')->nullable();
            $table->string('entreprise_signature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiche_journalieres');
    }
};
