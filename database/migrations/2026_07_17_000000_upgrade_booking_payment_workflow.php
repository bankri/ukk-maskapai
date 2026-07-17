<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->timestamp('seats_released_at')->nullable();
        });

        Schema::table('passengers', function (Blueprint $table) {
            $table->string('identity_type', 20)->nullable();
            $table->string('identity_number', 64)->nullable();
            $table->index(['seat_number']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('order_id')->nullable()->unique();
            $table->text('snap_token')->nullable();
            $table->text('redirect_url')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('fraud_status')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('raw_notification')->nullable();
        });

        Schema::create('booking_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('note')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_status_histories');

        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['order_id']);
            $table->dropColumn([
                'order_id',
                'snap_token',
                'redirect_url',
                'transaction_id',
                'transaction_status',
                'fraud_status',
                'paid_at',
                'raw_notification',
            ]);
        });

        Schema::table('passengers', function (Blueprint $table) {
            $table->dropIndex(['seat_number']);
            $table->dropColumn(['identity_type', 'identity_number']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['approved_at', 'rejected_reason', 'seats_released_at']);
        });
    }
};
