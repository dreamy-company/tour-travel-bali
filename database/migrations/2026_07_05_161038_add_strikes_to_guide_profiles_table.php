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
        Schema::table('guide_profiles', function (Blueprint $table) {
            $table->unsignedInteger('strikes')->default(0)->after('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_profiles', function (Blueprint $table) {
            $table->dropColumn('strikes');
        });
    }
};
