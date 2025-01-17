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
            Schema::table('account_infos', function (Blueprint $table) {
                $table->dropColumn('account_role');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_infos', function (Blueprint $table) {
            Schema::table('account_infos', function (Blueprint $table) {
                $table->dropColumn('account_role'); // Adjust the column type as necessary
            });
        });
    }
};
