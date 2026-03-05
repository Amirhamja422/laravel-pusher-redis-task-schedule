<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('failed_imports', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('upload_history_id');

            $table->string('name')->nullable();
            $table->string('email')->nullable();

            $table->text('reason')->nullable();

            $table->timestamps();

            // optional foreign key
            $table->foreign('upload_history_id')
                  ->references('id')
                  ->on('upload_histories')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_imports');
    }
};
