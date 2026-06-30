<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annotation_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');         // e.g. "Sentiment"
            $table->string('slug');         // e.g. "sentiment"
            $table->enum('type', ['select', 'checkbox'])->default('select');
            $table->json('options')->nullable(); // ["Positive","Neutral","Negative"]
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['project_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annotation_fields');
    }
};
