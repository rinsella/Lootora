<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'referral_code')) {
                $table->string('referral_code', 32)->nullable()->unique()->after('email');
            }
            if (!Schema::hasColumn('users', 'referred_by')) {
                $table->unsignedBigInteger('referred_by')->nullable()->index()->after('referral_code');
            }
            if (!Schema::hasColumn('users', 'day_streak')) {
                $table->unsignedInteger('day_streak')->default(0)->after('total_points');
            }
            if (!Schema::hasColumn('users', 'best_streak')) {
                $table->unsignedInteger('best_streak')->default(0)->after('day_streak');
            }
            if (!Schema::hasColumn('users', 'last_checkin_at')) {
                $table->timestamp('last_checkin_at')->nullable()->after('best_streak');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'banned', 'suspicious'])->default('active')->after('last_checkin_at');
            }
            if (!Schema::hasColumn('users', 'kyc_status')) {
                $table->enum('kyc_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('status');
            }
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country', 4)->nullable()->after('kyc_status');
            }
        });

        // Promote point columns from FLOAT to DECIMAL(16,4) for money-safe storage.
        // Wrapped in try/catch to remain idempotent across MySQL versions.
        try {
            \DB::statement('ALTER TABLE users MODIFY current_points DECIMAL(16,4) NOT NULL DEFAULT 0');
            \DB::statement('ALTER TABLE users MODIFY today_points  DECIMAL(16,4) NOT NULL DEFAULT 0');
            \DB::statement('ALTER TABLE users MODIFY total_points  DECIMAL(16,4) NOT NULL DEFAULT 0');
        } catch (\Throwable $e) {
            // ignore — column types are best-effort upgrade
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['referral_code','referred_by','day_streak','best_streak','last_checkin_at','status','kyc_status','country'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
