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
        Schema::dropIfExists('post');
        Schema::create('post', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posterId')->constrained('users');
            $table->boolean('hasText');
            $table->boolean('hasPhoto');
            $table->Text('text')->nullable();
            $table->integer('likeTimes')->default(0);
            $table->integer('commentTimes')->default(0);
            $table->integer('shareTimes')->default(0);
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
        Schema::dropIfExists('post');
    }
};
