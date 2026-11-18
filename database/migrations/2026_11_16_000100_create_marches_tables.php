<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module Marchés : clients (maîtres d'ouvrage), marchés et décomptes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('type', 20)->default('public'); // public | prive | ong
            $table->string('contact')->nullable();
            $table->string('telephone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('marches', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('intitule');
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->string('domaine', 30)->default('batiment');
            $table->string('statut', 30)->default('en_attente')->index();
            $table->string('localisation')->nullable();
            $table->decimal('montant_ht', 15, 2)->default(0);
            $table->decimal('taux_tva', 5, 2)->default(19);
            $table->decimal('taux_retenue_garantie', 5, 2)->default(5);
            $table->decimal('avance_demarrage', 15, 2)->default(0);
            $table->date('date_soumission')->nullable();
            $table->date('date_signature')->nullable();
            $table->date('date_ordre_service')->nullable();
            $table->unsignedInteger('delai_jours')->nullable();
            $table->date('date_fin_prevue')->nullable();
            $table->date('date_reception')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('decomptes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marche_id')->constrained('marches')->cascadeOnDelete();
            $table->unsignedInteger('numero');
            $table->date('date_decompte');
            $table->decimal('montant_ht', 15, 2);
            $table->decimal('montant_paye', 15, 2)->default(0);
            $table->date('date_paiement')->nullable();
            $table->string('statut', 20)->default('soumis');
            $table->string('observations')->nullable();
            $table->timestamps();
            $table->unique(['marche_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decomptes');
        Schema::dropIfExists('marches');
        Schema::dropIfExists('clients');
    }
};
