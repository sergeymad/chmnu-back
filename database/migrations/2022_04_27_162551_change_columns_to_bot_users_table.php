<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnsToBotUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bot_users', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('gender')->nullable()->change();
            $table->string('full_path')->nullable()->change();
            $table->string('relative_path')->nullable()->change();
            $table->string('current_step')->nullable()->change();
            $table->integer('chat_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bot_users', function (Blueprint $table) {
            //
        });
    }
}
