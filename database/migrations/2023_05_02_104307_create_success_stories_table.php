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
        if (!Schema::hasTable('success_stories')) {
            Schema::create('success_stories', static function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->string('title', 100);
                $table->boolean('active');
                $table->string('preview_image', 255)->nullable();
                $table->json('industry');
                $table->json('technologies');
                $table->json('company');
                $table->json('steps');
                $table->json('project');
                $table->json('similar_cases')->nullable();
                $table->softDeletes();
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('success_stories');
    }
};
