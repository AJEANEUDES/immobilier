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
        Schema::create('users', function (Blueprint $table) {
           
            $table->bigIncrements('id_user');
            $table->string('nom_user', 100);
            $table->string('prenoms_user', 50);
            $table->string('email_user', 100)->unique();
            $table->string('prefixpays_user', 10)->nullable();
            $table->string('telephone_user', 15)->unique();
            $table->string('adresse_user', 500);
            $table->string('pays_user', 100)->nullable();
            $table->string('ville_user', 100)->nullable();
            $table->string('image_user')->default("images/profile.jpg");
            $table->string('roles_user', 30);
            $table->boolean('status_user')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('verification_code')->nullable();
            // $table->integer('id_role')->unsigned()->nullable();
            $table->rememberToken();
            $table->timestamps();


            // $table->foreign('id_role')
            // ->references('id_role')
            // ->on('roles')
            // ->onUpdate('cascade')
            // ->onDelete('cascade');

           



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
