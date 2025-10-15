<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 🧩 Permissions table (e.g., 'employeeprofiles_view', 'add_product')
        Schema::create('permissions', function (Blueprint $table) {
            $table->id('permission_id');
            $table->string('permission_key')->unique(); // e.g. 'employeeprofiles_view'
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 🧩 Position → Permission mapping (acts like role_permissions)
        Schema::create('position_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('position'); // e.g. 'HR', 'Finance', 'Admin'
            $table->foreignId('permission_id')->constrained('permissions', 'permission_id')->cascadeOnDelete();
            $table->boolean('is_allowed')->default(false);
            $table->timestamps();
        });

        // 🧩 Optional — Link Admin accounts to positions (if not already handled)
        Schema::table('administrativeaccounts', function (Blueprint $table) {
            if (!Schema::hasColumn('administrativeaccounts', 'position')) {
                $table->string('position')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('position_permissions');
        Schema::dropIfExists('permissions');
    }
};
