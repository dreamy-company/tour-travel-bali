<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Aligns the schema with the SRS document:
 * - guide_profiles: add communication_style + specializations (FR-02-01 matching parameters)
 * - guide_profiles: enforce unique NIK (KTP) number per SRS
 * - chat_messages: add receiver_id and allow nullable booking_id for pre-booking chat (FR-02-02)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('guide_profiles', function (Blueprint $table) {
            $table->string('communication_style')->nullable()->after('bio')
                ->comment("Enum: santai, edukatif, profesional, ekspresif");
            $table->json('specializations')->nullable()->after('communication_style')
                ->comment("Array: cafe_hopping, photography, nightlife, nature, culture_history, healing");
            $table->unique('ktp_number', 'guide_profiles_ktp_number_unique');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->foreignId('booking_id')->nullable()->change();
            $table->foreignId('receiver_id')->nullable()->after('sender_id')->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['receiver_id']);
            $table->dropColumn('receiver_id');
            $table->foreignId('booking_id')->nullable(false)->change();
        });

        Schema::table('guide_profiles', function (Blueprint $table) {
            $table->dropUnique('guide_profiles_ktp_number_unique');
            $table->dropColumn(['communication_style', 'specializations']);
        });
    }
};
