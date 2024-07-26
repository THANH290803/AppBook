<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function checkAvailability(Request $request)
    {
        $items = $request->input('items'); // Expecting an array of item IDs and quantities

        foreach ($items as $item) {
            $book = Book::find($item['id']); // Assuming you have a Book model

            if (!$book || $book->amount < $item['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => "Sản phẩm: {$book->name} đã hết hàng."
                ], 400);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'All products are available for checkout.'
        ]);
    }

    public function statistical()
    {
        // Tổng doanh thu và số lượng sản phẩm bán ra theo tháng
        $monthlyRevenue = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->select(
                DB::raw('YEAR(orders.created_at) as year'),
                DB::raw('MONTH(orders.created_at) as month'),
                DB::raw('SUM(order_details.unit_price) as total_revenue')
            )
            ->where('orders.status', 4)
            ->groupBy(DB::raw('YEAR(orders.created_at), MONTH(orders.created_at)'))
//            ->orderBy(DB::raw('YEAR(orders.created_at), MONTH(orders.created_at)'))
            ->get();

        $totalPendingOrders = DB::table('orders')
            ->where('status', 1)
            ->count();

        // Tổng số đơn hàng theo từng tháng
        $monthlyOrderCounts = DB::table('orders')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->where('orders.status', 4)
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->get();


        // Doanh thu và số lượng sản phẩm bán ra của tháng hiện tại
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $currentMonthRevenue = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->select(
                DB::raw('SUM(order_details.unit_price) as current_month_revenue')
            )
            ->where('orders.status', 4)
            ->whereYear('orders.created_at', $currentYear)
            ->whereMonth('orders.created_at', $currentMonth)
            ->first();

        // Get the total books sold for the current month, including only orders with status 4
        $currentMonthBooksSold = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->select(
                DB::raw('SUM(order_details.quantity) as current_month_books_sold')
            )
            ->where('orders.status', 4)
            ->whereYear('orders.created_at', $currentYear)
            ->whereMonth('orders.created_at', $currentMonth)
            ->first();

        return response()->json([
            'monthly_revenue' => $monthlyRevenue,
            'monthly_order_counts' => $monthlyOrderCounts,
            'current_month_revenue' => $currentMonthRevenue->current_month_revenue,
            'current_month_books_sold' => $currentMonthBooksSold->current_month_books_sold,
            'total_pending_orders' => $totalPendingOrders
        ]);
    }
}
