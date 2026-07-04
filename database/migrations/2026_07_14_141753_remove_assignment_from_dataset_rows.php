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
        Schema::table('dataset_rows', function (Blueprint $table) {
            // These were already dropped in previous failed attempts
            // $table->dropForeign(['assigned_to']);
            // $table->dropIndex(['dataset_id', 'status']);

            // Add index for dataset_id so the foreign key isn't left without one
            $table->index('dataset_id');
            
            $table->dropIndex(['dataset_id', 'assigned_to']);
            $table->dropColumn(['assigned_to', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dataset_rows', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['unassigned', 'in_progress', 'completed'])->default('unassigned');
            $table->index(['dataset_id', 'status']);
            $table->index(['dataset_id', 'assigned_to']);
        });
    }
};
