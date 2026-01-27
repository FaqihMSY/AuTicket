<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('status', ['DRAFT', 'PUBLISHED', 'ON_PROGRESS', 'WAITING', 'CLOSED'])
                ->default('DRAFT')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('status', ['DRAFT', 'ON_PROGRESS', 'WAITING', 'CLOSED'])
                ->default('DRAFT')
                ->change();
        });
    }
};
