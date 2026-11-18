<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module Chantiers : chantiers, planning (tâches), journal de chantier, pointage.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chantiers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('nom');
            $table->foreignId('marche_id')->nullable()->constrained('marches')->nullOnDelete();
            $table->foreignId('chef_chantier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('localisation')->nullable();
            $table->decimal('budget', 15, 2)->default(0);
            $table->string('statut', 30)->default('en_preparation')->index();
            $table->date('date_debut')->nullable();
            $table->date('date_fin_prevue')->nullable();
            $table->date('date_fin_reelle')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chantier_id')->constrained('chantiers')->cascadeOnDelete();
            $table->string('libelle');
            $table->string('responsable')->nullable();
            $table->date('date_debut_prevue');
            $table->date('date_fin_prevue');
            $table->date('date_debut_reelle')->nullable();
            $table->date('date_fin_reelle')->nullable();
            $table->decimal('cout_prevu', 15, 2)->default(0);
            $table->unsignedTinyInteger('avancement')->default(0); // 0..100
            $table->string('statut', 20)->default('planifie');
            $table->unsignedInteger('ordre')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('rapports_journaliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chantier_id')->constrained('chantiers')->cascadeOnDelete();
            $table->date('date_rapport');
            $table->string('meteo')->nullable();
            $table->text('observations')->nullable();
            $table->text('incidents')->nullable();
            $table->foreignId('redige_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['chantier_id', 'date_rapport']);
        });

        Schema::create('rapport_journalier_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapport_journalier_id')->constrained('rapports_journaliers')->cascadeOnDelete();
            $table->string('type', 30); // personnel | materiel | approvisionnement | travaux
            $table->string('designation');
            $table->decimal('quantite', 12, 2)->nullable();
            $table->string('unite', 30)->nullable();
            $table->foreignId('tache_id')->nullable()->constrained('taches')->nullOnDelete();
            $table->unsignedTinyInteger('avancement')->nullable(); // pour les lignes "travaux"
            $table->timestamps();
        });

        Schema::create('pointages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->cascadeOnDelete();
            $table->foreignId('chantier_id')->nullable()->constrained('chantiers')->nullOnDelete();
            $table->date('date_pointage');
            $table->boolean('present')->default(true);
            $table->decimal('heures_sup', 5, 2)->default(0);
            $table->string('observation')->nullable();
            $table->foreignId('saisi_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['employe_id', 'date_pointage']);
        });

        Schema::table('employes', function (Blueprint $table) {
            $table->foreignId('chantier_id')->nullable()->after('categorie')
                ->constrained('chantiers')->nullOnDelete(); // affectation actuelle
        });
    }

    public function down(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('chantier_id');
        });
        Schema::dropIfExists('pointages');
        Schema::dropIfExists('rapport_journalier_lignes');
        Schema::dropIfExists('rapports_journaliers');
        Schema::dropIfExists('taches');
        Schema::dropIfExists('chantiers');
    }
};
