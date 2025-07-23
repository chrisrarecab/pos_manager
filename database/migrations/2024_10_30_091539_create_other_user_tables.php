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
        Schema::create('user_branch', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('user_id')->index();
            $table->integer('client_branch_id')->index();
        });

        Schema::create('user_permission', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->integer('code')->index();
        });

        Schema::create('user_permission_codes', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->index();
            $table->string('category');
            $table->string('description');
        });

        Schema::create('source_project', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
        });

        DB::table('source_project')->insert(
            [
                ['name' => 'Core'],
                ['name' => 'CIRMS']
            ]
        );

        DB::table('user_permission_codes')->insert(
            [
                ['code' => '101', 'category' => 'Settings', 'description' => 'View'],
                ['code' => '102', 'category' => 'Settings', 'description' => 'Add'],
                ['code' => '103', 'category' => 'Settings', 'description' => 'Edit'],
                ['code' => '104', 'category' => 'Settings', 'description' => 'Delete'],
                ['code' => '201', 'category' => 'User', 'description' => 'View'],
                ['code' => '202', 'category' => 'User', 'description' => 'Add'],
                ['code' => '203', 'category' => 'User', 'description' => 'Edit'],
                ['code' => '204', 'category' => 'User', 'description' => 'Delete'],
                ['code' => '205', 'category' => 'User', 'description' => 'Approve'],
                ['code' => '301', 'category' => 'Audit Trail', 'description' => 'View'],
                ['code' => '302', 'category' => 'Audit Trail', 'description' => 'Add'],
                ['code' => '303', 'category' => 'Audit Trail', 'description' => 'Edit'],
                ['code' => '304', 'category' => 'Audit Trail', 'description' => 'Delete']
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_branch');
        Schema::dropIfExists('user_permission');
        Schema::dropIfExists('user_permission_codes');
        Schema::dropIfExists('source_project');
    }
};
