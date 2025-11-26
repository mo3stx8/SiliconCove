<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InventoryHistory;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(8);
        return view('product', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'restock_level' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240', // 10MB limit
            'category' => 'required|in:Processors,Motherboards,Graphics Cards,Memory & Storage,Power & Cooling,Peripherals & Accessories,Cases & Builds,Mod Zone'
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('products', 'public')
            : null;

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'restock_level' => $request->restock_level,
            'image' => $imagePath,
            'category' => $request->category,
        ]);

        return redirect()->route('admin.add-product')->with('success', 'Product added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:1',
            'restock_level' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB limit
            'category' => 'required|in:Processors,Motherboards,Graphics Cards,Memory & Storage,Power & Cooling,Peripherals & Accessories,Cases & Builds,Mod Zone'
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'restock_level' => $request->restock_level,
            'category' => $request->category,
            'image' => $request->hasFile('image')
                ? $request->file('image')->store('products', 'public')
                : $product->image
        ]);

        return redirect()->back()->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        // Convert the stored path to the correct storage path
        $imagePath = 'public/' . $product->image;

        // Delete the image file if it exists
        if (Storage::exists($imagePath)) {
            Storage::delete($imagePath);
        }

        // Delete the product from the database
        $product->delete();

        return redirect()->route('admin.view-products')->with('success', 'Product and image deleted successfully.');
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return redirect()->route('product')->with('error', 'Product not found.');
        }

        return view('product.show', compact('product'));
    }

    public function search(Request $request)
    {
        $query = Product::query();

        // Filter by category if provided
        if (!empty($request->input('category'))) {
            $query->where('category', 'LIKE', "%{$request->input('category')}%");
        }

        // Apply search filters if a query is provided
        if (!empty($request->input('query'))) {
            $searchTerm = $request->input('query');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                ->orWhere('price', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Get the filtered results
        $products = $query->orderBy('created_at', 'desc')->paginate(8);

        // Get latest products
        $latestProducts = Product::latest()->take(5)->get();

        return view('index', compact('products', 'latestProducts'));
    }

    public function restock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
    ]);

        $product = Product::findOrFail($request->product_id);

        // Record inventory history
        InventoryHistory::create([
            'product_id' => $product->id,
            'quantity_before' => $product->stock,
            'quantity_after' => $product->stock + $request->quantity,
            'purchase_price_before' => $product->price,
            'purchase_price_after' => $request->purchase_price ?? $product->price,
            'type' => 'restock',
            'notes' => $request->notes
        ]);

        // Update product stock and purchase price
        $product->stock += $request->quantity;
        $product->price = $request->purchase_price ?? $product->price;
        $product->save();

        return redirect()->back()->with('success', 'Product restocked successfully!');
    }
}
