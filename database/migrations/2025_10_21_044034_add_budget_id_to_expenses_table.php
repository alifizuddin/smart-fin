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
        Schema::table('expenses', function (Blueprint $table) {
            // Add the new budget_id column
            // We make it nullable() so old expenses aren't affected.
            // onDelete('set null') means if a budget is deleted,
            // the expense record remains, but its budget_id is set to null.
            $table->foreignId('budget_id')
                  ->nullable()
                  ->after('department_id') // Places it after department_id
                  ->constrained('budgets')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // This is the proper way to remove a foreign key
            $table->dropForeign(['budget_id']);
            $table->dropColumn('budget_id');
        });
    }
};