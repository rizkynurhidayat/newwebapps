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
        Schema::create('capa_actions', function (Blueprint $table) {
            $table->id();
            $table->string('capa_number')->unique()->index();
            $table->foreignId('defect_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('assigned_to_user_id')->constrained('users')->restrictOnDelete();
            $table->string('title');
            $table->text('problem_statement'); // Define
            $table->text('root_cause_analysis'); // Analyze (5-Why)
            $table->text('corrective_action'); // Improve
            $table->text('preventive_action'); // Control
            $table->date('target_completion_date');
            $table->date('actual_completion_date')->nullable();
            $table->string('status')->default('open'); // open, in_progress, implemented, verified, closed
            $table->text('verification_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capa_actions');
    }
};
