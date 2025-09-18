<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use App\Models\Review;
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
        try {
            $period = $request->input('period', '30_days');
            $dates = $this->getDateRange($period, $request->input('start_date'), $request->input('end_date'));

            $statistics = $this->getStatistics();
            $recentActivity = $this->getRecentActivity();
            $analytics = $this->getAnalytics($dates);

            return response()->json([
                'success' => true,
                'message' => 'Dashboard data retrieved successfully.',
                'data' => [
                    'statistics' => $statistics,
                    'recent_activity' => $recentActivity,
                    'analytics' => $this->formatAnalytics($analytics, $dates),
                ],
                'code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching dashboard data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the date range for analytics.
     */
    private function getDateRange(string $period, ?string $startDate, ?string $endDate): array
    {
        if ($startDate && $endDate) {
            return [Carbon::parse($startDate), Carbon::parse($endDate)];
        }

        return match ($period) {
            '7_days' => [Carbon::now()->subDays(7), Carbon::now()],
            '90_days' => [Carbon::now()->subDays(90), Carbon::now()],
            '1_year' => [Carbon::now()->subYear(), Carbon::now()],
            default => [Carbon::now()->subDays(30), Carbon::now()],
        };
    }

    /**
     * Get general statistics.
     */
    private function getStatistics(): array
    {
        $productStats = Product::select(
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active')
        )->first();

        $categoryStats = Category::select(
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active')
        )->first();

        $orderStats = Order::select(
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending'),
            DB::raw('SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as completed'),
            DB::raw('SUM(CASE WHEN status = "delivered" THEN COALESCE(total_amount,0) ELSE 0 END) as total_revenue')
        )->first();

        $userStats = User::select(
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as new')
        )->first();

        $totalReviews = Review::count();

        $totalVisitors = 2000; // Placeholder
        $conversionRate = $totalVisitors > 0 ? ($orderStats->total / $totalVisitors) * 100 : 0;

        return [
            'total_products' => $productStats->total,
            'active_products' => $productStats->active,
            'total_categories' => $categoryStats->total,
            'active_categories' => $categoryStats->active,
            'total_orders' => $orderStats->total,
            'pending_orders' => $orderStats->pending,
            'completed_orders' => $orderStats->completed,
            'total_users' => $userStats->total,
            'total_reviews' => $totalReviews,
            'total_revenue' => round($orderStats->total_revenue, 2),
            'new_customers' => $userStats->new,
            'conversion_rate' => round($conversionRate, 2),
        ];
    }

    /**
     * Get recent activity (orders, products, reviews).
     */
    private function getRecentActivity(): array
    {
        return [
            'recent_orders' => $this->formatRecentOrders(Order::with(['user', 'items.product'])->latest()->limit(10)->get()),
            'recent_products' => $this->formatRecentProducts(Product::with(['categories', 'details'])->latest()->limit(10)->get()),
            'recent_reviews' => $this->formatRecentReviews(Review::with(['user', 'product'])->latest()->limit(10)->get()),
        ];
    }

    /**
     * Get analytics data (sales, orders, users).
     */
    private function getAnalytics(array $dates): array
    {
        [$startDate, $endDate] = $dates;

        $topProducts = Product::join('order_items', 'order_items.product_detail_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        $topSellingProductsByCategory = DB::table('order_items')
            ->join('products', 'order_items.product_detail_id', '=', 'products.id')
            ->join('categories_products', 'products.id', '=', 'categories_products.product_id')
            ->join('categories', 'categories_products.category_id', '=', 'categories.id')
            ->select(
                'categories.name as category_name',
                'products.name as product_name',
                DB::raw('SUM(order_items.quantity) as total_quantity')
            )
            ->groupBy('categories.name', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();


        // Order Analytics
        $orderData = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as order_count'))
            ->groupBy('date')->orderBy('date')->get();

        $orderStatsForPeriod = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('AVG(CASE WHEN status = "completed" THEN total_amount ELSE NULL END) as average_order_value')
            )->first();

        // User Analytics
        $userRegistrationData = User::select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as user_count'))
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')->orderBy('year')->orderBy('month')->get();

        $userStats = User::select(
            DB::raw('COUNT(*) as total_users'),
            DB::raw('SUM(CASE WHEN EXISTS (SELECT 1 FROM orders WHERE orders.user_id = users.id) THEN 1 ELSE 0 END) as active_users')
        )->first();

        $userRoleDistribution = User::with('roles')->get()->groupBy(function ($user) {
            return $user->roles->isEmpty() ? 'No Role' : $user->roles->first()->name;
        })->map(fn ($users) => $users->count());

        return [
            'sales' => [
                'period' => request()->input('period', '30_days'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'top_products' => $topProducts,
                'order_status_distribution' => $this->getOrderStatusDistribution($dates),
                'top_selling_products_by_category' => $topSellingProductsByCategory,
            ],
            'orders' => [
                'period' => request()->input('period', '30_days'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'order_data' => $orderData,
                'total_orders' => $orderStatsForPeriod->total_orders,
                'average_order_value' => $orderStatsForPeriod->average_order_value ? round($orderStatsForPeriod->average_order_value, 2) : 0,
                'order_status_distribution' => $this->getOrderStatusDistribution($dates),
            ],
            'users' => [
                'user_registration_data' => $userRegistrationData,
                'user_role_distribution' => $userRoleDistribution,
                'total_users' => $userStats->total_users,
                'active_users' => $userStats->active_users,
            ],
        ];
    }

    /**
     * Format analytics data for the API response.
     */
    private function formatAnalytics(array $analytics, array $dates): array
    {
        return [
            'sales' => $this->formatSalesAnalytics($analytics['sales'], $dates),
            'orders' => $this->formatOrdersAnalytics($analytics['orders'], $dates),
            'users' => $this->formatUsersAnalytics($analytics['users'], $dates),
        ];
    }

    /**
     * Format sales analytics data for the API response.
     */
    private function formatSalesAnalytics(array $salesAnalytics, array $dates): array
    {
        [$startDate, $endDate] = $dates;

        return [
            'period' => request()->input('period', '30_days'),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'top_products' => $salesAnalytics['top_products'],
            'order_status_distribution' => $salesAnalytics['order_status_distribution'],
            'top_selling_products_by_category' => $salesAnalytics['top_selling_products_by_category'],
        ];
    }

    /**
     * Format orders analytics data for the API response.
     */
    private function formatOrdersAnalytics(array $ordersAnalytics, array $dates): array
    {
        [$startDate, $endDate] = $dates;

        return [
            'period' => request()->input('period', '30_days'),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'order_data' => $ordersAnalytics['order_data'],
            'total_orders' => $ordersAnalytics['total_orders'],
            'average_order_value' => $ordersAnalytics['average_order_value'],
            'order_status_distribution' => $ordersAnalytics['order_status_distribution'],
        ];
    }

    /**
     * Format users analytics data for the API response.
     */
    private function formatUsersAnalytics(array $usersAnalytics, array $dates): array
    {
        [$startDate, $endDate] = $dates;

        return [
            'period' => request()->input('period', '30_days'),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'user_registration_data' => $usersAnalytics['user_registration_data'],
            'user_role_distribution' => $usersAnalytics['user_role_distribution'],
            'total_users' => $usersAnalytics['total_users'],
            'active_users' => $usersAnalytics['active_users'],
        ];
    }

    /**
     * Format recent orders for the API response.
     */
    private function formatRecentOrders($orders): mixed
    {
        return $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'total' => $order->total_amount,
                'created_at' => $order->created_at,
                'user' => $order->user ? ['name' => $order->user->name, 'email' => $order->user->email] : null,
                'items' => $order->items->map(function ($item) {
                    return [
                        'name' => $item->product ? $item->product->name : 'Product Deleted',
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                    ];
                }),
            ];
        });
    }

    /**
     * Format recent products for the API response.
     */
    private function formatRecentProducts($products): mixed
    {
        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'is_active' => $product->is_active,
                'categories' => $product->categories->pluck('name')->toArray(),
            ];
        });
    }

    /**
     * Format recent reviews for the API response.
     */
    private function formatRecentReviews($reviews): mixed
    {
        return $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at,
                'user' => ['name' => $review->user?->name ?? 'Deleted User', 'email' => $review->user?->email],
                'product' => ['id' => $review->product?->id, 'name' => $review->product?->name ?? 'Product Deleted'],
            ];
        });
    }

    /**
     * Get order status distribution for the given date range.
     */
    private function getOrderStatusDistribution(array $dates): array
    {
        [$startDate, $endDate] = $dates;

        $statusDistribution = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'status',
                DB::raw('COUNT(*) as count'),
                DB::raw('ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM orders WHERE created_at BETWEEN ? AND ?), 2) as percentage')
            )
            ->addBinding([$startDate, $endDate], 'select')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->status => [
                        'count' => $item->count,
                        'percentage' => (float) $item->percentage
                    ]
                ];
            });

        // Ensure all statuses are present in the result
        $allStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
        $result = [];

        foreach ($allStatuses as $status) {
            $result[$status] = $statusDistribution[$status] ?? [
                'count' => 0,
                'percentage' => 0.0
            ];
        }

        return $result;
    }
}
