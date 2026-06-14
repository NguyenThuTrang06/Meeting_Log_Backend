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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('week')->nullable();
            $table->string('meeting_date')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('project_id')->nullable();
            $table->string('team')->nullable();
            $table->string('leader')->nullable();
            $table->string('name')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->string('video_link')->nullable();
            $table->text('short_summary')->nullable();
            $table->text('overview')->nullable();
            $table->text('action_items')->nullable();
            $table->text('decisions')->nullable();
            $table->text('issues')->nullable();
            $table->text('next_steps')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
