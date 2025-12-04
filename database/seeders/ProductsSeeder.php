<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $jsonFiles = glob(database_path('data/*.json')); // كل ملفات JSON في المجلد

        foreach ($jsonFiles as $file) {
            $data = json_decode(file_get_contents($file), true);

            foreach ($data as $product) {
                // إذا المنتج موجود مسبقًا بالاسم والفئة نحدثه، وإلا نضيفه
                Product::updateOrCreate(
                    [
                        'name' => $product['name'],
                        'category' => $product['category']
                    ],
                    [
                        'description' => $product['description'],
                        'price' => $product['price'],
                        'stock' => $product['stock'],
                        'image' => $product['image'] ?? null
                    ]
                );
            }
        }
    }
}
