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
        Schema::table('agents', function (Blueprint $table) {
            if (!Schema::hasColumn('agents', 'role_description')) {
                $table->text('role_description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('agents', 'persona')) {
                $table->text('persona')->nullable()->after('role_description');
            }
            if (!Schema::hasColumn('agents', 'type')) {
                $table->string('type')->default('analyst')->after('persona');
            }
            if (!Schema::hasColumn('agents', 'capabilities_config')) {
                $table->json('capabilities_config')->nullable()->after('type');
            }
            $table->unsignedBigInteger('organization_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('agents', 'role_description')) $cols[] = 'role_description';
            if (Schema::hasColumn('agents', 'persona')) $cols[] = 'persona';
            if (Schema::hasColumn('agents', 'type')) $cols[] = 'type';
            if (Schema::hasColumn('agents', 'capabilities_config')) $cols[] = 'capabilities_config';
            
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
