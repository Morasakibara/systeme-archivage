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
        Schema::create('liens_partage', function (Blueprint $table) {
            $table->id();
            $table->string('token', 255)->unique();
            $table->enum('resource_type', ['CLASSEUR', 'DOSSIER', 'DOCUMENT']);
            $table->bigInteger('resource_id');
            $table->json('permissions');
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->integer('access_count')->default(0);
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            // Index pour améliorer les performances
            $table->index(['resource_type', 'resource_id']);
            $table->index('token');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liens_partage');
    }
};
