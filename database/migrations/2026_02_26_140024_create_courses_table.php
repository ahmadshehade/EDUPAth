<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')
                ->onDelete('cascade');
            $table->json('title');
            $table->string('slug')->unique();
            $table->json('description');
            $table->decimal('price', 12, 2)->default(0);
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('duration_hours')->nullable();
            $table->json('level');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('courses');
    }
};
