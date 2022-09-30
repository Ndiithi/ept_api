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
        Schema::create('schemes', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            $table->string('name');
            $table->string('description');
            $table->uuid('program');
            $table->json('meta');
            $table->timestamps();
            $table->softDeletes();
            // $table->integer("sample");
            $table->string('scoringCriteria');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schemes');
    }
};
