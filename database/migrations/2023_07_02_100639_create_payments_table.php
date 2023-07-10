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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->morphs('for');
            $table->string('payment_id')->unique()->nullable();
            $table->string('payment_mode');
            $table->text('meta')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('status');
            $table->unsignedBigInteger('payment_done_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
