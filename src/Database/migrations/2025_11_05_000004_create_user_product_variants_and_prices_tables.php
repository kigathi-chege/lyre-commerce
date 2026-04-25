<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $prefix = config('lyre.table_prefix');
        $userVariantsTable = $prefix . 'user_product_variants';
        $variantPricesTable = $prefix . 'product_variant_prices';
        $variantsTable = $prefix . 'product_variants';

        Schema::create($userVariantsTable, function (Blueprint $table) use ($userVariantsTable, $variantsTable) {
            basic_fields($table, $userVariantsTable);
            $table->foreignId('user_id')->constrained((new (get_user_model()))->getTable());
            $table->foreignId('product_variant_id')->constrained($variantsTable)->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->integer('stock_level')->default(0);
            $table->integer('min_qty')->nullable();
            $table->integer('max_qty')->nullable();
        });

        Schema::create($variantPricesTable, function (Blueprint $table) use ($userVariantsTable, $variantPricesTable) {
            basic_fields($table, $variantPricesTable);
            $table->foreignId('user_product_variant_id')->constrained($userVariantsTable)->cascadeOnDelete();
            $table->decimal('price', 12, 2);
            $table->string('currency', 3);
            $table->decimal('compare_at_price', 12, 2)->nullable();
            $table->boolean('tax_included')->default(false);
            $table->timestamp('effective_from')->nullable();
            $table->timestamp('effective_through')->nullable();
        });
    }

    public function down(): void
    {
        $prefix = config('lyre.table_prefix');
        $userVariantsTable = $prefix . 'user_product_variants';
        $variantPricesTable = $prefix . 'product_variant_prices';
        Schema::dropIfExists($variantPricesTable);
        Schema::dropIfExists($userVariantsTable);
    }
};

