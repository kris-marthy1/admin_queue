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
        Schema::create('history', function (Blueprint $table) {
            $table->id('report_id');
            $table->string('queue_id');
            $table->timestamp('arrived_at');

            $table->unsignedBigInteger('window_id')->nullable();
            
            // Add the foreign key constraint
            $table->foreign('window_id')
                  ->references('window_id')  // Note: referencing window_id, not id
                  ->on('window')
                  ->onDelete('set null')
                  ->onUpdate('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history');
    }
};
