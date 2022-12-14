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
        Schema::create('round__forms', function (Blueprint $table) {
            $table->id();
            $table->uuid('round');
            $table->uuid('form');
            $table->string('type')->default('pre'); // pre, response, post
            $table->boolean('is_mandatory')->default(false);
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
        Schema::dropIfExists('round__forms');
    }
};
