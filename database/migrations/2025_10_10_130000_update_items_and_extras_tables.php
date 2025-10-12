<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add qty to extras
        Schema::table('service_request_item_extras', function (Blueprint $table) {
            if (!Schema::hasColumn('service_request_item_extras', 'qty')) {
                $table->unsignedInteger('qty')->default(1)->after('name');
            }
        });

        // Drop unit_type and bill_separately from service_request_items if they exist
        Schema::table('service_request_items', function (Blueprint $table) {
            if (Schema::hasColumn('service_request_items', 'unit_type')) {
                $table->dropColumn('unit_type');
            }
            if (Schema::hasColumn('service_request_items', 'bill_separately')) {
                $table->dropColumn('bill_separately');
            }
        });
    }

    public function down(): void
    {
        // Revert qty addition
        Schema::table('service_request_item_extras', function (Blueprint $table) {
            if (Schema::hasColumn('service_request_item_extras', 'qty')) {
                $table->dropColumn('qty');
            }
        });

        // We cannot reliably restore dropped columns without full definitions; skipping re-add in down.
    }
};
