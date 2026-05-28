<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('postback_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 64)->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('transaction_id')->nullable()->index();
            $table->string('offer_id')->nullable();
            $table->string('offer_name')->nullable();
            $table->decimal('amount', 16, 4)->nullable();
            $table->decimal('payout', 16, 4)->nullable();
            $table->string('ip_address', 64)->nullable();
            $table->string('country', 4)->nullable();
            $table->longText('raw_payload')->nullable();
            $table->boolean('signature_valid')->default(false);
            $table->enum('status', ['received','accepted','duplicate','rejected','error'])->default('received')->index();
            $table->string('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postback_logs');
    }
};
