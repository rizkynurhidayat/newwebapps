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
        Schema::create('production_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique()->index();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('production_line_id')->constrained()->restrictOnDelete();
            $table->foreignId('supervisor_id')->constrained('users')->restrictOnDelete();
            $table->date('production_date')->index();
            $table->string('shift')->default('Shift 1');
            $table->unsignedInteger('target_qty');
            $table->unsignedInteger('actual_qty')->default(0);
            $table->string('status')->default('draft'); // draft, in_production, completed, cancelled
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
        Schema::dropIfExists('production_batches');
    }
};
