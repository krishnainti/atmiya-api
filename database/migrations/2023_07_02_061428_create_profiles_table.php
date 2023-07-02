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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->string('reference_by');
            $table->string('reference_phone');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('marital_status');
            $table->string('gender');

            $table->text('address_line_1');
            $table->text('address_line_2')->nullable();
            $table->string('city');
            $table->unsignedBigInteger('state');
            $table->unsignedBigInteger('metro_area')->nullable();
            $table->string('zip_code');
            $table->string('country');

            $table->unsignedBigInteger('membership_category');

            $table->string('spouse_first_name')->nullable();
            $table->string('spouse_last_name')->nullable();
            $table->string('spouse_email')->nullable();
            $table->string('spouse_phone')->nullable();
            $table->json('family_members')->nullable();

            $table->string('payment_mode');

            $table->string('status'); // pending / payment_done / payment_failed / under_review / admin_approved / admin_rejected

            $table->foreign('user_id')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
