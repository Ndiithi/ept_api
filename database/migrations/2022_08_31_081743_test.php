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
        Schema::create('tests', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            $table->uuid('round');
            $table->uuid('schema');
            $table->string('name');
            $table->string('description');
            $table->json('meta');
            $table->string('overall_result');
            $table->string('target_code');
            $table->string('target_type');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /*
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tests');
    }
};
