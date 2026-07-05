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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('guide_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tour_package_id')->nullable()->constrained('tour_packages')->onDelete('set null');
            $table->text('pickup_location');
            $table->text('dropoff_location')->nullable();
            $table->json('custom_destinations')->nullable();
            $table->date('schedule_date');
            $table->time('pickup_time');
            $table->decimal('total_price', 12, 2);
            $table->string('status')->default('pending_confirmation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
