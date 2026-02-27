<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // System permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();          // e.g. 'scenarios.create'
            $table->string('module');                    // e.g. 'scenarios'
            $table->string('action');                    // e.g. 'create'
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Role → Permissions pivot
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role');                      // system role name: admin, hr_leader, etc.
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['role', 'permission_id']);
        });

        // Ensure 'role' column exists on users (should already exist as default 'employee')
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('collaborator')->after('email');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
    }
};
