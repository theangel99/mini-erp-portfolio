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
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->integer('sort')->default(0);
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('planned_at');
            $table->dateTime('completed_at')->nullable();
            $table->string('status');
            $table->string('waiting_on')->nullable();
            $table->text('waiting_note')->nullable();
            $table->dateTime('waiting_since')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
