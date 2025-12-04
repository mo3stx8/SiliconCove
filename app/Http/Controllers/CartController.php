<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to view your cart.');
        }

        // Fetch user's cart items along with product details
        $cartItems = Cart::where('user_id', auth()->id())->with('product')->get();

        return view('cart', ['cart' => $cartItems]);
    }


    public function add(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to add items to cart.');
        }

        // Handle bulk add from checkout
        if ($request->has('bulk_add')) {
            $productIds = json_decode($request->selected_product_items, true);
            $quantities = json_decode($request->selected_product_quantities, true);
            
            foreach ($productIds as $index => $productId) {
                $product = Product::findOrFail($productId);
                $quantity = $quantities[$index];

                if ($product->stock < $quantity) {
                    return redirect()->back()->with('error', "Insufficient stock for {$product->name}");
                }

                // Find existing cart item
                $cartItem = Cart::where('user_id', auth()->id())
                              ->where('product_id', $productId)
                              ->first();

                if ($cartItem) {
                    // Add new quantity to existing quantity
                    $cartItem->quantity += $quantity;
                    $cartItem->save();
                } else {
                    // Create new cart item
                    Cart::create([
                        'user_id' => auth()->id(),
                        'product_id' => $productId,
                        'quantity' => $quantity
                    ]);
                }
            }

            return redirect()->route('cart.index')->with('success', 'Items added to cart successfully!');
        }

        // Existing single item add logic
        $product = Product::findOrFail($request->product_id);

        $quantityToAdd = $request->quantity ?? 1;

        // if stock is 0, return error
        if ($product->stock <= 0) {
            return redirect()->back()->with('error', "Cannot add {$product->name} to cart. Out of stock.");
        }
        
        // Check if requested quantity exceeds available stock
        if ($quantityToAdd > $product->stock) {
            return redirect()->back()->with('error', "Cannot add more than available stock ({$product->stock}) for {$product->name}.");
        }

        $cartItem = Cart::where('user_id', auth()->id())->where('product_id', $product->id)->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantityToAdd;

            if ($newQuantity > $product->stock) {
                return redirect()->back()->with('error', "Adding this quantity exceeds available stock ({$product->stock}) for {$product->name}.");
            }

            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $quantityToAdd
            ]);
        }

        return redirect()->back()->with('success', "{$product->name} added to cart!");
    }

    public function remove($id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to remove items.');
        }

        // Find the cart item belonging to the logged-in user
        $cartItem = Cart::where('user_id', auth()->id())->where('id', $id)->first();

        if ($cartItem) {
            $cartItem->delete();
            return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
        }

        return redirect()->route('cart.index')->with('error', 'Item not found in cart.');
    }

    public function clear()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to clear your cart.');
        }

        // Delete all cart items for the logged-in user
        Cart::where('user_id', auth()->id())->delete();

        return redirect()->route('cart.index')->with('success', 'Cart cleared.');
    }

    public function buyNow(Request $request)
    {
        if (isset($request->product) && $request->product == 'true') {
            $productIds = json_decode($request->selected_product_items, true);
            $quantities = json_decode($request->selected_product_quantities, true);
            $cartItems = collect();

            foreach ($productIds as $index => $productId) {
                $product = Product::findOrFail($productId);
                $quantity = $quantities[$index];

                if ($quantity > $product->stock) {
                    return redirect()->back()->with('error', "Insufficient stock for {$product->name}.");
                }

                // Create a temporary cart item object with an id
                $cartItems->push((object)[
                    'id' => $productId, // Add this line to fix the undefined property error
                    'product' => $product,
                    'quantity' => $quantity,
                    'total' => $product->price * $quantity,
                    'instant_order' => true,
                ]);
            }

            // if admin is logged in, deduct stock from each products and the create new order data from orders table
            if (auth()->guard('admin')->check()) {
                $orderNo = strtoupper(Str::random(15));

                foreach ($cartItems as $cartItem) {
                    $product = Product::findOrFail($cartItem->id);
                    $product->stock -= $cartItem->quantity;
                    $product->save();

                    // Create new order data in orders table
                    Order::create([
                        'admin_id' => auth('admin')->id(),
                        'order_no' => $orderNo,
                        'product_id' => $cartItem->id,
                        'payment_method' => 'cash',
                        'quantity' => $cartItem->quantity,
                        'total_amount' => $cartItem->total,
                        'status' => 'delivered',
                    ]);
                }

                return redirect()->back()->with('success', 'Order placed successfully!')->with('order_no', $orderNo);
            }

            $address = auth()->user()->address ?? "";
            return view('cart.buy-now', compact('cartItems', 'address'));
        }

        $selectedItems = explode(',', $request->input('selected_items', ''));
        $quantities = explode(',', $request->input('quantities', ''));
        $cartItems = Cart::where('user_id', auth()->id())->whereIn('id', $selectedItems)->with('product')->get();

        foreach ($cartItems as $index => $cartItem) {
            $product = $cartItem->product;
            $quantity = isset($quantities[$index]) ? (int)$quantities[$index] : $cartItem->quantity;

            if ($quantity > $product->stock) {
                return redirect()->route('cart.index')->with('error', "Insufficient stock for {$product->name}.");
            }

            $cartItem->quantity = $quantity;
            $cartItem->save();
        }

        $address = auth()->user()->address;

        return view('cart.buy-now', compact('cartItems', 'address'));
    }
}
