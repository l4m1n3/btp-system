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
        Schema::create('entreprises', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('email');
            $table->string('telephone');
            $table->enum('abonnement', ['gratuit', 'mensuel', 'annuel'])->default('gratuit');
            $table->date('date_debut')->nullable(); // date de début de l'abonnement
            $table->date('date_fin')->nullable();   // date de fin de l'abonnement
            $table->boolean('actif')->default(false); // true si abonnement actif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patrons');
    }
};
