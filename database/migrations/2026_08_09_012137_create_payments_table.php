<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('amount', 10, 2);

            $table->enum('payment_method', [
                'cash',
                'wave',
                'orange_money',
                'bank',
                'other',
            ]);

            $table->string('reference')->nullable();

            $table->date('payment_date');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};