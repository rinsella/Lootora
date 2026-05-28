<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'description')) {
                $table->text('description')->nullable()->after('currency');
            }
            if (!Schema::hasColumn('payments', 'min_withdrawal')) {
                $table->decimal('min_withdrawal', 16, 4)->nullable()->after('description');
            }
            if (!Schema::hasColumn('payments', 'fee_percentage')) {
                $table->decimal('fee_percentage', 5, 2)->default(0)->after('min_withdrawal');
            }
            if (!Schema::hasColumn('payments', 'fixed_fee')) {
                $table->decimal('fixed_fee', 16, 4)->default(0)->after('fee_percentage');
            }
            if (!Schema::hasColumn('payments', 'account_label')) {
                $table->string('account_label')->nullable()->after('fixed_fee');
            }
            if (!Schema::hasColumn('payments', 'instructions')) {
                $table->text('instructions')->nullable()->after('account_label');
            }
            if (!Schema::hasColumn('payments', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('instructions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            foreach (['description','min_withdrawal','fee_percentage','fixed_fee','account_label','instructions','sort_order'] as $col) {
                if (Schema::hasColumn('payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
