<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
        });

        Schema::table('patient_visits', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
        });

        // Add any other tables that reference patients
        // Example:
        // Schema::table('other_table', function (Blueprint $table) {
        //     $table->dropForeign(['patient_id']);
        // });
    }

    public function down()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->foreign('patient_id')->references('id')->on('patients');
        });

        Schema::table('patient_visits', function (Blueprint $table) {
            $table->foreign('patient_id')->references('id')->on('patients');
        });
    }
};