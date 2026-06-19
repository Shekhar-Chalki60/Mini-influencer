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
        Schema::create('profile_snapshots', function (Blueprint $table) {

            $table->id();

            $table->foreignId('profile_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('followers_count');

            $table->unsignedBigInteger('following_count');

            $table->unsignedBigInteger('posts_count');

            $table->timestampTz('captured_at');

            $table->timestampsTz();
$table->index([
    'profile_id',
    'captured_at'
]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_snapshots');
    }
};
