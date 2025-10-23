<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTechnicianAssignmentsIndexes extends Migration
{
    public function up()
    {
        // Add indexes for technician assignments
        Schema::table('service_request_items', function (Blueprint $table) {
            // Composite index for date range queries
            $table->index(['start_date', 'end_date'], 'idx_service_dates');
            
            // Index for technician assignment lookups
            $table->index('assigned_technician_id', 'idx_assigned_tech');
            
            // Combined index for date+time range queries
            $table->index(
                ['start_date', 'start_time', 'end_time'],
                'idx_service_datetime_range'
            );
        });

        // Add index for technician assignments
        Schema::table('technician_assignments', function (Blueprint $table) {
            // Composite index for technician availability checks
            $table->index(
                ['technician_id', 'status'],
                'idx_tech_status'
            );
            
            // Index for item lookups
            $table->index('item_id', 'idx_item_id');
        });
    }

    public function down()
    {
        Schema::table('service_request_items', function (Blueprint $table) {
            $table->dropIndex('idx_service_dates');
            $table->dropIndex('idx_assigned_tech');
            $table->dropIndex('idx_service_datetime_range');
        });

        Schema::table('technician_assignments', function (Blueprint $table) {
            $table->dropIndex('idx_tech_status');
            $table->dropIndex('idx_item_id');
        });
    }
}
