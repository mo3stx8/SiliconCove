<?php

namespace App\Http\Controllers;

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
     
        return view('index', compact('products', 'latestProducts'));
    }
    
    
}
