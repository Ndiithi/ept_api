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
            $table->uuid('uuid');
            $table->uuid('form');
            $table->string('name');
            $table->string('description');
            $table->json('meta');
            $table->json('actions');
            $table->string('next');
            $table->boolean('next_condition');
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
