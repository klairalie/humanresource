<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            // ✅ Add columns safely if they don't exist
            if (!Schema::hasColumn('service_requests', 'order_total')) {
                $table->decimal('order_total', 12, 2)->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('service_requests', 'overall_discount')) {
                $table->decimal('overall_discount', 12, 2)->default(0)->after('order_total');
            }

            // ❌ Do NOT drop any existing columns — keep service_date, start_date, etc.
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            // Rollback if needed
            if (Schema::hasColumn('service_requests', 'overall_discount')) {
                $table->dropColumn('overall_discount');
            }
            if (Schema::hasColumn('service_requests', 'order_total')) {
                $table->dropColumn('order_total');
            }
        });
    }
};
