<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pos_settings', function (Blueprint $table) {
            $table->string('uuid');
            $table->integer('source')->default('1');
            $table->string('key', 128)->default('');
            $table->string('value', 1024)->nullable();
            $table->dateTime('datemodified')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->primary(['uuid', 'source', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_setting');
    }
};
