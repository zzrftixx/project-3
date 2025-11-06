<!-- resources/views/layouts/sidebar.blade.php -->
<div class="w-[280px] min-h-screen bg-white border-r border-[#cedce8]">
  <div class="p-4 h-full">
    <div class="flex flex-col h-full">
      <!-- Brand Section -->
      <div class="flex items-center gap-3 px-2 py-4">
        <img src="{{asset('images/logo_TC.png')}}" alt="Logo" class="h-8 w-8">
        <h1 class="text-[#0d151c] text-base font-bold">TECHNO CENTER</h1>
      </div>
      
      <!-- Navigation Menu -->
      <nav class="mt-8">
        <div class="flex flex-col gap-1">
          <!-- Dashboard -->
          <a href="{{route('admin.dashboard')}}" 
             class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-[#e7edf4]' : 'hover:bg-[#f1f5f9]' }} transition-colors duration-200">
            <div class="text-[#0d151c] w-5 h-5">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor">
                <path d="M224,115.55V208a16,16,0,0,1-16,16H168a16,16,0,0,1-16-16V168a8,8,0,0,0-8-8H112a8,8,0,0,0-8,8v40a16,16,0,0,1-16,16H48a16,16,0,0,1-16-16V115.55a16,16,0,0,1,5.17-11.78l80-75.48.11-.11a16,16,0,0,1,21.53,0,1.14,1.14,0,0,0,.11.11l80,75.48A16,16,0,0,1,224,115.55Z"/>
              </svg>
            </div>
            <span class="text-[#0d151c] text-sm font-medium">Dashboard</span>
          </a>
          
          <!-- Product Management -->
          <a href="{{route('admin.databarang.index')}}" 
             class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.databarang') ? 'bg-[#e7edf4]' : 'hover:bg-[#f1f5f9]' }} transition-colors duration-200">
            <div class="text-[#0d151c] w-5 h-5">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor">
                <path d="M223.68,66.15,135.68,18a15.88,15.88,0,0,0-15.36,0l-88,48.17a16,16,0,0,0-8.32,14v95.64a16,16,0,0,0,8.32,14l88,48.17a15.88,15.88,0,0,0,15.36,0l88-48.17a16,16,0,0,0,8.32-14V80.18A16,16,0,0,0,223.68,66.15ZM128,32l80.34,44-29.77,16.3-80.35-44ZM128,120,47.66,76l33.9-18.56,80.34,44ZM40,90l80,43.78v85.79L40,175.82Zm176,85.78h0l-80,43.79V133.82l32-17.51V152a8,8,0,0,0,16,0V107.55L216,90v85.77Z"/>
              </svg>
            </div>
            <span class="text-[#0d151c] text-sm font-medium">Manajemen Produk</span>
          </a>
          
          <!-- Package Management -->
          <a href="{{route('admin.paket.index')}}" 
             class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.paket.index') ? 'bg-[#e7edf4]' : 'hover:bg-[#f1f5f9]' }} transition-colors duration-200">
            <div class="text-[#0d151c] w-5 h-5">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor">
                <path d="M223.68,66.15,135.68,18a15.88,15.88,0,0,0-15.36,0l-88,48.17a16,16,0,0,0-8.32,14v95.64a16,16,0,0,0,8.32,14l88,48.17a15.88,15.88,0,0,0,15.36,0l88-48.17a16,16,0,0,0,8.32-14V80.18A16,16,0,0,0,223.68,66.15ZM128,32l80.34,44-29.77,16.3-80.35-44ZM128,120,47.66,76l33.9-18.56,80.34,44ZM40,90l80,43.78v85.79L40,175.82Zm176,85.78h0l-80,43.79V133.82l32-17.51V152a8,8,0,0,0,16,0V107.55L216,90v85.77Z"/>
              </svg>
            </div>
            <span class="text-[#0d151c] text-sm font-medium">Manajemen Paket</span>
          </a>
          
          <!-- Komponen CCTV Management (Admin) -->
          <a href="{{ route('admin.komponen.index') }}"
             class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.komponen.*') ? 'bg-[#e7edf4]' : 'hover:bg-[#f1f5f9]' }} transition-colors duration-200">
            <div class="text-[#0d151c] w-5 h-5">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
            </div>
            <span class="text-[#0d151c] text-sm font-medium">Komponen CCTV</span>
          </a>
          
          <!-- Sales Reports -->
          <a href="{{route('admin.laporan-penjualan.index')}}" 
             class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.laporan-penjualan') ? 'bg-[#e7edf4]' : 'hover:bg-[#f1f5f9]' }} transition-colors duration-200">
            <div class="text-[#0d151c] w-5 h-5">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor">
                <path d="M216,40H136V24a8,8,0,0,0-16,0V40H40A16,16,0,0,0,24,56V176a16,16,0,0,0,16,16H79.36L57.75,219a8,8,0,0,0,12.5,10l29.59-37h56.32l29.59,37a8,8,0,1,0,12.5-10l-21.61-27H216a16,16,0,0,0,16-16V56A16,16,0,0,0,216,40Zm0,136H40V56H216V176ZM104,120v24a8,8,0,0,1-16,0V120a8,8,0,0,1,16,0Zm32-16v40a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm32-16v56a8,8,0,0,1-16,0V88a8,8,0,0,1,16,0Z"/>
              </svg>
            </div>
            <span class="text-[#0d151c] text-sm font-medium">Laporan Transaksi</span>
          </a>
          
          <!-- Users Management -->
          <a href="{{ route('admin.customers.index') }}" 
             class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.customers.*') ? 'bg-[#e7edf4]' : 'hover:bg-[#f1f5f9]' }} transition-colors duration-200">
            <div class="text-[#0d151c] w-5 h-5">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5zm0 2c-4.418 0-8 2.015-8 4.5V21h16v-2.5c0-2.485-3.582-4.5-8-4.5z"/>
              </svg>
            </div>
            <span class="text-[#0d151c] text-sm font-medium">Users</span>
          </a>

          <!-- Installation Requests -->
          <a href="{{ route('admin.installations.index') }}" 
             class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.installations.*') ? 'bg-[#e7edf4]' : 'hover:bg-[#f1f5f9]' }} transition-colors duration-200">
            <div class="text-[#0d151c] w-5 h-5">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <span class="text-[#0d151c] text-sm font-medium">Pemasangan</span>
          </a>
    </div>
  </div>
</div>