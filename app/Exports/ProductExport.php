<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductExport implements FromCollection, WithHeadings
//  ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        return Product::with('categories', 'conditions')
            ->whereIn('id', $this->ids)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'discount' => $product->discount,
                    'final_price' => $product->final_price,
                    'category_id' => $product->categories->pluck('id')->first() ?? '',
                    'condition_id' => $product->condition_id,
                    'image' => $product->image,
                    'status' => $product->status ? 'Active' : 'Inactive',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Price',
            'Discount',
            'Final Price',

            'Category ID',
            'Condition ID',
            'Image',
            'Status',

        ];
    }
}
