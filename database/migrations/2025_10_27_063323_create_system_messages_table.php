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
        Schema::create('system_messages', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['user_join', 'user_leave', 'room_created', 'settings_changed', 'user_promoted', 'user_demoted', 'user_banned', 'user_unbanned', 'other'])->default('other');
            $table->string('content');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_messages');
    }
};
