<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductCodeService
{
    public function generateSku(): string
    {
        do {
            $sku = 'SKU-'.strtoupper(Str::random(8));
        } while (Product::withTrashed()->where('sku', $sku)->exists());

        return $sku;
    }

    public function generateBarcode(): string
    {
        do {
            $barcode = (string) random_int(1000000000000, 9999999999999);
        } while (Product::withTrashed()->where('barcode', $barcode)->exists());

        return $barcode;
    }
}
