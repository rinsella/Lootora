<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('referral_earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();          // earner
            $table->unsignedBigInteger('referred_user_id')->index(); // referred user
            $table->unsignedBigInteger('source_transaction_id')->nullable()->index();
            $table->decimal('points', 16, 4)->default(0);
            $table->enum('status', ['pending','completed','reversed'])->default('completed')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_earnings');
    }
};
