<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->string('name', 255);
            $table->string('slug', 100)->unique();
            $table->string('subdomain', 63)->unique()->comment('Used as tenant discriminator');
            $table->text('description')->nullable();
            $table->string('logo_path', 500)->nullable();
            $table->string('primary_color', 7)->default('#1a4f8a');
            $table->string('secondary_color', 7)->default('#c0392b');

            // Status: draft | active | inactive
            $table->string('status', 20)->default('draft')->index();

            // Access code gate
            $table->boolean('access_enabled')->default(false);
            $table->string('access_code_hash', 255)->nullable();
            $table->timestamp('access_expires_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Search optimization
            $table->index(['status', 'subdomain']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
