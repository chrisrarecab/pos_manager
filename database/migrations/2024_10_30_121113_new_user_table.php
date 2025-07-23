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
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('username');
            $table->string('password')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->index();
            $table->datetime('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('last_modified_by');
            $table->datetime('last_modified_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->tinyInteger('is_deleted')->default(0);
            $table->tinyInteger('source_project_id')->index()->default(1);
            $table->integer('client_group_id')->default(0);
            $table->integer('client_network_id')->default(0);
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
