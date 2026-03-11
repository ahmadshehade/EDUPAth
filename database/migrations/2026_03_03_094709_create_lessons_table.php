<?php

use App\Enums\LessonType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('sections')
                ->onDelete('cascade');
            $table->json('title');
            $table->longText('content')->nullable();
            $types = array_map(fn($case) => $case->value, LessonType::cases());
            $table->enum('type', $types);
            $table->string('live_url')->nullable();
            $table->datetime('live_start_at')->nullable();
            $table->datetime('live_end_at')->nullable();
            $table->enum('live_status', ['upcoming', 'live', 'ended'])->default('upcoming');
            $table->unsignedInteger('order');
            $table->timestamps();

            $table->unique(['section_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('lessons');
    }
};
