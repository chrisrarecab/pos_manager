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
        Schema::create('superadmin_tool_flags', function (Blueprint $table) {
            $table->id();
            $table->integer('clientgroupid')->default('1000');
            $table->integer('networkid')->default('1000');
            $table->integer('branchid')->default('10');
            $table->integer('terminalno')->default('0');
            $table->enum('type',['1','2','3']);
            $table->boolean('value')->default(true);
            $table->date('vatchange_date')->default('2000-01-01');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('superadmin_tool_flags');
    }
};
