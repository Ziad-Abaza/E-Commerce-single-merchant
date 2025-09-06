<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use App\Models\Review;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_categories' => Category::count(),
            'active_categories' => Category::where('is_active', true)->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'total_users' => User::count(),
            'total_reviews' => Review::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
        ];

        return response()->json([
            'message' => 'Dashboard statistics retrieved successfully.',
            'data' => $stats,
            'code' => 200,
        ]);
    }

    /**
     * Get recent orders
     */
    public function recentOrders(): JsonResponse
    {
        $orders = Order::with(['user', 'orderItems.product'])
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'message' => 'Recent orders retrieved successfully.',
            'data' => $orders,
            'code' => 200,
        ]);
    }

    /**
     * Get recent products
     */
    public function recentProducts(): JsonResponse
    {
        $products = Product::with(['categories', 'details'])
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'message' => 'Recent products retrieved successfully.',
            'data' => $products,
            'code' => 200,
        ]);
    }

    /**
     * Get recent reviews
     */
    public function recentReviews(): JsonResponse
    {
        $reviews = Review::with(['user', 'product'])
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'message' => 'Recent reviews retrieved successfully.',
            'data' => $reviews,
            'code' => 200,
        ]);
    }

    /**
     * Get sales analytics for a specific period
     */
    public function salesAnalytics(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'nullable|in:7_days,30_days,90_days,1_year',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $period = $request->period ?? '30_days';
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Set date range based on period
        if (!$startDate || !$endDate) {
            switch ($period) {
                case '7_days':
                    $startDate = Carbon::now()->subDays(7);
                    $endDate = Carbon::now();
                    break;
                case '30_days':
                    $startDate = Carbon::now()->subDays(30);
                    $endDate = Carbon::now();
                    break;
                case '90_days':
                    $startDate = Carbon::now()->subDays(90);
                    $endDate = Carbon::now();
                    break;
                case '1_year':
                    $startDate = Carbon::now()->subYear();
                    $endDate = Carbon::now();
                    break;
            }
        }

        // Get sales data grouped by date
        $salesData = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get top selling products
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // Get order status distribution
        $orderStatusDistribution = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'message' => 'Sales analytics retrieved successfully.',
            'data' => [
                'period' => $period,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'sales_data' => $salesData,
                'top_products' => $topProducts,
                'order_status_distribution' => $orderStatusDistribution,
            ],
            'code' => 200,
        ]);
    }

    /**
     * Get revenue analytics
     */
    public function revenueAnalytics(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'nullable|in:7_days,30_days,90_days,1_year',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $period = $request->period ?? '30_days';
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Set date range based on period
        if (!$startDate || !$endDate) {
            switch ($period) {
                case '7_days':
                    $startDate = Carbon::now()->subDays(7);
                    $endDate = Carbon::now();
                    break;
                case '30_days':
                    $startDate = Carbon::now()->subDays(30);
                    $endDate = Carbon::now();
                    break;
                case '90_days':
                    $startDate = Carbon::now()->subDays(90);
                    $endDate = Carbon::now();
                    break;
                case '1_year':
                    $startDate = Carbon::now()->subYear();
                    $endDate = Carbon::now();
                    break;
            }
        }

        // Get revenue data grouped by date
        $revenueData = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as daily_revenue'),
                DB::raw('COUNT(*) as payment_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Calculate total revenue for the period
        $totalRevenue = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');

        // Calculate average order value
        $averageOrderValue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->avg('total_amount');

        return response()->json([
            'message' => 'Revenue analytics retrieved successfully.',
            'data' => [
                'period' => $period,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'revenue_data' => $revenueData,
                'total_revenue' => $totalRevenue,
                'average_order_value' => round($averageOrderValue, 2),
            ],
            'code' => 200,
        ]);
    }

    /**
     * Get user analytics
     */
    public function userAnalytics(): JsonResponse
    {
        // Get user registration data for the last 12 months
        $userRegistrationData = User::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as user_count')
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        // Get user role distribution
        $userRoleDistribution = User::with('roles')
            ->get()
            ->groupBy(function ($user) {
                return $user->roles->isEmpty() ? 'No Role' : $user->roles->first()->name;
            })
            ->map(function ($users) {
                return $users->count();
            });

        // Get active users (users who made at least one order)
        $activeUsers = User::whereHas('orders')->count();

        return response()->json([
            'message' => 'User analytics retrieved successfully.',
            'data' => [
                'user_registration_data' => $userRegistrationData,
                'user_role_distribution' => $userRoleDistribution,
                'total_users' => User::count(),
                'active_users' => $activeUsers,
            ],
            'code' => 200,
        ]);
    }
}
