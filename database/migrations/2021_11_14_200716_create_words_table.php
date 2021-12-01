<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('words', function (Blueprint $table) {
            $table->id();
            $table->string('word_foreign');
            $table->string('word_translation');
            $table->string('learning_status')->default('reviewing');
            $table->integer('times_reviewed')->unsigned()->default(0);
            $table->foreignId('dictionary_id');
            $table->foreignId('user_id');
            $table->integer('order_index')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('dictionary_id')->references('id')->on('dictionaries')->cascadeOnDelete();
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
        Schema::dropIfExists('words');
    }
}
