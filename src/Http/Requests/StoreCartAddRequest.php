<?php

namespace Lyre\Commerce\Http\Requests;

use Lyre\Request;

class StoreCartAddRequest extends Request
{
    public function rules(): array
    {
        $prefix = config('lyre.table_prefix');
        return [
            'product_variant_id' => ['nullable', 'integer', "exists:{$prefix}product_variants,id"],
            'product_id' => ['nullable', 'string'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
