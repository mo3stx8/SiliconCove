<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->q);

        if (!$q) {
            return redirect()->back();
        }

        return view('admin.search.results', [
            'query'   => $q,
            'users'   => $this->searchUsers($q),
            'products'=> $this->searchProducts($q),
            'orders'  => $this->searchOrders($q),
        ]);
    }

    protected function searchUsers($q)
    {
        return User::where('name', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->limit(10)
            ->get();
    }

    protected function searchProducts($q)
    {
        return Product::where('name', 'like', "%{$q}%")
            ->limit(10)
            ->get();
    }

    protected function searchOrders($q)
    {
        return Order::where('id', 'like', "%{$q}%")
            ->orWhere('user_id', 'like', "%{$q}%")
            ->orWhere('id', 'like', "%{$q}%")
            ->limit(10)
            ->get();
    }
}
