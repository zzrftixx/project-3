<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Order;
use App\Models\InstallationRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            // Admin dashboard
            // Total stok semua barang
            $totalStok = Barang::sum('stock');

            // Total penjualan dari order yang selesai
            $totalPenjualan = Order::where('status', 'completed')->sum('total');

            // Total permintaan pemasangan
            $permintaanPemasangan = InstallationRequest::count();

            // Penjualan 6 bulan terakhir
            $penjualanBulanan = Order::select(
                    DB::raw('DATE_FORMAT(created_at, "%b %Y") as bulan'),
                    DB::raw('SUM(total) as total')
                )
                ->where('status', 'completed')
                ->where('created_at', '>=', Carbon::now()->subMonths(6)->startOfMonth())
                ->groupBy('bulan')
                ->orderByRaw('MIN(created_at)')
                ->pluck('total', 'bulan')
                ->toArray();

            // Label dan data grafik
            $labels = [];
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $label = Carbon::now()->subMonths($i)->format('M Y');
                $labels[] = $label;
                $data[] = $penjualanBulanan[$label] ?? 0;
            }

            // Ambil data penjualan terbaru
            $laporan = $this->getRecentPurchases();

            return view('admin.dashboard', compact(
                'totalStok',
                'totalPenjualan',
                'permintaanPemasangan',
                'labels',
                'data',
                'laporan'
            ));
        } else {
            // User dashboard
            return view('dashboard');
        }
    }

    /**
     * Mengambil 10 pembelian terbaru beserta user dan barang.
     */
   private function getRecentPurchases()
{
    $orders = Order::with([
            'user:id,name,email',
            'items:id,order_id,barang_id,quantity,price',
            'items.barang:id,name,price' // <-- disesuaikan di sini
        ])
        ->whereHas('items')
        ->latest('created_at')
        ->take(10)
        ->get();

    return $orders->map(function($order) {
        return (object) [
            'id' => $order->id,
            'order_id' => 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'user_name' => optional($order->user)->name ?? 'User tidak ditemukan',
            'user_email' => optional($order->user)->email ?? '-',
            'barang_list' => $this->formatBarangList($order->items),
            'total' => $order->total,
            'status' => $order->status,
            'transaction_date' => $order->created_at
        ];
    });
}

    /**
     * Format daftar barang untuk ditampilkan
     */
    private function formatBarangList($items)
    {
        if ($items->isEmpty()) {
            return 'Tidak ada barang';
        }

        return $items->map(function($item) {
            $barangName = optional($item->barang)->name ?? 'Barang tidak ditemukan';
            return $barangName . ' (x' . $item->quantity . ')';
        })->implode(', ');
    }
}
