<?php

namespace Lyre\Commerce\Http\Requests;

use Lyre\Request;

class StoreCartRemoveRequest extends Request
{
    public function rules(): array
    {
        $prefix = config('lyre.table_prefix');
        return [
            'product_variant_id' => ['required', 'integer', "exists:{$prefix}product_variants,id"],
        ];
    }
}
