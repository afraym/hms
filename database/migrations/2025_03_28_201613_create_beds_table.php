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
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->string('bed_number')->unique();
            $table->string('room_number');
            $table->enum('status', ['متاح', 'محجوز', 'صيانة'])->default('متاح');
            $table->string('department')->nullable(); // القسم
            if (Schema::hasTable('patients')) {
                $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            }
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beds');
    }
};
