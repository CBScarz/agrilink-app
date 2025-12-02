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
        Schema::table('products', function (Blueprint $table) {
            $table->string('availability')->nullable()->default('In Stock');
            $table->string('unit')->nullable();
            $table->string('origin')->nullable();
            $table->date('harvestDate')->nullable();
            $table->date('expirationDate')->nullable();
            $table->string('certification')->nullable();
            $table->text('features')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'availability',
                'unit',
                'origin',
                'harvestDate',
                'expirationDate',
                'certification',
                'features',
            ]);
        });
    }
};
