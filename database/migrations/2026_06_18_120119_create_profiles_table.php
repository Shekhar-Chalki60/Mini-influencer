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
        Schema::create('profiles', function (Blueprint $table) {

            $table->id();

            $table->string('username')->unique();

            $table->string('status')
                ->default('pending');

            $table->unsignedBigInteger('followers_count')
                ->nullable();

            $table->unsignedBigInteger('following_count')
                ->nullable();

            $table->unsignedBigInteger('posts_count')
                ->nullable();

            $table->string('profile_picture_url')
                ->nullable();

            $table->longText('bio')->nullable();

            $table->text('last_error')
                ->nullable();

            $table->timestampTz('last_refreshed_at')
                ->nullable();

            $table->timestampsTz();

                $table->index([
                    'status',
                    'last_refreshed_at'
                ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
