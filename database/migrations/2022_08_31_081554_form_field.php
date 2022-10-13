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
        Schema::create('form_fields', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            $table->uuid('form_section');
            $table->string('name');
            $table->string('type');
            $table->string('description');
            $table->json('meta');
            $table->json('actions')->nullable();
            $table->json('validation')->nullable();
            $table->index('index');
            $table->boolean('disabled')->default(false);
            $table->string('options')->nullable(); // reference to dictionary entry
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
        Schema::dropIfExists('form_fields');
    }
};
