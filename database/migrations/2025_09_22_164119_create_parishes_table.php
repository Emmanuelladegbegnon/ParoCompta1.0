<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parishes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('enable_payment_tracking')->default(false);
            $table->decimal('weekly_payment_amount', 10, 2)->nullable();
            $table->string('storage_path')->default('public');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parishes');
    }
};

