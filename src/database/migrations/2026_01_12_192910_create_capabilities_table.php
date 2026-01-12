<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('capabilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->default('technical'); // technical, leadership, business, operational
            $table->string('status')->default('active'); // active, inactive
            $table->foreignId('discovered_in_scenario_id')->nullable()->constrained('scenarios')->onDelete('set null'); // -- "incubando"
            $table->timestamps();

            $table->index(['organization_id', 'status']);
        });

        // Añadir constraints solo en drivers que soporten ALTER TABLE ADD CONSTRAINT (no SQLite)
        if (DB::getDriverName() !== 'sqlite') {
            // Constraint para category
            DB::statement("ALTER TABLE capabilities ADD CONSTRAINT capabilities_category_check CHECK (category IN ('technical', 'leadership', 'business', 'operational'))");

            // Constraint para status
            DB::statement("ALTER TABLE capabilities ADD CONSTRAINT capabilities_status_check CHECK (status IN ('active', 'inactive'))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('capabilities');
    }
};