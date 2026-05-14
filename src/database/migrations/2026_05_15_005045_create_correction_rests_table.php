<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorrectionRestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correction_rests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_correction_id')
                ->constrained('correction_attendances')
                ->cascadeOnDelete();
            $table->time('requested_break_in');
            $table->time('requested_break_out');
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
        Schema::dropIfExists('correction_rests');
    }
}
