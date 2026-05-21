<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('borrowing_id')->nullable();
            $table->decimal('amount', 8, 2);
            $table->string('reason');
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
        });

        Schema::table('fines', function (Blueprint $table) {
            $table->foreign('borrowing_id')->references('id')->on('borrowings')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
