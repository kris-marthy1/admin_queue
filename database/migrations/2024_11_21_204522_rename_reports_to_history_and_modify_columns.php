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
        // Rename the table from 'reports' to 'history'
        Schema::rename('reports', 'history');

        // Modify columns
        Schema::table('history', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->string('school_id')->after('report_id'); // Add 'school_id'
            $table->string('email')->after('school_id'); // Add 'email'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename the table from 'history' back to 'reports'
        // Schema::rename('history', 'reports');

        // Revert column changes
        // Schema::table('reports', function (Blueprint $table) {
        //     $table->dropColumn('school_id');
        //     $table->dropColumn('email');
        //     $table->string('first_name')->after('report_id'); // Add 'first_name' back
        //     $table->string('last_name')->after('first_name'); // Add 'last_name' back
        // });
    }
};
