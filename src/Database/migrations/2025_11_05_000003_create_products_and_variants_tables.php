<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $prefix = config('lyre.table_prefix');
        $productsTable = $prefix . 'products';
        $variantsTable = $prefix . 'product_variants';

        Schema::create($productsTable, function (Blueprint $table) use ($productsTable) {
            basic_fields($table, $productsTable);
            $table->string('name');
            $table->boolean('saleable')->default(true);
            $table->string('hscode')->nullable();
            $table->string('hstype')->nullable();
            $table->text('hsdescription')->nullable();
            $table->string('status')->nullable();
        });

        Schema::create($variantsTable, function (Blueprint $table) use ($productsTable, $variantsTable) {
            basic_fields($table, $variantsTable);
            $table->foreignId('product_id')->constrained($productsTable)->cascadeOnDelete();
            $table->string('name');
            $table->boolean('enabled')->default(true);
            $table->json('attributes')->nullable();
            $table->string('barcode')->nullable();
        });
    }

    public function down(): void
    {
        $prefix = config('lyre.table_prefix');
        $productsTable = $prefix . 'products';
        $variantsTable = $prefix . 'product_variants';
        Schema::dropIfExists($variantsTable);
        Schema::dropIfExists($productsTable);
    }
};

