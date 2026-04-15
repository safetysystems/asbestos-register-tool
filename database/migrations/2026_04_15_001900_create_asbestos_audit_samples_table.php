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
        Schema::create('asbestos_audit_samples', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audit_id');
            $table->foreign('audit_id')->references('id')->on('property_asbestos_audits')->cascadeOnDelete();
            $table->string('sample_number')->nullable();
            $table->string('building_area')->nullable();
            $table->string('location')->nullable();
            $table->string('surface')->nullable();
            $table->string('material')->nullable();
            $table->string('hazardous_material')->nullable();
            $table->float('quantity', 10, 2)->nullable();
            $table->string('condition')->nullable();
            $table->string('access_level')->nullable();
            $table->string('friability')->nullable();
            $table->string('hazard_priority')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asbestos_audit_samples');
    }
};
