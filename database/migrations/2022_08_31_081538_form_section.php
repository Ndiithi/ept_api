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

        Schema::create('form_sections', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            $table->uuid('form');
            $table->string('name');
            $table->string('description')->nullable();
            $table->json('meta')->nullable();
            $table->json('actions')->nullable();
            $table->integer('index')->nullable();
            // $table->string('next')->nullable();
            // $table->boolean('next_condition')->nullable();
            $table->boolean('disabled');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_sections');
    }
};
