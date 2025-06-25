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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable()->index();
            $table->string('second_name')->nullable();
            $table->string('third_name')->nullable();
            $table->string('fourth_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone2')->nullable();
            $table->string('national_id')->nullable()->unique(); // Make national_id nullable and unique
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('status')->default('waiting');
            $table->string('medical_id')->unique()->nullable(); // الرقم الطبي 
            $table->unsignedBigInteger('bed_id')->nullable();
            $table->string('uhi_number')->nullable()->unique(); // رقم التأمين الصحي الشامل
            $table->string('address')->nullable();
            $table->string('governorate')->nullable();
            $table->string('companion_name')->nullable();
            $table->string('companion_relation')->nullable();
            $table->string('companion_national_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            // Add department foreign key
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            
            // Additional fields
            $table->text('notes')->nullable();                    // ملاحظات عامة
            $table->string('blood_type')->nullable();            // فصيلة الدم
            $table->string('marital_status')->nullable();        // الحالة الاجتماعية
            $table->string('occupation')->nullable();            // المهنة
            $table->boolean('is_active')->default(true);         // حالة نشاط المريض
            $table->softDeletes();                              // يضيف حقل deleted_at للحذف الناعم
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
