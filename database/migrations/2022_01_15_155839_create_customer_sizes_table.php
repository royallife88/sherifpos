<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerSizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_sizes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('name')->nullable();
            $table->string('yoke')->nullable();
            $table->string('neck_round')->nullable();
            $table->string('neck_width')->nullable();
            $table->string('neck_deep')->nullable();
            $table->string('front_neck')->nullable();
            $table->string('back_neck')->nullable();
            $table->string('upper_bust')->nullable();
            $table->string('bust')->nullable();
            $table->string('low_bust')->nullable();
            $table->string('shoulder_er')->nullable();
            $table->string('arm_hole')->nullable();
            $table->string('arm_round')->nullable();
            $table->string('wrist_round')->nullable();
            $table->string('lenght_of_sleeve')->nullable();
            $table->string('waist')->nullable();
            $table->string('low_waist')->nullable();
            $table->string('hips')->nullable();
            $table->string('thigh')->nullable();
            $table->string('knee_round')->nullable();
            $table->string('calf_round')->nullable();
            $table->string('ankle')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

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
        Schema::dropIfExists('customer_sizes');
    }
}
