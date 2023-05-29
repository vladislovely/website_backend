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
        if (!Schema::hasTable('mails')) {
            Schema::create('mails', static function (Blueprint $table) {
                $table->id();
                $table->string('theme', 50);
                $table->string('from', 50);
                $table->string('to', 50);
                $table->string('username', 50);
                $table->string('company', 50);
                $table->string('phone', 50);
                $table->text('text');
                $table->json('attachments');
                $table->boolean('is_success_sent');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mails');
    }
};
