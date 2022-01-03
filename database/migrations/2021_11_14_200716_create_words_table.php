<?php

use App\Models\Dictionary;
use App\Models\User;
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
            $table->foreignIdFor(Dictionary::class);
            $table->foreignIdFor(User::class);
            $table->boolean('favorite')->default(false);
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
