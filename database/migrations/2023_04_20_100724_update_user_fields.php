<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->enum('status', [
                'STATUS_ACTIVE',
                'STATUS_INACTIVE',
                'STATUS_NOT_VERIFIED',
                'STATUS_DELETED',
            ])->default('STATUS_NOT_VERIFIED')->nullable(false)->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
