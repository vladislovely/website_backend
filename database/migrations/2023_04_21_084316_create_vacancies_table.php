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
        Schema::create('vacancies', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('title', 100)->unique();
            $table->boolean('active');
            $table->text('announcement_text');
            $table->text('detail_text')->nullable();
            $table->string('detail_image', 200);
            $table->text('description');
            $table->boolean('remote_format');
            $table->json('conditions');
            $table->json('locations')->nullable();
            $table->json('language_level')->nullable();
            $table->json('grade')->nullable();
            $table->json('country')->nullable();
            $table->json('technologies');
            $table->json('specialisations');
            $table->json('offer_timeline')->nullable();
            $table->json('vacancy_type', 30);
            $table->json('work_schedule', 30);
            $table->json('type_of_employment', 30);
            $table->json('work_experience', 30);
            $table->json('salary')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
