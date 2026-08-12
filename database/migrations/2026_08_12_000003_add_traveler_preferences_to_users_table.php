<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Traveler persona preferences on the customer account, used to
 * improve matching accuracy (SRS: same interest, same vibe).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('traveler_preferences')->nullable()->after('status')
                ->comment("Array: introvert, cafe_hopper, photography_enthusiast, adventurer, culture_lover, night_owl, foodie, wellness_seeker");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('traveler_preferences');
        });
    }
};
