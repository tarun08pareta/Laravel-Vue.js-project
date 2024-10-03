<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Condition;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $category = Category::find($row['category_id']);
        $condition = Condition::find($row['condition_id']);

        // Create or update the product
        $product = Product::updateOrCreate(
            // ['id' => $row['id']],
            [
                'name' => $row['name'],
                'description' => $row['description'],
                'price' => $row['price'],
                'discount' => $row['discount'],
                'final_price' => $row['final_price'],
                'condition_id' => $condition ? $condition->id : null,
                'image' => $row['image'],
                'status' => isset($row['status']) ? $row['status'] === 'Active' : 1,
            ]
        );
        if (!$category || !$condition) {
            // Handle missing category or condition
            return null; // Or log the error, skip the row, etc.
        }
        // Attach category to the product
        if ($category) {
            $product->categories()->sync([$category->id]);
        }

        return $product;
    }
}
