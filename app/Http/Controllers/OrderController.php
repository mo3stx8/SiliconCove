<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Dompdf\Dompdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $request->validate([
            'payment_method' => 'required',
            'proof_of_payment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'selected_cart_items' => 'required|array',
        ]);

        $isInstantOrder = isset($request->instant_order) && $request->instant_order;
        $selectedProductQuantities = $request->selected_product_quantities;
        $selectedCartItems = $request->selected_cart_items;

        $orderNo = strtoupper(Str::random(15));
        $productNameErrors = [];

        foreach ($selectedCartItems as $index => $cartItemId) {
            if ($isInstantOrder) {
                // the cart item ID is product ID
                $product = Product::find($cartItemId);
                if ($product && $product->stock < $selectedProductQuantities[$index]) {
                    $productNameErrors[] = $product->name;
                }
            } else {
                // the cart item ID is order ID
                $cartItem = Cart::find($cartItemId);
                if ($cartItem) {
                    $product = Product::find($cartItem->product_id);
                    if ($product && $product->stock < $cartItem->quantity) {
                        $productNameErrors[] = $product->name;
                    }
                }
            }
        }

        if (! empty($productNameErrors)) {
            return redirect()->route('cart.index')->with('error', 'Cannot place order. Insufficient stock for: '.implode(', ', $productNameErrors));
        }

        $proofOfPaymentPath = null;

        if ($request->hasFile('proof_of_payment')) {
            $proofOfPaymentPath = $request->file('proof_of_payment')->store('proof_of_payments', 'public');
        }

        foreach ($selectedCartItems as $index => $cartItemId) {
            if ($isInstantOrder) {
                Order::create([
                    'user_id' => Auth::id(),
                    'order_no' => $orderNo,
                    'product_id' => $cartItemId,
                    'payment_method' => $request->payment_method,
                    'proof_of_payment' => $proofOfPaymentPath,
                    'quantity' => $selectedProductQuantities[$index],
                    'total_amount' => Product::find($cartItemId)->price * $selectedProductQuantities[$index],
                    'status' => 'pending',
                ]);

                // the cart item ID is product ID
                $product = Product::find($cartItemId);
                if ($product) {
                    $product->stock -= $selectedProductQuantities[$index];
                    $product->save();
                }
            } else {
                $cartItem = Cart::find($cartItemId);
                if ($cartItem) {
                    Order::create([
                        'user_id' => Auth::id(),
                        'order_no' => $orderNo,
                        'product_id' => $cartItem->product_id,
                        'payment_method' => $request->payment_method,
                        'proof_of_payment' => $proofOfPaymentPath,
                        'quantity' => $cartItem->quantity,
                        'total_amount' => $cartItem->product->price * $cartItem->quantity,
                        'status' => 'pending',
                    ]);

                    $product = Product::find($cartItem->product_id);
                    if ($product) {
                        $product->stock -= $cartItem->quantity;
                        $product->save();
                    }
                }
            }
        }

        if (! $isInstantOrder) {
            Cart::where('user_id', Auth::id())
                ->whereIn('id', $selectedCartItems)
                ->delete();
        }

        return redirect()->route('cart.index')->with('success', 'Order placed successfully!');
    }

    public function viewOrders(Request $request)
    {
        $entries = $request->get('entries', 5);
        $query = $this->applySearchFilters(Order::with(['user', 'user.address'])->orderBy('id', 'desc'), $request);

        $orders = $query->whereNotIn('status', ['refunded', 'refund_requested', 'refund_rejected'])->paginate($entries);

        return view('admin.view-orders', compact('orders'));
    }

    public function pendingOrders(Request $request)
    {
        $entries = $request->get('entries', 5);
        $query = $this->applySearchFilters(Order::with(['user', 'user.address'])->orderBy('id', 'desc'), $request);

        $orders = $query->whereNotIn('status', ['delivered', 'refunded', 'refund_requested', 'refund_rejected'])->paginate($entries);
        $newOrdersCount = Order::where('status', 'pending')->count();
        $aprovedOrdersCount = Order::where('status', 'approved')->count();
        $readyToShipOrdersCount = Order::where('status', 'in progress')->count();
        $shippedOrdersCount = Order::where('status', 'delivered')->count();

        return view('admin.pending-orders', compact('orders', 'newOrdersCount', 'aprovedOrdersCount', 'readyToShipOrdersCount', 'shippedOrdersCount'));
    }

    public function completedOrders(Request $request)
    {
        $entries = $request->get('entries', 5);
        $query = $this->applySearchFilters(Order::with(['user', 'user.address'])->orderBy('id', 'desc'), $request);

        $orders = $query->where('status', 'delivered')->paginate($entries);

        // Group completed orders by month and count them
        $monthlyCompletedOrders = Order::where('status', 'delivered')
            ->selectRaw('DATE_FORMAT(updated_at, "%b %Y") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderByRaw('MIN(updated_at)')
            ->pluck('count', 'month');

        return view('admin.completed-orders', compact('orders', 'monthlyCompletedOrders'));
    }

    public function generateInvoice($orderNo)
    {
        $order = Order::with('user', 'product')->where('order_no', $orderNo)->firstOrFail();

        // Render the Blade view as HTML
        $html = view('invoices.order-invoice', compact('order'))->render();

        // Initialize Dompdf
        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait'); // Set paper size and orientation
        $dompdf->render();

        // Return the generated PDF as a download
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="invoice-'.$order->order_no.'.pdf"',
        ]);
    }

    public function deleteOrder($id)
    {
        try {
            $order = Order::where('id', $id)->firstOrFail();

            // Perform deletion
            $order->delete();

            return redirect()->route('admin.view-orders')->with('success', 'Order deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.view-orders')->with('error', 'Order not found.');
        } catch (\Exception $e) {
            // Optional: handle other unexpected errors
            return redirect()->route('admin.view-orders')->with('error', 'An error occurred while deleting the order.');
        }
    }

    public function approveOrder($id)
    {
        try {
            $order = Order::where('id', $id)->firstOrFail();

            // Update to approved status
            $order->update(['status' => 'approved']);

            // Schedule status change to 'in progress' after 1 second
            sleep(1);
            $order->update(['status' => 'in progress']);

            return response()->json([
                'success' => true,
                'message' => 'Order approved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving order',
            ], 500);
        }
    }

    public function completeOrder($id)
    {
        try {
            $order = Order::where('id', $id)->where('status', 'in progress')->firstOrFail();

            // Update to delivered status
            $order->update([
                'status' => 'delivered',
                'tracking_number' => request('tracking_number'),
                'completion_notes' => request('completion_notes'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order marked as complete successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or cannot be completed',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing order',
            ], 500);
        }
    }

    public function viewSales(Request $request)
    {
        // Date range filter
        $dateRange = $request->input('dateRange');
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        if ($dateRange) {
            $dates = explode(' - ', $dateRange);
            $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
            $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
        }

        // Base query
        $query = Order::where('status', 'delivered')
            ->whereBetween('updated_at', [$startDate, $endDate]);

        // Apply payment method filter
        if ($request->filled('paymentMethod')) {
            $query->where('payment_method', strtolower($request->paymentMethod));
        }

        // Get orders for calculations
        $orders = $query->get();

        // Calculate metrics using filtered data
        $currentMonthSales = $orders->sum('total_amount');
        $monthlyOrderCount = $orders->count();
        $averageOrderValue = $monthlyOrderCount > 0 ? $currentMonthSales / $monthlyOrderCount : 0;

        // Calculate revenue growth
        $previousQuery = clone $query;
        $previousStartDate = clone $startDate;
        $previousEndDate = clone $endDate;
        $daysDifference = $startDate->diffInDays($endDate);

        $previousStartDate->subDays($daysDifference + 1);
        $previousEndDate->subDays($daysDifference + 1);

        $previousPeriodSales = Order::where('status', 'delivered')
            ->whereBetween('updated_at', [$previousStartDate, $previousEndDate])
            ->sum('total_amount');

        $revenueGrowth = $previousPeriodSales > 0
            ? (($currentMonthSales - $previousPeriodSales) / $previousPeriodSales) * 100
            : 0;

        // Use same query conditions for sales trend
        $monthlySales = clone $query;
        $monthlySales = $monthlySales->selectRaw('DATE_FORMAT(updated_at, "%Y-%m-%d") as date, SUM(total_amount) as total_sales')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total_sales', 'date')
            ->toArray();

        // Use same query conditions for payment distribution
        $paymentDistribution = clone $query;
        $paymentDistribution = $paymentDistribution->selectRaw('payment_method, COUNT(*) as count')
            ->groupBy('payment_method')
            ->pluck('count', 'payment_method')
            ->toArray();

        // Normalize distribution to include all expected methods
        $expectedMethods = ['cod', 'Kuraimi Bank USD', 'Kuraimi Bank SR', 'cash'];
        foreach ($expectedMethods as $method) {
            if (! isset($paymentDistribution[$method])) {
                $paymentDistribution[$method] = 0;
            }
        }

        // Optionally re-order keys to a preferred order (cod, Kuraimi Bank USD, Kuraimi Bank SR, cash)
        $paymentDistribution = array_merge(
            array_intersect_key(array_flip($expectedMethods), $paymentDistribution),
            $paymentDistribution
        );

        // Ensure both payment methods exist in distribution
        // if (empty($paymentDistribution)) {
        //     $paymentDistribution = ['cod' => 0, 'gcash' => 0];
        // } else {
        //     if (! isset($paymentDistribution['cod'])) {
        //         $paymentDistribution['cod'] = 0;
        //     }
        //     if (! isset($paymentDistribution['gcash'])) {
        //         $paymentDistribution['gcash'] = 0;
        //     }
        // }

        // Use same query conditions for top customers with payment method filter
        $topCustomers = clone $query;
        $topCustomers = $topCustomers->with('user')
            ->when($request->filled('paymentMethod'), function ($q) use ($request) {
                $q->where('payment_method', strtolower($request->paymentMethod));
            })
            ->select('user_id', DB::raw('COUNT(*) as purchase_count'), DB::raw('SUM(total_amount) as total_spent'))
            ->groupBy('user_id')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        // Add top products query with payment method filter
        $topProducts = Order::where('status', 'delivered')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->when($request->filled('paymentMethod'), function ($q) use ($request) {
                $q->where('payment_method', strtolower($request->paymentMethod));
            })
            ->select(
                'product_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        // Rest of the code remains the same...
        return view('admin.analytics', compact(
            'orders',
            'currentMonthSales',
            'monthlyOrderCount',
            'averageOrderValue',
            'revenueGrowth',
            'monthlySales',
            'paymentDistribution',
            'topCustomers',
            'topProducts',
            'startDate',
            'endDate'
        ));
    }

    public function processRefunds(Request $request)
    {
        // Get entries for both tables
        $pendingEntries = $request->get('pending_entries', 5);
        $processedEntries = $request->get('processed_entries', 5);

        $query = $this->applySearchFilters(Order::with('user')->orderBy('id', 'desc'), $request, 'pending_search');
        $processedQuery = $this->applySearchFilters(Order::with('user')->orderBy('id', 'desc'), $request, 'processed_search');

        // Calculate refund statistics
        $currentMonth = now()->startOfMonth();
        $totalRefunds = Order::where('status', 'refunded')
            ->whereMonth('updated_at', $currentMonth->month)
            ->sum('total_amount');

        $pendingRefunds = Order::where('status', 'refund_requested')->count();

        $processedRefunds = Order::where('status', 'refunded')
            ->whereMonth('updated_at', $currentMonth->month)
            ->count();

        // Calculate refund rate
        $totalOrders = Order::whereMonth('created_at', $currentMonth->month)->count();
        $refundRate = $totalOrders > 0
            ? ($processedRefunds / $totalOrders) * 100
            : 0;

        // Get pending refund requests with its own page parameter
        $orders = $query->where('status', 'refund_requested')
            ->paginate($pendingEntries, ['*'], 'pending_page');

        // Get processed refunds with its own page parameter
        $processedOrders = $processedQuery->whereIn('status', ['refunded', 'refund_rejected'])
            ->orderBy('updated_at', 'desc')
            ->paginate($processedEntries, ['*'], 'processed_page');

        return view('admin.process-refunds', compact(
            'orders',
            'processedOrders',
            'totalRefunds',
            'pendingRefunds',
            'processedRefunds',
            'refundRate'
        ));
    }

    public function approveRefund($id)
    {
        try {
            $order = Order::findOrFail($id);

            if ($order->status !== 'refund_requested') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is not in refund requested status',
                ], 400);
            }

            $order->update([
                'status' => 'refunded',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refund approved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving refund',
            ], 500);
        }
    }

    public function denyRefund($id)
    {
        try {
            $order = Order::findOrFail($id);

            if ($order->status !== 'refund_requested') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is not in refund requested status',
                ], 400);
            }

            $order->update(['status' => 'refund_rejected']);

            return response()->json([
                'success' => true,
                'message' => 'Refund denied successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error denying refund',
            ], 500);
        }
    }

    public function requestRefund(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'refund_reason' => 'required|string|min:10',
            ]);

            $order = Order::findOrFail($request->order_id);

            // Check if order is eligible for refund
            if ($order->status !== 'delivered') {
                return redirect()->back()->with('error', 'Only delivered orders can be refunded.');
            }

            if (in_array($order->status, ['refund_requested', 'refunded', 'refund_rejected'])) {
                return redirect()->back()->with('error', 'This order already has a refund request.');
            }

            // Generate refund number
            $refundNo = 'RFN-'.strtoupper(Str::random(10));

            // Update order with refund details
            $order->update([
                'refund_no' => $refundNo,
                'refund_requested_date' => now(),
                'refund_reason' => $request->refund_reason,
                'status' => 'refund_requested',
            ]);

            return redirect()->back()->with('success', 'Refund request submitted successfully! Your refund ID is: '.$refundNo);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error submitting refund request. Please try again.');
        }
    }

    /**
     * Apply search filters to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applySearchFilters($query, Request $request, $searchKey = 'search')
    {
        if ($request->has($searchKey) && $request->get($searchKey) != '') {
            $search = $request->get($searchKey);
            $query->where(function ($q) use ($search) {
                $q->where('order_no', 'like', '%'.$search.'%')
                    ->orWhere('created_at', 'like', '%'.$search.'%')
                    ->orWhere('total_amount', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        return $query;
    }
}
