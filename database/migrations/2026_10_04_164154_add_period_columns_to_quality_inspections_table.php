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
        Schema::table('quality_inspections', function (Blueprint $table) {
            $table->unsignedSmallInteger('inspection_year')->nullable()->after('inspector_id');
            $table->unsignedTinyInteger('inspection_month')->nullable()->after('inspection_year');
            $table->unsignedTinyInteger('inspection_week')->nullable()->after('inspection_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quality_inspections', function (Blueprint $table) {
            $table->dropColumn(['inspection_year', 'inspection_month', 'inspection_week']);
        });
    }
};
