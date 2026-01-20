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
        Schema::create('forecasts', function (Blueprint $table) {
            $table->id();
            $table->date('forecast_date');
            $table->decimal('amount', 15, 2); // Predicted amount
            $table->decimal('confidence_score', 5, 2)->nullable(); // 0.00 to 1.00
            $table->text('insights')->nullable(); // N8N explanation
            $table->string('category')->nullable(); // Null = Total, otherwise specific category
            $table->timestamps();
            
            // Index for faster lookups
            $table->index(['forecast_date', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forecasts');
    }
};
