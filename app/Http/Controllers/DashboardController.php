<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\InventoryHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalUsers' => User::count(),
            'totalProducts' => Product::count(),
            'totalSales' => Order::where('status', 'delivered')->sum('total_amount'),
            'lowStockProducts' => Product::whereLowStock()->get(),
            'dailySales' => $this->getDailySales(),
            'monthlySales' => $this->getMonthlySales(),
            'yearlySales' => $this->getYearlySales(),
            'dailyProfit' => $this->getDailyProfit(),
            'monthlyProfit' => $this->getMonthlyProfit(),
            'stockHistory' => $this->getStockHistory(),
            'recentTransactions' => $this->getRecentTransactions(),
            'dailyOrders' => $this->getDailyOrders(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
        ];

        return view('admin.dashboard', $data);
    }

    private function getDailyOrders()
    {
        $today = Carbon::today()->toDateString();
        return Order::with('product')->where('status', 'delivered')
                    ->whereDate('created_at', $today)
                    ->orderBy('created_at')
                    ->get();
    }

    private function getDailySales()
    {
        return Order::where('status', 'delivered')
            ->whereDate('created_at', Carbon::today())
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%H:00") as hour'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('hour')
            ->get();
    }

    private function getMonthlySales()
    {
        return Order::where('status', 'delivered')
            ->whereYear('created_at', Carbon::now()->year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('month')
            ->get();
    }

    private function getYearlySales()
    {
        return Order::where('status', 'delivered')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('year')
            ->get();
    }

    private function getDailyProfit()
    {
        return Order::where('orders.status', 'delivered')
            ->whereDate('orders.created_at', Carbon::today())
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->select(
                DB::raw('DATE_FORMAT(orders.created_at, "%H:00") as hour'),
                DB::raw('SUM(orders.total_amount - (products.price * orders.quantity)) as profit')
            )
            ->groupBy('hour')
            ->get();
    }

    private function getMonthlyProfit()
    {
        return Order::where('orders.status', 'delivered')
            ->whereYear('orders.created_at', Carbon::now()->year)
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->select(
                DB::raw('MONTH(orders.created_at) as month'),
                DB::raw('SUM(orders.total_amount - (products.price * orders.quantity)) as profit')
            )
            ->groupBy('month')
            ->get();
    }

    private function getStockHistory()
    {
        return InventoryHistory::with('product')
            ->select('inventory_history.*')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    private function getRecentTransactions()
    {
        return Order::with(['product'])
            ->where('status', 'delivered')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
}
