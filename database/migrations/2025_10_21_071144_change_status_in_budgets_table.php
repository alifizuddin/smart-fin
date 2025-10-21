<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            // Change default status from 'active' to 'pending'
            $table->string('status')->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            // Revert default status back to 'active'
            $table->string('status')->default('active')->change();
        });
    }
};