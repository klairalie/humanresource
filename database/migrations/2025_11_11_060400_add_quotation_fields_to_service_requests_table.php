<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->enum('quotation_status', ['Pending','Approved','Declined'])->default('Pending')->after('payment_status');
            $table->date('quotation_decision_date')->nullable()->after('quotation_status');

            // File storage for uploaded quotation (blob style to match existing pdf_* fields pattern)
            $table->string('quotation_file_name')->nullable()->after('quotation_decision_date');
            $table->string('quotation_file_mime')->nullable()->after('quotation_file_name');
            $table->binary('quotation_file')->nullable()->after('quotation_file_mime');
            $table->timestamp('quotation_uploaded_at')->nullable()->after('quotation_file');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn([
                'quotation_status',
                'quotation_decision_date',
                'quotation_file_name',
                'quotation_file_mime',
                'quotation_file',
                'quotation_uploaded_at',
            ]);
        });
    }
};
