<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_action_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->index();
            $table->unsignedBigInteger('target_user_id')->nullable()->index();
            $table->string('action', 64);              // e.g. ban_user, adjust_points, approve_withdrawal
            $table->string('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 64)->nullable();
            $table->timestamps();
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            if (!Schema::hasColumn('withdrawals', 'admin_note')) {
                $table->string('admin_note')->nullable()->after('status');
            }
            if (!Schema::hasColumn('withdrawals', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('admin_note');
            }
            if (!Schema::hasColumn('withdrawals', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('processed_at');
            }
        });

        // Money-safe upgrade for withdrawal amount.
        try {
            \DB::statement('ALTER TABLE withdrawals MODIFY amount DECIMAL(16,4) NOT NULL DEFAULT 0');
        } catch (\Throwable $e) {
            // ignore
        }

        // Extend status enum to include paid/cancelled.
        try {
            \DB::statement("ALTER TABLE withdrawals MODIFY status ENUM('pending','approved','rejected','paid','cancelled') NOT NULL DEFAULT 'pending'");
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_action_logs');
        Schema::table('withdrawals', function (Blueprint $table) {
            foreach (['admin_note','processed_at','paid_at'] as $col) {
                if (Schema::hasColumn('withdrawals', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
