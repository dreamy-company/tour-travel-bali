<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Document file columns become nullable so admins can delete a guide's
 * KYC documents (revoking verification) without dropping the profile.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('guide_profiles', function (Blueprint $table) {
            $table->string('ktp_photo')->nullable()->change();
            $table->string('ktpp_file')->nullable()->change();
            $table->string('skck_file')->nullable()->change();
            $table->string('surat_sehat_file')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_profiles', function (Blueprint $table) {
            $table->string('ktp_photo')->nullable(false)->change();
            $table->string('ktpp_file')->nullable(false)->change();
            $table->string('skck_file')->nullable(false)->change();
            $table->string('surat_sehat_file')->nullable(false)->change();
        });
    }
};
