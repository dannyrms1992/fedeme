<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('accent_color', 7)->default('#F59E0B')->after('secondary_color');
            $table->string('bg_color', 7)->default('#F8FAFC')->after('accent_color');
            $table->string('surface_color', 7)->default('#FFFFFF')->after('bg_color');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['accent_color', 'bg_color', 'surface_color']);
        });
    }
};
