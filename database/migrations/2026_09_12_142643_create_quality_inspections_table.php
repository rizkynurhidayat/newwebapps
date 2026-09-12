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
        Schema::create('quality_inspections', function (Blueprint $table) {
            $table->id();
            $table->string('inspection_number')->unique()->index();
            $table->foreignId('production_batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inspector_id')->constrained('users')->restrictOnDelete();
            $table->dateTime('inspection_time');
            $table->string('inspection_stage')->default('in_process'); // incoming, in_process, final_qa
            $table->unsignedInteger('sample_size_inspected'); // N
            $table->unsignedInteger('passed_qty');
            $table->unsignedInteger('defective_units_qty')->default(0);
            $table->unsignedInteger('total_defects_count')->default(0); // D
            $table->decimal('dpu', 10, 4)->default(0); // Defects Per Unit
            $table->decimal('dpo', 12, 6)->default(0); // Defects Per Opportunity
            $table->decimal('dpmo', 12, 2)->default(0); // Defects Per Million Opportunities
            $table->decimal('sigma_level', 4, 2)->default(0); // Sigma Level e.g. 3.85
            $table->decimal('yield_percentage', 6, 2)->default(0); // Yield %
            $table->string('result_status')->default('passed'); // passed, conditional, rejected
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_inspections');
    }
};
