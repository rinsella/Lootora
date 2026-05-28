<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('provider_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 64)->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('transaction_id');
            $table->string('offer_id')->nullable();
            $table->string('offer_name')->nullable();
            $table->decimal('reward_points', 16, 4)->default(0);
            $table->decimal('payout_usd', 16, 4)->default(0);
            $table->decimal('platform_profit', 16, 4)->default(0);
            $table->string('ip_address', 64)->nullable();
            $table->string('country', 4)->nullable();
            $table->enum('status', ['pending','completed','reversed','rejected'])->default('completed')->index();
            $table->timestamps();

            $table->unique(['provider', 'transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_transactions');
    }
};
