<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');

        $products = Product::when($query, function ($q) use ($query) {
            return $q->where('name', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->orWhere('price', 'LIKE', "%{$query}%");
        })->latest()->paginate(8); // Removed pagination

        $latestProducts = Product::latest()->take(5)->get();

        $persistedCartItems = collect();
        if (auth()->check()) {
            $persistedCartItems = Cart::where('user_id', auth()->id())
                ->with('product')
                ->get()
                ->map(function ($cartItem) {
                    return [
                        'id' => (string) $cartItem->product_id,
                        'name' => $cartItem->product->name,
                        'quantity' => (int) $cartItem->quantity,
                        'price' => (float) $cartItem->product->price,
                        'total' => (float) ($cartItem->quantity * $cartItem->product->price),
                    ];
                });
        }

        return view('index', compact('products', 'latestProducts', 'persistedCartItems'));
    }
}
