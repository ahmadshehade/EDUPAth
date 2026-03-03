<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->json('title'); 
            $table->string('slug')->nullable();
            $table->json('description')->nullable();
            $table->unsignedInteger('order')->default(1);
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger(column: 'duration')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['course_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('sections');
    }
};
