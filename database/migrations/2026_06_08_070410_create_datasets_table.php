<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('original_filename');
            $table->json('column_names'); // array of original column headers
            $table->unsignedInteger('row_count')->default(0);
            $table->string('file_path')->nullable();
            $table->enum('import_status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('import_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datasets');
    }
};
