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
        Schema::create('sauvegardes_cloud', function (Blueprint $table) {
            $table->id();
            $table->string('resource_type', 100);
            $table->bigInteger('resource_id');
            $table->string('cloud_provider', 100);
            $table->string('cloud_path', 500);
            $table->enum('status', ['PENDING', 'SUCCESS', 'FAILED'])->default('PENDING');
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index(['resource_type', 'resource_id']);
            $table->index('status');
            $table->index('cloud_provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sauvegardes_cloud');
    }
};
