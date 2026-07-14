<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dataset_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('row_index');
            $table->json('data');
            $table->timestamps();

            $table->index(['project_id', 'row_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dataset_rows');
    }
};
