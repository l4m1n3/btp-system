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
        Schema::create('fiche_personnels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiche_id')->constrained('fiches_journalieres')->onDelete('cascade');
            $table->string('designation'); // ex: "Chef de chantier", "Maçon"...
            $table->unsignedInteger('hommes')->default(0);
            $table->unsignedInteger('femmes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiche_personnels');
    }
};
