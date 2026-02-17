<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('scenario_role_skills', function (Blueprint $table) {
            // Drop old foreign key if it exists
            $table->dropForeign(['role_id']);
            
            // Re-define foreign key to point to scenario_roles instead of roles
            $table->foreign('role_id')->references('id')->on('scenario_roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scenario_role_skills', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }
};
