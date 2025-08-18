<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('student_id');
            $table->string('email');
            $table->string('phone');
            $table->string('semester');
            $table->string('batch');
            $table->string('tracker_url')->nullable();
            $table->integer('total_solved')->nullable();
            $table->json('courses');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
