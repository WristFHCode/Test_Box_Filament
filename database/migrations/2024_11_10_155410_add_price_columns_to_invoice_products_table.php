<?php
// 1. Migration baru: database/migrations/xxxx_add_price_columns_to_invoice_products_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_products', function (Blueprint $table) {
            $table->decimal('price_per_unit', 10, 2)->default(0)->after('quantity');
            $table->decimal('total_price', 10, 2)->default(0)->after('price_per_unit');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_products', function (Blueprint $table) {
            $table->dropColumn(['price_per_unit', 'total_price']);
        });
    }
};
