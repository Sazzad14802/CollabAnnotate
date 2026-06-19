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
            $table->foreignId('dataset_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('row_index'); // original row order
            $table->json('data');               // {"text":"...", "source":"..."}
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['unassigned', 'in_progress', 'completed'])->default('unassigned');
            $table->timestamps();

            $table->index(['dataset_id', 'status']);
            $table->index(['dataset_id', 'assigned_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dataset_rows');
    }
};
