<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class AdminController extends Controller
{
    public function viewProducts(Request $request)
    {
        $query = Product::query();

        // Apply search if provided
        if ($request->has('search') && $request->get('search') != '') {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('price', 'like', '%' . $search . '%')
                    ->orWhere('stock', 'like', '%' . $search . '%');
            });
        }

        // Get number of entries per page
        $entries = $request->get('entries', 5);

        // Get products with pagination
        $products = $query->paginate($entries);

        // Transform the data for the data-table component
        $rows = $products->through(function ($product, $index) use ($products) {
            return [
                'id' => $products->firstItem() + $index,
                'image' => '<img src="' . asset('storage/' . $product->image) . '" alt="Product Image" width="60" height="60" class="rounded">',
                'name' => $product->name,
                'description' => $product->description,
                'price' => '$' . number_format($product->price, 2),
                'stock' => $product->stock,
                'product_id' => $product->id,
                'product_raw' => $product
            ];
        });

        $actions = [
            [
                'view' => null,
                'inline' => function ($row) {
                    $product = $row['product_raw'];
                    return '<div class="d-flex align-items-center gap-1">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editProductModal"
                                onclick="editProduct(' . $product->id . ', \'' . addslashes($product->name) . '\', \'' . addslashes($product->description) . '\', ' . $product->price . ', ' . $product->stock . ', \'' . asset('storage/' . $product->image) . '\', \'' . $product->category . '\', \'' . $product->restock_level . '\')">
                                <i class="fa fa-edit"></i><span class="d-none d-sm-inline ms-1">Edit</span>
                            </button>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                data-bs-target="#deleteProductModal"
                                onclick="setDeleteProduct(' . $product->id . ')">
                                <i class="fa fa-trash"></i><span class="d-none d-sm-inline ms-1">Delete</span>
                            </button>
                        </div>';
                }
            ]
        ];

        return view('admin.view-products', compact('products', 'rows', 'actions'));
    }
}
