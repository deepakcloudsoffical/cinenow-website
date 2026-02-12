<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWatchProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('watch_progress')) {
            Schema::create('watch_progress', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('entertainment_id')->index(); // movie_id
                $table->string('entertainment_type')->nullable(); // movie, series
                $table->unsignedBigInteger('ticket_id')->nullable()->index();
                $table->integer('watched_percentage')->default(0);
                $table->integer('last_time_seconds')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('watch_progress');
    }
}
