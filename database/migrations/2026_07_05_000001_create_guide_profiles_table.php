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
        Schema::create('guide_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('ktp_number');
            $table->string('ktp_photo');
            $table->string('ktpp_number')->comment('Lisensi resmi HPI');
            $table->string('ktpp_file');
            $table->date('ktpp_expired_at');
            $table->string('skck_file');
            $table->date('skck_expired_at');
            $table->string('surat_sehat_file');
            $table->text('vehicle_details')->nullable();
            $table->text('bio')->nullable();
            $table->json('languages')->comment('Array of languages for filtering');
            $table->string('tariff_mode');
            $table->decimal('base_rate', 12, 2);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('signed_sop_at')->nullable()->comment('Digital Signature SOP');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_profiles');
    }
};
