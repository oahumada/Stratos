<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('lms_certificates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('organization_id')->index();
            $table->unsignedBigInteger('person_id')->nullable()->index();
            $table->unsignedBigInteger('lms_enrollment_id')->nullable()->index();
            $table->string('certificate_number')->nullable()->unique();
            $table->string('certificate_url')->nullable();
            $table->string('certificate_hash')->nullable();
            $table->unsignedBigInteger('certificate_template_id')->nullable()->index();
            $table->dateTime('issued_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_revoked')->default(false);
            $table->text('meta')->nullable();
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_certificates');
    }
};
