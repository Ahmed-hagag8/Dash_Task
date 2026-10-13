<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('line_total', 10, 2)->nullable();
        });

        DB::table('order_items')->update([
            'line_total' => DB::raw('quantity * unit_price'),
        ]);

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('line_total', 10, 2)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('line_total');
        });
    }
};
