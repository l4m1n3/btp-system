<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plannings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chantier_id')->constrained('chantiers')->onDelete('cascade');

            // Description de la tâche/activité
            $table->string('activite');
            $table->string('responsable')->nullable();
            $table->text('note')->nullable();

            // --- Détection des RETARDS ---
            $table->date('date_debut_prevue');
            $table->date('date_fin_prevue');
            $table->date('date_debut_reelle')->nullable();  // null = pas encore commencé
            $table->date('date_fin_reelle')->nullable();    // null = pas encore terminé

            // --- Détection des DÉPASSEMENTS BUDGÉTAIRES ---
            $table->decimal('cout_prevu', 15, 2);
            $table->decimal('cout_reel', 15, 2)->default(0);

            // Statut de l'activité
            $table->enum('statut', [
                'planifie',
                'en_cours',
                'termine',
                'retarde',
                'annule',
            ])->default('planifie');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plannings');
    }
};