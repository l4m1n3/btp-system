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
        Schema::create('fiche_travaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiche_id')->constrained('fiches_journalieres')->onDelete('cascade');
            $table->string('designation');
            $table->string('niveau_avancement')->nullable(); // ex: "50%", "Terminé"
            $table->foreignId('planning_id') // lien avec la table plannings existante
                ->nullable()
                ->constrained('plannings')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiche_travaux');
    }
};
