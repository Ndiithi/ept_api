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
        Schema::create('samples', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            $table->uuid('round');
            $table->uuid('schema');
            $table->string('name');
            $table->string('description');
            $table->string('expected_outcome');
            $table->string('expected_outcome_notes');
            $table->string('expected_interpretation');
            $table->string('expected_interpretation_notes');
            $table->json('meta');
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
        Schema::dropIfExists('samples');
    }
};
