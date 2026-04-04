<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_scheduled_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('report_type'); // completion, compliance, engagement, time_to_complete
            $table->jsonb('filters')->nullable();
            $table->string('frequency'); // daily, weekly, monthly
            $table->jsonb('recipients'); // array of email addresses
            $table->dateTime('last_sent_at')->nullable();
            $table->dateTime('next_send_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_scheduled_reports');
    }
};
