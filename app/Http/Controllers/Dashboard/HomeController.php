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
     * Unified dashboard overview endpoint
     */
    public function overview(Request $request): JsonResponse
    {
        // Get period and dates for analytics (shared between sales & revenue)
        $period = $request->input('period', '30_days');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

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
                default:
                    $startDate = Carbon::now()->subDays(30);
                    $endDate = Carbon::now();
            }
        }

        // 1. Statistics
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

        // 2. Recent Orders
        $recentOrders = Order::with(['user', 'items.product'])
            ->latest()
            ->limit(10)
            ->get();

        // 3. Recent Products
        $recentProducts = Product::with(['categories', 'details'])
            ->latest()
            ->limit(10)
            ->get();

        // 4. Recent Reviews
        $recentReviews = Review::with(['user', 'product'])
            ->latest()
            ->limit(10)
            ->get();

        // 5. Sales Analytics
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

        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_detail_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        $orderStatusDistribution = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $salesAnalytics = [
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'sales_data' => $salesData,
            'top_products' => $topProducts,
            'order_status_distribution' => $orderStatusDistribution,
        ];

        // 6. Revenue Analytics
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

        $totalRevenue = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');

        $averageOrderValue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->avg('total_amount');

        $revenueAnalytics = [
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'revenue_data' => $revenueData,
            'total_revenue' => $totalRevenue,
            'average_order_value' => round($averageOrderValue, 2),
        ];

        // 7. User Analytics
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

        $userRoleDistribution = User::with('roles')
            ->get()
            ->groupBy(function ($user) {
                return $user->roles->isEmpty() ? 'No Role' : $user->roles->first()->name;
            })
            ->map(function ($users) {
                return $users->count();
            });

        $activeUsers = User::whereHas('orders')->count();

        $userAnalytics = [
            'user_registration_data' => $userRegistrationData,
            'user_role_distribution' => $userRoleDistribution,
            'total_users' => User::count(),
            'active_users' => $activeUsers,
        ];

        // Return unified response
        return response()->json([
            'message' => 'Dashboard overview retrieved successfully.',
            'data' => [
                'statistics' => $stats,
                'recent_orders' => $recentOrders,
                'recent_products' => $recentProducts,
                'recent_reviews' => $recentReviews,
                'sales_analytics' => $salesAnalytics,
                'revenue_analytics' => $revenueAnalytics,
                'user_analytics' => $userAnalytics,
            ],
            'code' => 200,
        ]);
    }
}
