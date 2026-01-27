<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users');
            $table->foreignId('reviewee_id')->constrained('users');
            $table->tinyInteger('overall_rating')->comment('1-100');
            $table->tinyInteger('timeliness_rating')->nullable()->comment('1-100');
            $table->tinyInteger('completeness_rating')->nullable()->comment('1-100');
            $table->tinyInteger('quality_rating')->nullable()->comment('1-100');
            $table->tinyInteger('communication_rating')->nullable()->comment('1-100');
            $table->text('feedback')->nullable();
            $table->timestamps();
            
            $table->unique(['project_id', 'reviewee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
