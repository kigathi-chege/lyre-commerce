<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $prefix = config('lyre.table_prefix');
        $tableName = $prefix . 'locations';

        Schema::create($tableName, function (Blueprint $table) use ($tableName) {
            basic_fields($table, $tableName);
            $table->string('name');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('address')->nullable();
            $table->decimal('delivery_fee', 12, 2)->nullable();
        });
    }

    public function down(): void
    {
        $prefix = config('lyre.table_prefix');
        $tableName = $prefix . 'locations';
        Schema::dropIfExists($tableName);
    }
};

