<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('offerwalls', function (Blueprint $table) {
            if (!Schema::hasColumn('offerwalls', 'slug')) {
                $table->string('slug', 64)->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('offerwalls', 'description')) {
                $table->text('description')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('offerwalls', 'iframe_url_template')) {
                $table->text('iframe_url_template')->nullable()->after('iframe_url');
            }
            if (!Schema::hasColumn('offerwalls', 'postback_url')) {
                $table->string('postback_url')->nullable()->after('iframe_url_template');
            }
            if (!Schema::hasColumn('offerwalls', 'api_key')) {
                $table->string('api_key')->nullable()->after('postback_url');
            }
            if (!Schema::hasColumn('offerwalls', 'secret_key')) {
                $table->string('secret_key')->nullable()->after('api_key');
            }
            if (!Schema::hasColumn('offerwalls', 'postback_secret')) {
                $table->string('postback_secret')->nullable()->after('secret_key');
            }
            if (!Schema::hasColumn('offerwalls', 'ip_whitelist')) {
                $table->text('ip_whitelist')->nullable()->after('postback_secret');
            }
            if (!Schema::hasColumn('offerwalls', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('is_active');
            }
            if (!Schema::hasColumn('offerwalls', 'category')) {
                $table->string('category', 32)->nullable()->after('sort_order');
            }
            if (!Schema::hasColumn('offerwalls', 'payout_type')) {
                $table->string('payout_type', 32)->nullable()->after('category');
            }
            if (!Schema::hasColumn('offerwalls', 'revenue_share_percentage')) {
                $table->decimal('revenue_share_percentage', 5, 2)->default(70.00)->after('payout_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('offerwalls', function (Blueprint $table) {
            foreach ([
                'slug','description','iframe_url_template','postback_url','api_key',
                'secret_key','postback_secret','ip_whitelist','sort_order','category',
                'payout_type','revenue_share_percentage',
            ] as $col) {
                if (Schema::hasColumn('offerwalls', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
