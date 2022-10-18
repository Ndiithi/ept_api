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
        Schema::create('form_responses', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            // $table->uuid('user');
            // $table->uuid('form');
            // $table->uuid('round')->nullable();
            $table->uuid('form_submission');
            $table->uuid('form_section')->nullable();
            $table->uuid('form_field');
            $table->json('meta')->nullable();
            $table->json('value');
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
        Schema::dropIfExists('form_responses');
    }
};
