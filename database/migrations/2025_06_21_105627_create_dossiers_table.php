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
        Schema::create('dossiers', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nulltable();
            $table->enum('statut',['EN COURS','TERMINE','ARCHIVE'])->default('EN COURS');
            $table->foreignId('entreprise')->constrained('entreprises')->onDelete('cascade');
            $table->foreignId('classeur_id')->constrained('classeurs')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('archived_at')->nullable();
            $table->string('cloud_path')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();

            $table->index(['statut','created_at']);
            $table->index(['assigned_to', 'statut']);
            $table->index('archived_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossiers');
    }
};
