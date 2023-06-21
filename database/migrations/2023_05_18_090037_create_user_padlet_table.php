<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_padlet', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Verweist automatisch auf id, wenn nicht anders angegeben
            $table->foreignId('padlet_id')->constrained()->onDelete('cascade');

            $table->integer("role");

            // Rollen & Rechte
            // 1: nur lesen (Padlet und Einträge), aber sonst nichts
            // 2: lesen und Einträge erstellen (Eintrag erstellen, Kommentare erstellen, Rating abgeben)
            // 3: lesen und erstellen und bearbeiten (Einträge von anderen bearbeiten, Padletname ändern)
            // 4: lesen und erstellen und bearbeiten und löschen (Einträge und Padlet löschen), hat dann quasi Autorenrechte

            // User_id und padlet_id sind gemeinsam eindeutig
            $table->primary(['user_id','padlet_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_padlet');
    }
};
