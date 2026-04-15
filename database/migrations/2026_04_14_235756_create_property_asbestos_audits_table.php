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
        Schema::create('property_asbestos_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->foreign('property_id')->references('id')->on('customer_properties')->cascadeOnDelete();
            $table->date('audit_date')->nullable();
            $table->string('audit_hours')->nullable();
            $table->string('job_type')->nullable();
            $table->string('labelling_status')->nullable();
            $table->string('qr_number')->nullable();
            $table->string('installation_status')->nullable();
            $table->string('lead_status')->nullable();
            $table->string('samples_taken')->nullable();
            $table->string('smf_status')->nullable();
            $table->text('smf_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_asbestos_audits');
    }
};
