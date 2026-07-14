<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // owner
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'completed', 'archived'])->default('active');
            $table->unsignedInteger('chunk_size')->default(10);
            // Absorbed from the old datasets table
            $table->string('original_filename')->nullable();
            $table->json('column_names')->nullable();
            $table->unsignedInteger('row_count')->default(0);
            $table->string('file_path')->nullable();
            $table->enum('import_status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('import_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
