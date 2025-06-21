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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nom')->after('name');
            $table->string('prenom')->after('nom');
            $table->enum('role',['PDG','SECRETAIRE','EMPLOYE'])->default('EMPLOYE')->after('prenom');
            $table->dropColum('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->dropColum(['nom','prenom','role']);
        });
    }
};
