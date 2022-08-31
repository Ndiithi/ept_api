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
        Schema::create('shipment', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->uuid('round');
            $table->string('name');
            $table->uuid('regions');
            $table->string('courier');
            $table->string('tracking_number');
            $table->boolean('received');
            $table->date('shipped_on');
            $table->date('received_on');
            $table->string('description');
            $table->json('meta');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipment');
    }
};
