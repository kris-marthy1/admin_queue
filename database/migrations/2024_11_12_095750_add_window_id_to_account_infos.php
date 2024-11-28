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
        Schema::table('account_infos', function (Blueprint $table) {
            // Add the window_id column
            $table->unsignedBigInteger('window_id')->nullable()->after('account_id');
            
            // Add the foreign key constraint
            $table->foreign('window_id')
                  ->references('window_id')  // Note: referencing window_id, not id
                  ->on('window')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_infos', function (Blueprint $table) {
            $table->dropForeign(['window_id']);
            $table->dropColumn('window_id');
        });
    }
};