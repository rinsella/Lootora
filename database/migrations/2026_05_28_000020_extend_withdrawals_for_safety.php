<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            if (!Schema::hasColumn('withdrawals', 'payout_method_id')) {
                $table->unsignedBigInteger('payout_method_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('withdrawals', 'account_name')) {
                $table->string('account_name', 128)->nullable()->after('account');
            }
            if (!Schema::hasColumn('withdrawals', 'account_identifier')) {
                $table->string('account_identifier', 255)->nullable()->after('account_name');
            }
            if (!Schema::hasColumn('withdrawals', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
            if (!Schema::hasColumn('withdrawals', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable();
            }
            if (!Schema::hasColumn('withdrawals', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            foreach (['payout_method_id','account_name','account_identifier','approved_at','rejected_at','refunded_at'] as $col) {
                if (Schema::hasColumn('withdrawals', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
