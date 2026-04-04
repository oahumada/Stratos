<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_courses', function (Blueprint $table) {
            $table->json('tags')->nullable()->after('is_active');
            $table->boolean('featured')->default(false)->after('tags');
            $table->string('enrollment_type')->default('invite')->after('featured');
        });
    }

    public function down(): void
    {
        Schema::table('lms_courses', function (Blueprint $table) {
            $table->dropColumn(['tags', 'featured', 'enrollment_type']);
        });
    }
};
