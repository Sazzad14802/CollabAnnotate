<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dataset_row_id')->constrained('dataset_rows')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('annotation_field_id')->constrained('annotation_fields')->cascadeOnDelete();
            $table->text('value')->nullable();
            $table->timestamps();

            // One annotation per user per row per field
            $table->unique(['project_id', 'dataset_row_id', 'user_id', 'annotation_field_id'], 'annotations_unique');
            $table->index(['project_id', 'dataset_row_id']);
            $table->index(['user_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annotations');
    }
};
