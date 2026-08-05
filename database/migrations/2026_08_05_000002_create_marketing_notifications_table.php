<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_notifications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('id_marketing');
            $table->string('title', 150);
            $table->text('message');
            $table->string('type')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('id_marketing')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_notifications');
    }
};
