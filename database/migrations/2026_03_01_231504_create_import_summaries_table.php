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
        Schema::create('import_summaries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('upload_history_id');

            $table->integer('total_rows')->default(0);
            $table->integer('total_success')->default(0);
            $table->integer('total_duplicate')->default(0);
            $table->integer('total_failed')->default(0);
            $table->integer('total_exist')->default(0);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_summaries');
    }
};
