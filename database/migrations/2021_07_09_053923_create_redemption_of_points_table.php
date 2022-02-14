<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRedemptionOfPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('redemption_of_points', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->text('store_ids');
            $table->text('earning_of_point_ids');
            $table->text('product_ids');
            $table->text('pct_data');
            $table->decimal('value_of_1000_points', 15, 4)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('created_by');
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
        Schema::dropIfExists('redemption_of_points');
    }
}
