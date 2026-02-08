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
        Schema::create('prompt_instructions', function (Blueprint $table) {
            $table->id();
            $table->string('language', 10)->default('es');
            $table->text('content');
            $table->boolean('editable')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('author_name')->nullable();
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
        Schema::dropIfExists('prompt_instructions');
    }
};
