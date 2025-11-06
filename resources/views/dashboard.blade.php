@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50" style='font-family: Inter, "Noto Sans", sans-serif;'>
  <!-- Header -->
  <div class="flex flex-wrap justify-between gap-3 p-4">
    <p class="text-[#0d151c] tracking-light text-[32px] font-bold leading-tight min-w-72">Dashboard</p>
  </div>

  <!-- Welcome Message -->
  <div class="px-4 py-3">
    <div class="flex overflow-hidden rounded-xl border border-[#cedce8] bg-white shadow-sm">
      <div class="p-6 text-gray-900">
        {{ __("You're logged in!") }}
      </div>
    </div>
  </div>
</div>
@endsection
