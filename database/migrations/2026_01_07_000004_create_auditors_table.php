<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('specialization')->nullable();
            $table->string('certification')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('performance_score', 5, 2)->default(0)
                ->comment('Auto-calculated from reviews (0-100)');
            $table->integer('total_completed_projects')->default(0);
            $table->decimal('average_completion_days', 5, 2)->nullable();
            $table->timestamps();
            
            $table->index('performance_score');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditors');
    }
};
