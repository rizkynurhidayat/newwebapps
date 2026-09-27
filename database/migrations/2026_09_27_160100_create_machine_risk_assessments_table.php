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
        Schema::create('machine_risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('hazard_code')->unique()->index();
            $table->string('hazard_name');
            $table->string('machine_area')->default('Mesin Stamping Press');
            $table->text('risk_description');
            $table->unsignedTinyInteger('likelihood'); // 1-5
            $table->unsignedTinyInteger('severity');   // 1-5
            $table->unsignedTinyInteger('risk_score');  // likelihood * severity (1-25)
            $table->string('risk_level')->index();      // low, medium, high, extreme
            $table->text('control_measures')->nullable();
            $table->string('pic')->nullable();
            $table->string('status')->default('active'); // active, controlled, closed
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_risk_assessments');
    }
};
