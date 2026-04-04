<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_marketplace_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('lms_marketplace_listings')->cascadeOnDelete();
            $table->foreignId('buyer_organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('purchased_by')->constrained('users')->cascadeOnDelete();
            $table->decimal('price_paid', 10, 2);
            $table->string('status')->default('pending'); // pending, completed, refunded
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_marketplace_purchases');
    }
};
