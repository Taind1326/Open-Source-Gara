<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_slots', function (Blueprint $table) {
            $table->id();

            $table->date('booking_date');
            $table->string('time_slot', 20);

            $table->unique(
                ['booking_date', 'time_slot'],
                'booking_slots_date_time_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_slots');
    }
};