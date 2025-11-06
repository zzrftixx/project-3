<!-- resources/views/admin/dashboard.blade.php -->

@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="min-h-screen bg-slate-50" style='font-family: Inter, "Noto Sans", sans-serif;'>
  
  <!-- Header -->
  <div class="flex flex-wrap justify-between gap-3 p-4">
    <p class="text-[#0d151c] tracking-light text-[32px] font-bold leading-tight min-w-72">Dashboard</p>
  </div>

  <!-- Sales Overview -->
  <h2 class="text-[#0d151c] text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Ringkasan Operasional</h2>
  <div class="flex flex-wrap gap-4 p-4">
    <!-- Card Penjualan -->
    <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 border border-[#cedce8] bg-white shadow-sm">
      <p class="text-[#0d151c] text-base font-medium leading-normal">Penjualan</p>
      <p class="text-[#0d151c] tracking-light text-2xl font-bold leading-tight">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
      <p class="text-[#078838] text-base font-medium leading-normal">+15%</p>
    </div>
    
    <!-- Card Permintaan Pemasangan -->
    <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 border border-[#cedce8] bg-white shadow-sm">
      <p class="text-[#0d151c] text-base font-medium leading-normal">Permintaan Pemasangan</p>
      <p class="text-[#0d151c] tracking-light text-2xl font-bold leading-tight">{{ $permintaanPemasangan }}</p>
      <p class="text-[#078838] text-base font-medium leading-normal">+5%</p>
    </div>

    <!-- Card Stok Produk -->
    <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 border border-[#cedce8] bg-white shadow-sm">
      <p class="text-[#0d151c] text-base font-medium leading-normal">Stok Produk</p>
      <p class="text-[#0d151c] tracking-light text-2xl font-bold leading-tight">{{ $totalStok }}</p>
      <p class="text-[#078838] text-base font-medium leading-normal">+2%</p>
    </div>
  </div>

  <!-- Ringkasan Penjualan Terbaru -->
  <h2 class="text-[#0d151c] text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Ringkasan Penjualan Terbaru</h2>
  <div class="px-4 py-3">
    <div class="flex overflow-hidden rounded-xl border border-[#cedce8] bg-white shadow-sm">
      <table class="flex-1">
             <thead class="bg-gray-50">
          <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
          </tr>
      </thead>
        <tbody class="bg-white divide-y divide-gray-200"> 
    @forelse($laporan as $report)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ $report->order_id }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ $report->user_name }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                {{ $report->barang_list }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                Rp {{ number_format($report->total, 0, ',', '.') }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                @switch($report->status)
                    @case('completed')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                        @break
                    @case('pending')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @break
                    @case('cancelled')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Dibatalkan</span>
                        @break
                    @default
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($report->status) }}</span>
                @endswitch
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ $report->transaction_date ? \Carbon\Carbon::parse($report->transaction_date)->format('d/m/Y H:i') : '-' }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tidak ada data penjualan</td>
        </tr>
    @endforelse
    </tbody>
      </table>
    </div>
  </div>

  <!-- Charts Section -->
  <h2 class="text-[#0d151c] text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Grafik Penjualan dan Produk</h2>
  <div class="flex flex-wrap gap-4 px-4 py-6">
    <!-- Area Chart -->
    <div class="flex min-w-72 flex-1 flex-col gap-2 rounded-xl border border-[#cedce8] p-6 bg-white shadow-sm">
      <p class="text-[#0d151c] text-base font-medium leading-normal">Tren Penjualan</p>
      <p class="text-[#0d151c] tracking-light text-[32px] font-bold leading-tight truncate">
        Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
      </p>
      <div class="flex gap-1">
        <p class="text-[#49749c] text-base font-normal leading-normal">30 Hari Terakhir</p>
        <p class="text-[#078838] text-base font-medium leading-normal">+15%</p>
      </div>
      <div class="flex min-h-[180px] flex-1 flex-col gap-8 py-4">
        <div class="chart-area">
          <canvas id="myAreaChart" style="height: 180px;"></canvas>
        </div>
      </div>
    </div>

    <!-- Bar Chart -->
    <div class="flex min-w-72 flex-1 flex-col gap-2 rounded-xl border border-[#cedce8] p-6 bg-white shadow-sm">
      <p class="text-[#0d151c] text-base font-medium leading-normal">Penjualan Produk</p>
      <p class="text-[#0d151c] tracking-light text-[32px] font-bold leading-tight truncate">
        Rp {{ number_format($totalPenjualan * 0.8, 0, ',', '.') }}
      </p>
      <div class="flex gap-1">
        <p class="text-[#49749c] text-base font-normal leading-normal">30 Hari Terakhir</p>
        <p class="text-[#e73908] text-base font-medium leading-normal">-5%</p>
      </div>
      <div class="flex min-h-[180px] flex-1 flex-col gap-8 py-4">
        <div class="chart-bar">
          <canvas id="myBarChart" style="height: 180px;"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Area Chart Example (Penjualan Bulanan)
      var ctxArea = document.getElementById('myAreaChart').getContext('2d');
      new Chart(ctxArea, {
        type: 'line',
        data: {
          labels: {!! json_encode($labels) !!},
          datasets: [{
            label: "Penjualan",
            data: {!! json_encode($data) !!},
            fill: true,
            tension: 0.4,
            backgroundColor: 'rgba(231, 237, 244, 0.5)',
            borderColor: '#49749c',
            borderWidth: 3,
            pointBackgroundColor: '#49749c',
            pointBorderColor: '#49749c',
            pointBorderWidth: 2,
            pointRadius: 4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            x: {
              grid: {
                display: false
              },
              ticks: {
                color: '#49749c',
                font: {
                  size: 13,
                  weight: 'bold'
                }
              }
            },
            y: {
              grid: {
                color: '#e7edf4'
              },
              ticks: {
                display: false
              }
            }
          }
        }
      });

      // Bar Chart Example (Penjualan Bulanan)
      var ctxBar = document.getElementById('myBarChart').getContext('2d');
      new Chart(ctxBar, {
        type: 'bar',
        data: {
          labels: {!! json_encode($labels) !!},
          datasets: [{
            label: "Penjualan",
            data: {!! json_encode($data) !!},
            backgroundColor: '#e7edf4',
            borderColor: '#49749c',
            borderWidth: 2,
            borderSkipped: false,
            borderRadius: {
              topLeft: 4,
              topRight: 4
            }
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            x: {
              grid: {
                display: false
              },
              ticks: {
                color: '#49749c',
                font: {
                  size: 13,
                  weight: 'bold'
                }
              }
            },
            y: {
              grid: {
                color: '#e7edf4'
              },
              ticks: {
                display: false
              }
            }
          }
        }
      });
    });
  </script>
@endpush