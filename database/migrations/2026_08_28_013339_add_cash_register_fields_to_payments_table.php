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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->change();
            $table->string('type', 20)->default('inflow')->after('id');
            $table->string('category', 50)->nullable()->after('type');
            $table->string('title')->nullable()->after('category');
            $table->string('payment_method', 50)->change();
            $table->string('receipt_number', 50)->nullable()->unique()->after('notes');
            $table->foreignId('created_by')->nullable()->after('receipt_number')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'type',
                'category',
                'title',
                'receipt_number',
                'created_by',
            ]);
        });
    }
};
