<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $prefix = config('lyre.table_prefix');
        $ordersTable = $prefix . 'orders';
        $orderItemsTable = $prefix . 'order_items';
        $shippingAddressesTable = $prefix . 'shipping_addresses';
        $locationsTable = $prefix . 'locations';
        $couponsTable = $prefix . 'coupons';
        $productVariantsTable = $prefix . 'product_variants';

        Schema::create($ordersTable, function (Blueprint $table) use ($ordersTable, $shippingAddressesTable, $locationsTable, $couponsTable) {
            basic_fields($table, $ordersTable);
            $table->string('reference')->unique();
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->foreignId('customer_id')->constrained((new (get_user_model()))->getTable());
            $table->unsignedBigInteger('payment_term_id')->nullable();
            $table->decimal('packaging_cost', 12, 2)->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('shipping_address_id')->nullable()->constrained($shippingAddressesTable);
            $table->foreignId('location_id')->nullable()->constrained($locationsTable);
            $table->foreignId('coupon_id')->nullable()->constrained($couponsTable);
            $table->text('notes')->nullable();
            // metadata added by basic_fields()
        });

        Schema::create($orderItemsTable, function (Blueprint $table) use ($orderItemsTable, $ordersTable, $productVariantsTable) {
            basic_fields($table, $orderItemsTable);
            $table->foreignId('order_id')->constrained($ordersTable)->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained($productVariantsTable);
            $table->decimal('unit_price', 12, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 2);
            $table->string('currency', 3)->nullable();
            $table->json('snapshot')->nullable();
        });
    }

    public function down(): void
    {
        $prefix = config('lyre.table_prefix');
        $ordersTable = $prefix . 'orders';
        $orderItemsTable = $prefix . 'order_items';
        Schema::dropIfExists($orderItemsTable);
        Schema::dropIfExists($ordersTable);
    }
};

