<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module Dépenses : catégories, fournisseurs et dépenses (internes et chantiers).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories_depenses', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('portee', 20); // interne | chantier
            $table->string('description')->nullable();
            $table->boolean('systeme')->default(false); // catégories utilisées automatiquement (salaires…)
            $table->timestamps();
            $table->unique(['nom', 'portee']);
        });

        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('nif')->nullable();
            $table->string('telephone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            $table->timestamps();
        });

        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->string('portee', 20)->index(); // interne | chantier
            $table->foreignId('chantier_id')->nullable()->constrained('chantiers')->restrictOnDelete();
            $table->foreignId('tache_id')->nullable()->constrained('taches')->nullOnDelete();
            $table->foreignId('categorie_id')->constrained('categories_depenses')->restrictOnDelete();
            $table->foreignId('fournisseur_id')->nullable()->constrained('fournisseurs')->nullOnDelete();
            $table->date('date_depense')->index();
            $table->string('libelle');
            $table->decimal('montant', 15, 2);
            $table->string('mode_paiement', 20)->default('especes');
            $table->string('reference_piece')->nullable();
            $table->string('justificatif')->nullable();
            $table->string('statut', 20)->default('en_attente')->index();
            $table->foreignId('saisi_par')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('valide_le')->nullable();
            $table->string('motif_rejet')->nullable();
            $table->string('source_type')->nullable(); // ex. App\Models\PeriodePaie
            $table->unsignedBigInteger('source_id')->nullable();
            $table->timestamps();
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depenses');
        Schema::dropIfExists('fournisseurs');
        Schema::dropIfExists('categories_depenses');
    }
};
