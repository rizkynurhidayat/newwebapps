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
        Schema::create('defect_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('defect_category_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('severity')->default('minor'); // minor, major, critical
            $table->string('default_5m_category')->default('machine'); // man, machine, method, material, measurement, environment
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defect_types');
    }
};
