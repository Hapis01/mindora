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
        Schema::create('encryption_metadata', function (Blueprint $table) {
            $table->id();
            $table->string('key_version', 32)->unique();
            $table->string('algorithm', 32)->default('AES-256-GCM');
            $table->string('status', 32)->default('active'); // active, deprecated, revoked
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encryption_metadata');
    }
};
