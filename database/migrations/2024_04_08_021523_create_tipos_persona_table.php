<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('tipos_persona', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 8);
            $table->string('nombre', 60);
            $table->smallInteger('orden');
            $table->enum('estado', ['A', 'I', 'D'])->default('A')->comment('A: Activo, I: Inactivo, D: Eliminado');
            
            // $table->timestamps();
        });

        // Insertar datos iniciales en la tabla
        DB::table('tipos_persona')->insert([
            ['codigo' => '01', 'nombre' => 'Persona natural', 'orden' => 1, 'estado' => 'A'],
            ['codigo' => '02', 'nombre' => 'Persona jurídica', 'orden' => 2, 'estado' => 'A'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipos_persona');
    }
};
