<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToWatchProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('watch_progress')) {
            Schema::table('watch_progress', function (Blueprint $table) {
                if (!Schema::hasColumn('watch_progress', 'last_time_seconds')) {
                    $table->integer('last_time_seconds')->default(0);
                }
                if (!Schema::hasColumn('watch_progress', 'ticket_id')) {
                    $table->unsignedBigInteger('ticket_id')->nullable()->index();
                }
                if (!Schema::hasColumn('watch_progress', 'entertainment_type')) {
                    $table->string('entertainment_type')->nullable();
                }
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
        Schema::table('watch_progress', function (Blueprint $table) {
            //
        });
    }
}
