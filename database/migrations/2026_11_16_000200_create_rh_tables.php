<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module RH : employés, congés/absences, avances.
 * (Le pointage est créé après les chantiers.)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->string('matricule')->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->string('sexe', 1)->nullable(); // M | F
            $table->date('date_naissance')->nullable();
            $table->string('telephone', 30)->nullable();
            $table->string('adresse')->nullable();
            $table->string('numero_cnss')->nullable();
            $table->string('fonction');
            $table->string('categorie')->nullable(); // cadre, agent de maîtrise, ouvrier qualifié…
            $table->string('type_contrat', 20)->default('cdi');
            $table->date('date_embauche');
            $table->date('date_fin_contrat')->nullable();
            $table->string('mode_remuneration', 20)->default('mensuel');
            $table->decimal('salaire_base', 15, 2)->default(0);   // mensuel
            $table->decimal('taux_journalier', 15, 2)->default(0); // journaliers
            $table->string('statut', 20)->default('actif')->index();
            $table->string('mode_paiement', 20)->default('especes');
            $table->string('numero_compte')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('employe_id')->nullable()->after('role')
                ->constrained('employes')->nullOnDelete();
        });

        Schema::create('conges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->string('type', 30);
            $table->date('date_debut');
            $table->date('date_fin');
            $table->string('motif')->nullable();
            $table->string('statut', 20)->default('demande');
            $table->foreignId('traite_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('avances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->date('date_avance');
            $table->decimal('montant', 15, 2);
            $table->string('motif')->nullable();
            $table->char('periode_retenue', 7); // AAAA-MM : mois où l'avance est retenue sur le salaire
            $table->foreignId('bulletin_id')->nullable(); // renseigné quand la retenue est faite
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avances');
        Schema::dropIfExists('conges');
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('employe_id');
        });
        Schema::dropIfExists('employes');
    }
};
