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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->text('encrypted_name')->nullable();
            $table->text('encrypted_dob')->nullable();
            $table->text('encrypted_phone')->nullable();
            $table->text('encrypted_address')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('avatar_path')->nullable();
            $table->string('dob_verification_hash')->nullable();
            $table->string('key_version', 32)->default('v1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
