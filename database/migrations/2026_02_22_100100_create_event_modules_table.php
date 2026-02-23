<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_modules', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('event_id')->index();
            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();

            // Module type: hero | info | pdf | contact | map
            $table->string('type', 50);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order')->default(0);

            // Flexible module configuration (title, content, url, etc.)
            $table->json('settings')->nullable();

            $table->timestamps();

            $table->unique(['event_id', 'type']);
            $table->index(['event_id', 'is_active', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_modules');
    }
};
