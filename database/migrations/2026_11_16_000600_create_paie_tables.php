<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module Paie (simple) : périodes, bulletins et lignes (primes, indemnités, retenues).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodes_paie', function (Blueprint $table) {
            $table->id();
            $table->char('mois', 7)->unique(); // AAAA-MM
            $table->string('statut', 20)->default('brouillon');
            $table->timestamp('cloturee_le')->nullable();
            $table->foreignId('cloturee_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('bulletins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_paie_id')->constrained('periodes_paie')->cascadeOnDelete();
            $table->foreignId('employe_id')->constrained('employes')->restrictOnDelete();
            $table->foreignId('chantier_id')->nullable()->constrained('chantiers')->nullOnDelete(); // imputation
            $table->string('mode_remuneration', 20);
            $table->decimal('salaire_base', 15, 2)->default(0);
            $table->decimal('jours_travailles', 5, 2)->default(0);
            $table->decimal('jours_absence', 5, 2)->default(0);
            $table->decimal('salaire_du', 15, 2)->default(0);      // base proratisée ou jours × taux
            $table->decimal('heures_sup', 6, 2)->default(0);
            $table->decimal('montant_heures_sup', 15, 2)->default(0);
            $table->decimal('total_primes', 15, 2)->default(0);    // primes + indemnités
            $table->decimal('salaire_brut', 15, 2)->default(0);
            $table->decimal('total_avances', 15, 2)->default(0);
            $table->decimal('total_retenues', 15, 2)->default(0);
            $table->decimal('net_a_payer', 15, 2)->default(0);
            $table->boolean('paye')->default(false);
            $table->date('date_paiement')->nullable();
            $table->timestamps();
            $table->unique(['periode_paie_id', 'employe_id']);
        });

        Schema::create('bulletin_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulletin_id')->constrained('bulletins')->cascadeOnDelete();
            $table->string('type', 20); // prime | indemnite | retenue
            $table->string('libelle');
            $table->decimal('montant', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulletin_lignes');
        Schema::dropIfExists('bulletins');
        Schema::dropIfExists('periodes_paie');
    }
};
