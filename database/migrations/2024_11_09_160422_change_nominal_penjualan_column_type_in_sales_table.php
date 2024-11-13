<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNominalPenjualanColumnTypeInSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            // Mengubah tipe data kolom 'nominal_penjualan' menjadi integer
            $table->integer('nominal_penjualan')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            // Mengubah kembali tipe data kolom 'nominal_penjualan' menjadi decimal jika rollback
            $table->decimal('nominal_penjualan', 10, 2)->change();
        });
    }
}
