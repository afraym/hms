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
        Schema::create('patient_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['in', 'out']);
            $table->dateTime('visit_at');
            $table->text('notes')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('bed_id')->nullable()->constrained('beds')->nullOnDelete();
            $table->string('companion_name')->nullable();
            $table->string('companion_relation')->nullable();
            $table->string('companion_phone')->nullable();
            $table->string('companion_national_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_visits');
    }
};
