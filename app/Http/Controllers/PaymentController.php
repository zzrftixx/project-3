<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;
use App\Models\SalesReport;
use Illuminate\Support\Str;
use Midtrans\Notification;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;

class PaymentController extends Controller
{
    /**
     * Tampilkan form checkout dengan data keranjang.
     */
    public function showCheckout()
    {
        $cartItems = CartItem::with(['barang', 'paket', 'komponen'])
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong');
        }

        $subtotal = $cartItems->sum(function ($item) {
            if ($item->barang) {
                return $item->barang->price * $item->quantity;
            } elseif ($item->paket) {
                return $item->paket->price * $item->quantity;
            } elseif ($item->komponen) {
                return $item->komponen->harga * $item->quantity;
            }
            return 0;
        });

        // Clear any login-related errors that might be carried over
        session()->forget(['errors']);

        return view('keranjang.checkout', compact('cartItems', 'subtotal'));
    }

    /**
     * Proses data checkout dan lanjut ke pembayaran.
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address_street' => 'required|string|max:255',
            'address_city' => 'required|string|max:255',
            'address_province' => 'required|string|max:255',
            'address_postal_code' => 'required|string|max:10',
            'shipping_method' => 'required|in:JNE,J&T,Grab,Gojek,ambil di toko',
            'address_notes' => 'nullable|string|max:500',
        ]);

        // Ambil data keranjang
        $cartItems = CartItem::with(['barang', 'paket', 'komponen'])
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong');
        }

        // Hitung total
        $total = $cartItems->sum(function ($item) {
            if ($item->barang) {
                return $item->barang->price * $item->quantity;
            } elseif ($item->paket) {
                return $item->paket->price * $item->quantity;
            } elseif ($item->komponen) {
                return $item->komponen->harga * $item->quantity;
            }
            return 0;
        });

        // Generate order_id unik
        $orderId = 'ORD' . time() . Auth::id();

        // Buat order dengan status pending
        $orderData = [
            'order_id' => $orderId,
            'user_id' => Auth::id(),
            'total' => $total,
            'status' => 'pending',
        ];

        // Tambahkan data checkout
        $checkoutFields = [
            'full_name', 'email', 'phone', 'address_street', 'address_city',
            'address_province', 'address_postal_code', 'shipping_method', 'address_notes'
        ];
        foreach ($checkoutFields as $field) {
            $orderData[$field] = $request->input($field);
        }

        $order = Order::create($orderData);

        // Buat order items
        foreach ($cartItems as $item) {
            $orderItemData = [
                'order_id' => $order->id, // menggunakan id order, bukan order_id
                'quantity' => $item->quantity,
            ];

            if ($item->barang_id) {
                $orderItemData['barang_id'] = $item->barang_id;
                $orderItemData['price'] = $item->barang->price;
            } elseif ($item->paket_id) {
                $orderItemData['paket_id'] = $item->paket_id;
                $orderItemData['price'] = $item->paket->price;
            } elseif ($item->komponen_id) {
                $orderItemData['komponen_id'] = $item->komponen_id;
                $orderItemData['price'] = $item->komponen->harga;
            }

            OrderItem::create($orderItemData);
        }

        // Hapus keranjang setelah order dibuat
        CartItem::where('user_id', Auth::id())->delete();

        // Simpan order_id ke session untuk digunakan di createTransaction
        session(['current_order_id' => $orderId]);

        // Redirect ke halaman pembayaran
        return redirect()->route('payment.page');
    }

    /**
     * Menampilkan halaman pembayaran.
     */
    public function showPayment()
    {
        return view('keranjang.payment');
    }

    /**
     * Membuat transaksi pembayaran dan mengirim Snap Token ke frontend.
     */
    public function createTransaction(Request $request)
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        // Ambil order_id dari session
        $orderId = session('current_order_id');

        if (!$orderId) {
            return response()->json(['error' => 'Order ID tidak ditemukan'], 400);
        }

        // Ambil order dan items
        $order = Order::with('items.barang', 'items.paket', 'items.komponen')->where('order_id', $orderId)->first();

        if (!$order) {
            return response()->json(['error' => 'Order tidak ditemukan'], 400);
        }

        $total = $order->total;

        // Get checkout data from session
        $checkoutData = session('checkout_data', []);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int)$total, // konversi ke integer untuk IDR
            ],
            'callbacks' => [
                'notification_url' => route('payment.callback'),
            ],
            'customer_details' => [
                'first_name' => $checkoutData['full_name'] ?? Auth::user()->name,
                'last_name' => '',
                'email' => $checkoutData['email'] ?? Auth::user()->email,
                'phone' => $checkoutData['phone'] ?? Auth::user()->phone ?? '08123456789',
                'billing_address' => [
                    'first_name' => $checkoutData['full_name'] ?? Auth::user()->name,
                    'last_name' => '',
                    'address' => $checkoutData['address_street'] ?? '',
                    'city' => $checkoutData['address_city'] ?? '',
                    'postal_code' => $checkoutData['address_postal_code'] ?? '',
                    'phone' => $checkoutData['phone'] ?? Auth::user()->phone ?? '08123456789',
                    'country_code' => 'IDN'
                ],
                'shipping_address' => [
                    'first_name' => $checkoutData['full_name'] ?? Auth::user()->name,
                    'last_name' => '',
                    'address' => $checkoutData['address_street'] ?? '',
                    'city' => $checkoutData['address_city'] ?? '',
                    'postal_code' => $checkoutData['address_postal_code'] ?? '',
                    'phone' => $checkoutData['phone'] ?? Auth::user()->phone ?? '08123456789',
                    'country_code' => 'IDN'
                ],
            ],
            'item_details' => $order->items->map(function ($item) {
                $name = '';
                if ($item->barang) {
                    $name = $item->barang->name;
                } elseif ($item->paket) {
                    $name = $item->paket->name;
                } elseif ($item->komponen) {
                    $name = $item->komponen->nama;
                }
                return [
                    'id' => $item->id,
                    'price' => (int)$item->price, // konversi ke integer untuk IDR
                    'quantity' => $item->quantity,
                    'name' => $name,
                ];
            })->toArray(),
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Simpan data ke sales_reports dengan status pending, hindari duplikasi order_id
            $barangList = $order->items->map(function ($item) {
                $name = '';
                if ($item->barang) {
                    $name = $item->barang->name;
                } elseif ($item->paket) {
                    $name = $item->paket->name;
                } elseif ($item->komponen) {
                    $name = $item->komponen->nama;
                }
                return $name . ' (x' . $item->quantity . ')';
            })->implode(', ');

            SalesReport::updateOrCreate(
            ['order_id' => $orderId],
            [
                'user_id' => Auth::id(),
                'total' => $total,
                'status' => 'pending',
                'transaction_date' => now(),
                'barang' => $barangList, // ✅ simpan nama barang di kolom 'barang'
                // Store checkout data in sales_reports for callback use
                'checkout_data' => json_encode($checkoutData),
            ]
        );

            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memproses pembayaran: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Callback dari Midtrans untuk update status pembayaran.
     */
    public function handleCallback(Request $request)
    {
        try {
            $notification = new \Midtrans\Notification();

            $transactionStatus = $notification->transaction_status;
            $orderId = $notification->order_id;

            // Log callback data for debugging
            \Log::info('Midtrans Callback Received', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payment_type' => $notification->payment_type ?? null,
                'gross_amount' => $notification->gross_amount ?? null,
                'request_data' => $request->all(),
            ]);

            $salesReport = SalesReport::where('order_id', $orderId)->first();

            if ($salesReport) {
                $userId = $salesReport->user_id;

                // Log sales report data
                \Log::info('Sales Report Found', [
                    'sales_report_id' => $salesReport->id,
                    'user_id' => $userId,
                    'checkout_data' => $salesReport->checkout_data,
                ]);

                $order = Order::with('items.barang')->where('order_id', $orderId)->first();

                if (in_array($transactionStatus, ['settlement', 'capture'])) {
                    $salesReport->update([
                        'status' => 'completed',
                        'transaction_date' => now(),
                    ]);

                    if ($order) {
                        $order->update(['status' => 'completed']);

                        // Kurangi stok barang setelah pembayaran berhasil
                        foreach ($order->items as $item) {
                            if ($item->barang) {
                                $barang = $item->barang;
                                $barang->stock = max(0, $barang->stock - $item->quantity);
                                $barang->save();
                            }
                        }
                    }

                } elseif ($transactionStatus == 'pending') {
                    $salesReport->update(['status' => 'pending']);
                    if ($order) {
                        $order->update(['status' => 'pending']);
                    }
                } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                    $salesReport->update(['status' => 'cancelled']);
                    if ($order) {
                        $order->update(['status' => 'cancelled']);
                    }
                }
            } else {
                // Jika belum ada, buat data sales_reports baru
                \App\Models\SalesReport::create([
                    'order_id' => $orderId,
                    'user_id' => null,
                    'total' => 0,
                    'status' => $transactionStatus == 'pending' ? 'pending' : ($transactionStatus == 'settlement' || $transactionStatus == 'capture' ? 'completed' : 'cancelled'),
                    'transaction_date' => now(),
                ]);
            }

            return response()->json(['message' => 'Callback processed']);
        } catch (\Exception $e) {
            \Log::error('Callback Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            \Log::error('Callback Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Callback processing failed: ' . $e->getMessage()], 500);
        }
    }
}