<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Change the 'status' column to add 'rejected'
        DB::statement("ALTER TABLE budgets CHANGE COLUMN status status ENUM('pending', 'active', 'closed', 'rejected') NOT NULL DEFAULT 'pending'");
    }
    public function down(): void {
        // Revert back
        DB::statement("ALTER TABLE budgets CHANGE COLUMN status status ENUM('pending', 'active', 'closed') NOT NULL DEFAULT 'pending'");
    }
};