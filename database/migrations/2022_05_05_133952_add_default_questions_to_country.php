<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultQuestionsToCountry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bot_country', function (Blueprint $table) {
            $table->longText('question_0')->nullable();
            $table->longText('question_1')->nullable();
            $table->longText('question_2')->nullable();
            $table->longText('question_3')->nullable();
            $table->longText('question_4')->nullable();
            $table->longText('question_5')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bot_country', function (Blueprint $table) {
            $table->dropColumn('question_0');
            $table->dropColumn('question_1');
            $table->dropColumn('question_2');
            $table->dropColumn('question_3');
            $table->dropColumn('question_4');
            $table->dropColumn('question_5');

        });
    }
}
