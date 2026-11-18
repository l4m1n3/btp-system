<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module Logistique : matériel/engins (affectations, maintenances) et stock de matériaux.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materiels', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('designation');
            $table->string('type', 20)->default('engin');
            $table->string('marque')->nullable();
            $table->string('immatriculation')->nullable();
            $table->date('date_acquisition')->nullable();
            $table->decimal('valeur_acquisition', 15, 2)->nullable();
            $table->string('etat', 20)->default('disponible')->index();
            $table->foreignId('chantier_id')->nullable()->constrained('chantiers')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('affectations_materiel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materiel_id')->constrained('materiels')->cascadeOnDelete();
            $table->foreignId('chantier_id')->constrained('chantiers')->cascadeOnDelete();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materiel_id')->constrained('materiels')->cascadeOnDelete();
            $table->date('date_maintenance');
            $table->string('type', 20)->default('corrective'); // preventive | corrective
            $table->string('description');
            $table->decimal('cout', 15, 2)->default(0);
            $table->foreignId('depense_id')->nullable()->constrained('depenses')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('designation');
            $table->string('unite', 30);
            $table->decimal('stock_minimum', 12, 2)->default(0);
            $table->decimal('stock_actuel', 12, 2)->default(0);      // tenu à jour par StockService
            $table->decimal('cout_moyen', 15, 2)->default(0);        // coût moyen unitaire pondéré
            $table->timestamps();
        });

        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->restrictOnDelete();
            $table->string('type', 20); // entree | sortie | ajustement
            $table->decimal('quantite', 12, 2); // toujours positive, sauf ajustement (+/-)
            $table->decimal('prix_unitaire', 15, 2)->default(0); // prix d'achat (entrée) ou coût moyen (sortie)
            $table->foreignId('chantier_id')->nullable()->constrained('chantiers')->restrictOnDelete();
            $table->foreignId('fournisseur_id')->nullable()->constrained('fournisseurs')->nullOnDelete();
            $table->date('date_mouvement')->index();
            $table->string('reference')->nullable();
            $table->string('observation')->nullable();
            $table->foreignId('saisi_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('maintenances');
        Schema::dropIfExists('affectations_materiel');
        Schema::dropIfExists('materiels');
    }
};
