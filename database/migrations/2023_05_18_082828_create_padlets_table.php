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
        Schema::create('padlets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');

            //Foreign key field
            $table->bigInteger('user_id')->unsigned();

            // constraint (Beziehung + Löschverhalten) - auf Datenbankebene
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('padlets');
    }
};
