@extends('layouts.app')

@section('title', 'Home Keuangan')

@section('styles')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Welcome Header & Info -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-white">
                Home Keuangan
            </h1>
            <p class="text-slate-400 text-sm mt-1">
                Kelola pemasukkan dan pengeluaran Anda secara rinci dalam satu aplikasi terpadu.
            </p>
        </div>
        <!-- Quick Stats Widget & Export Button -->
        <div class="flex items-center space-x-3 self-start">
            <a href="{{ route('expenses.export') }}" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-xl shadow-lg hover:shadow-indigo-600/20 transition duration-150 text-xs flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export Laporan CSV
            </a>
            <div class="glass p-2 px-3 rounded-xl flex items-center space-x-2 text-xs border border-slate-700/50">
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-slate-300 font-mono">Server node: {{ $serverName }}</span>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 px-4 py-3.5 rounded-xl text-sm flex items-center space-x-2 shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Top Cards (Balance, Income, Expense Summaries) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Saldo Aktif Card -->
        <div class="glass rounded-2xl p-6 flex flex-col justify-between relative overflow-hidden border border-slate-700/50 min-h-[160px] shadow-xl group hover:border-emerald-500/30 transition duration-300">
            <!-- Background Glow -->
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition duration-300"></div>
            
            <div class="flex items-center justify-between z-10">
                <span class="text-slate-400 text-xs uppercase tracking-wider font-semibold">Saldo Aktif (Net)</span>
                <div class="p-2 bg-teal-500/10 rounded-lg text-teal-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                    </svg>
                </div>
            </div>
            
            <div class="my-3 z-10">
                <span id="cardBalance" class="text-3xl font-black tracking-tight font-mono {{ $balance >= 0 ? 'text-emerald-400' : 'text-rose-450' }}">
                    Rp {{ number_format($balance, 0, ',', '.') }}
                </span>
            </div>
            
            <div class="text-xs text-slate-400 z-10 flex items-center">
                Saldo bersih tabungan saat ini
            </div>
        </div>

        <!-- Total Pemasukan Card -->
        <div class="glass rounded-2xl p-6 flex flex-col justify-between relative overflow-hidden border border-slate-700/50 min-h-[160px] shadow-xl group hover:border-emerald-500/30 transition duration-300">
            <!-- Background Glow -->
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition duration-300"></div>
            
            <div class="flex items-center justify-between z-10">
                <span class="text-slate-400 text-xs uppercase tracking-wider font-semibold">Total Pemasukan</span>
                <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            
            <div class="my-3 z-10">
                <span id="cardIncome" class="text-3xl font-black tracking-tight font-mono text-emerald-400">
                    Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </span>
            </div>
            
            <div class="text-xs text-slate-400 z-10 flex items-center">
                Akumulasi pemasukan uang masuk
            </div>
        </div>

        <!-- Total Pengeluaran Card -->
        <div class="glass rounded-2xl p-6 flex flex-col justify-between relative overflow-hidden border border-slate-700/50 min-h-[160px] shadow-xl group hover:border-rose-500/30 transition duration-300">
            <!-- Background Glow -->
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-rose-500/10 rounded-full blur-2xl group-hover:bg-rose-500/20 transition duration-300"></div>
            
            <div class="flex items-center justify-between z-10">
                <span class="text-slate-400 text-xs uppercase tracking-wider font-semibold">Total Pengeluaran</span>
                <div class="p-2 bg-rose-500/10 rounded-lg text-rose-450">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                    </svg>
                </div>
            </div>
            
            <div class="my-3 z-10">
                <span id="cardExpense" class="text-3xl font-black tracking-tight font-mono text-rose-450">
                    Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </span>
            </div>
            
            <div class="text-xs text-slate-400 z-10 flex items-center">
                Akumulasi pengeluaran uang keluar
            </div>
        </div>
    </div>

    <!-- Main Content Grid: Forms & Visuals -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Interactive Tabbed Input Form -->
        <div class="glass rounded-2xl p-6 lg:col-span-2 border border-slate-700/50 shadow-xl flex flex-col h-full">
            <!-- Tab Headers -->
            <div class="flex border-b border-slate-800 mb-6">
                <button type="button" id="tabExpenseBtn" onclick="switchFormTab('expense')"
                    class="flex-1 pb-3 text-center text-sm font-semibold border-b-2 border-indigo-500 text-white transition-all flex items-center justify-center">
                    <span class="w-2 h-2 rounded-full bg-rose-500 mr-2"></span>
                    Catat Pengeluaran
                </button>
                <button type="button" id="tabIncomeBtn" onclick="switchFormTab('income')"
                    class="flex-1 pb-3 text-center text-sm font-semibold border-b-2 border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-700 transition-all flex items-center justify-center">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                    Catat Pemasukan
                </button>
            </div>
            
            <!-- Expense Form -->
            <form id="expenseForm" action="{{ route('expenses.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-grow">
                @csrf
                <div>
                    <label for="expense_category_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Kategori Pengeluaran</label>
                    <select id="expense_category_id" name="category_id" required
                        class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-3.5 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        <option value="" disabled selected>Pilih Kategori</option>
                        @foreach($expenseCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="expense_date" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal</label>
                    <input type="date" id="expense_date" name="date" required value="{{ date('Y-m-d') }}"
                        class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-3.5 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>

                <div>
                    <label for="expense_amount" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nominal Pengeluaran (Rp)</label>
                    <input type="number" id="expense_amount" name="amount" min="1" step="1" required placeholder="Contoh: 50000"
                        class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-3.5 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>

                <div>
                    <label for="expense_description" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Deskripsi</label>
                    <input type="text" id="expense_description" name="description" placeholder="Contoh: Makan siang bakso"
                        class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-3.5 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>

                <div class="md:col-span-2 flex justify-end mt-4">
                    <button type="submit"
                        class="bg-transparent border border-red-500 text-red-500 hover:bg-red-600 hover:text-white font-semibold px-6 py-2.5 rounded-xl shadow-lg hover:shadow-red-600/20 transition duration-150 text-sm">
                        Simpan Pengeluaran
                    </button>
                </div>
            </form>

            <!-- Income Form (Hidden by default) -->
            <form id="incomeForm" action="{{ route('incomes.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-grow hidden">
                @csrf
                <div>
                    <label for="income_category_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Kategori Pemasukan</label>
                    <select id="income_category_id" name="category_id" required
                        class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-3.5 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                        <option value="" disabled selected>Pilih Kategori</option>
                        @foreach($incomeCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="income_date" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal</label>
                    <input type="date" id="income_date" name="date" required value="{{ date('Y-m-d') }}"
                        class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-3.5 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                </div>

                <div>
                    <label for="income_amount" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nominal Pemasukan (Rp)</label>
                    <input type="number" id="income_amount" name="amount" min="1" step="1" required placeholder="Contoh: 1500000"
                        class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-3.5 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                </div>

                <div>
                    <label for="income_description" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Deskripsi</label>
                    <input type="text" id="income_description" name="description" placeholder="Contoh: Transfer gaji bulanan"
                        class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-3.5 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm">
                </div>

                <div class="md:col-span-2 flex justify-end mt-4">
                    <button type="submit"
                        class="bg-transparent border border-emerald-500 text-emerald-500 hover:bg-emerald-600 hover:text-white font-semibold px-6 py-2.5 rounded-xl shadow-lg hover:shadow-emerald-600/20 transition duration-150 text-sm">
                        Simpan Pemasukan
                    </button>
                </div>
            </form>
        </div>

        <!-- Doughnut Ratio Chart Card -->
        <div class="glass rounded-2xl p-6 border border-slate-700/50 shadow-xl flex flex-col justify-between">
            <h2 class="text-lg font-bold text-white mb-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                Rasio Keuangan
            </h2>
            
            <div class="relative flex-grow flex items-center justify-center py-4 min-h-[180px]">
                <canvas id="ratioChart" class="max-h-[160px]"></canvas>
                <div id="noChartDataMsg" class="absolute inset-0 flex items-center justify-center text-center text-slate-500 text-sm hidden">
                    Belum ada data visualisasi untuk rentang ini
                </div>
            </div>
            
            <div class="text-center text-xs text-slate-400 border-t border-slate-800/80 pt-3 mt-2">
                Persentase pembagian uang masuk vs uang keluar.
            </div>
        </div>
    </div>

    <!-- Category Breakdowns Graphs -->
    <div id="breakdownSection" class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Expense Breakdown -->
        <div class="glass rounded-2xl p-6 border border-slate-700/50 shadow-xl">
            <h3 class="text-base font-bold text-white mb-4 flex items-center">
                <span class="w-3 h-3 rounded-full bg-rose-500 mr-2"></span>
                Penyebaran Pengeluaran Kategori
            </h3>
            <div class="relative flex items-center justify-center min-h-[200px]">
                <canvas id="expenseCategoryChart" class="max-h-[180px]"></canvas>
                <div id="noExpenseChartDataMsg" class="absolute inset-0 flex items-center justify-center text-center text-slate-500 text-sm hidden">
                    Tidak ada data pengeluaran
                </div>
            </div>
        </div>

        <!-- Income Breakdown -->
        <div class="glass rounded-2xl p-6 border border-slate-700/50 shadow-xl">
            <h3 class="text-base font-bold text-white mb-4 flex items-center">
                <span class="w-3 h-3 rounded-full bg-emerald-500 mr-2"></span>
                Penyebaran Pemasukan Kategori
            </h3>
            <div class="relative flex items-center justify-center min-h-[200px]">
                <canvas id="incomeCategoryChart" class="max-h-[180px]"></canvas>
                <div id="noIncomeChartDataMsg" class="absolute inset-0 flex items-center justify-center text-center text-slate-500 text-sm hidden">
                    Tidak ada data pemasukan
                </div>
            </div>
        </div>
    </div>

    <!-- Integrated Transactions Table Card -->
    <div class="glass rounded-2xl border border-slate-700/50 shadow-xl overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-800 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <h2 class="text-xl font-bold text-white">
                    Riwayat Transaksi Terpadu
                </h2>
            </div>
            
            <!-- Dual Filters (Date Range & Type) -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Date Range Picker -->
                <div class="flex flex-wrap items-center gap-2 bg-slate-900/60 p-2 rounded-xl border border-slate-800 text-xs">
                    <div class="flex items-center space-x-1.5">
                        <span class="text-slate-400 font-semibold">Mulai:</span>
                        <input type="date" id="startDateFilter" onchange="filterDates()" 
                            class="bg-slate-950 border border-slate-750 text-slate-350 rounded-lg px-2 py-1 focus:outline-none focus:ring-1 focus:ring-indigo-500 cursor-pointer">
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <span class="text-slate-400 font-semibold">Selesai:</span>
                        <input type="date" id="endDateFilter" onchange="filterDates()" 
                            class="bg-slate-950 border border-slate-750 text-slate-355 rounded-lg px-2 py-1 focus:outline-none focus:ring-1 focus:ring-indigo-500 cursor-pointer">
                    </div>
                    <button type="button" onclick="resetDateFilter()" 
                        class="text-indigo-400 hover:text-indigo-300 font-bold px-1.5 transition duration-150" title="Reset Rentang Tanggal">
                        Reset
                    </button>
                </div>

                <!-- Type Filters Buttons -->
                <div class="flex items-center bg-slate-900/60 p-1 rounded-xl border border-slate-800">
                    <button type="button" id="filterAllBtn" onclick="filterType('all')" 
                        class="px-3.5 py-1 text-xs font-semibold rounded-lg bg-indigo-650 text-white transition duration-150">
                        Semua
                    </button>
                    <button type="button" id="filterIncomeBtn" onclick="filterType('income')" 
                        class="px-3.5 py-1 text-xs font-semibold rounded-lg text-slate-400 hover:text-slate-200 transition duration-150">
                        Pemasukan
                    </button>
                    <button type="button" id="filterExpenseBtn" onclick="filterType('expense')" 
                        class="px-3.5 py-1 text-xs font-semibold rounded-lg text-slate-400 hover:text-slate-200 transition duration-150">
                        Pengeluaran
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            @if($transactions->isEmpty())
                <div class="p-12 text-center text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-slate-650 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-lg font-medium">Belum ada transaksi</p>
                    <p class="text-sm mt-1">Silakan gunakan form di atas untuk mencatat keuangan Anda.</p>
                </div>
            @else
                <table class="w-full text-left border-collapse" id="transactionsTable">
                    <thead>
                        <tr class="bg-slate-900/50 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-800">
                            <th class="py-3.5 px-6 font-semibold">Tanggal</th>
                            <th class="py-3.5 px-6 font-semibold">Tipe</th>
                            <th class="py-3.5 px-6 font-semibold">Kategori</th>
                            <th class="py-3.5 px-6 font-semibold">Deskripsi</th>
                            <th class="py-3.5 px-6 font-semibold text-right">Nominal</th>
                            <th class="py-3.5 px-6 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-slate-800/25 transition duration-150 text-sm transaction-row" 
                                data-type="{{ $transaction->type }}"
                                data-date="{{ $transaction->date }}">
                                <!-- Tanggal -->
                                <td class="py-4 px-6 text-slate-300 font-mono whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                                </td>
                                
                                <!-- Tipe -->
                                <td class="py-4 px-6 whitespace-nowrap">
                                    @if($transaction->type === 'income')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                            </svg>
                                            Pemasukan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-300 border border-rose-500/20">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                            </svg>
                                            Pengeluaran
                                        </span>
                                    @endif
                                </td>
                                
                                <!-- Kategori -->
                                <td class="py-4 px-6 whitespace-nowrap">
                                    @php
                                        // Dynamic styling based on category name & transaction type
                                        $catName = strtolower($transaction->category->name);
                                        $badgeStyle = 'bg-slate-500/10 text-slate-350 border-slate-500/20';
                                        
                                        if ($transaction->type === 'income') {
                                            if ($catName === 'gaji') {
                                                $badgeStyle = 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20';
                                            } elseif ($catName === 'investasi') {
                                                $badgeStyle = 'bg-cyan-500/10 text-cyan-300 border-cyan-500/20';
                                            } elseif ($catName === 'usaha') {
                                                $badgeStyle = 'bg-amber-500/10 text-amber-300 border-amber-500/20';
                                            } elseif ($catName === 'hadiah') {
                                                $badgeStyle = 'bg-indigo-500/10 text-indigo-300 border-indigo-500/20';
                                            }
                                        } else {
                                            if ($catName === 'kosan') {
                                                $badgeStyle = 'bg-blue-500/10 text-blue-300 border-blue-500/20';
                                            } elseif ($catName === 'makan harian') {
                                                $badgeStyle = 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20';
                                            } elseif ($catName === 'transportasi') {
                                                $badgeStyle = 'bg-amber-500/10 text-amber-300 border-amber-500/20';
                                            } elseif (str_contains($catName, 'internet') || str_contains($catName, 'kuota')) {
                                                $badgeStyle = 'bg-purple-500/10 text-purple-300 border-purple-500/20';
                                            } elseif ($catName === 'hiburan & rekreasi') {
                                                $badgeStyle = 'bg-rose-500/10 text-rose-300 border-rose-500/20';
                                            }
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeStyle }}">
                                        {{ $transaction->category->name }}
                                    </span>
                                </td>
                                
                                <!-- Deskripsi -->
                                <td class="py-4 px-6 text-slate-355 max-w-xs truncate">
                                    {{ $transaction->description ?? '-' }}
                                </td>
                                
                                <!-- Nominal -->
                                <td class="py-4 px-6 text-right font-semibold font-mono whitespace-nowrap">
                                    @if($transaction->type === 'income')
                                        <span class="text-emerald-400">+ Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-rose-450">- Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                
                                <!-- Aksi -->
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Edit Button -->
                                        <button type="button" 
                                            onclick="openEditModal({{ json_encode($transaction) }}, '{{ $transaction->type }}')"
                                            class="text-indigo-400 hover:text-indigo-300 hover:bg-indigo-500/10 p-1.5 rounded-lg transition duration-150"
                                            title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        
                                        <!-- Delete Form -->
                                        <form action="{{ $transaction->type === 'income' ? route('incomes.destroy', $transaction->id) : route('expenses.destroy', $transaction->id) }}" 
                                            method="POST" 
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="text-rose-450 hover:text-rose-400 hover:bg-rose-500/10 p-1.5 rounded-lg transition duration-150"
                                                title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="noFilteredTransactionsMsg" class="p-12 text-center text-slate-500 hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-slate-650 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-lg font-medium">Tidak ada transaksi ditemukan</p>
                    <p class="text-sm mt-1">Coba sesuaikan tipe atau tanggal filter Anda.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Glassmorphic Dynamic Edit Modal (Hidden by Default) -->
<div id="editModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <!-- Backdrop overlay -->
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>
    
    <!-- Modal container -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="glass max-w-lg w-full rounded-2xl shadow-2xl border border-slate-700/50 p-6 relative z-10 transform scale-95 transition-transform duration-300 ease-out" id="modalContent">
            
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                <h3 class="text-lg font-bold text-white flex items-center" id="modalTitle">
                    Edit Transaksi
                </h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form inside Modal -->
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <!-- Category Select -->
                <div>
                    <label for="edit_category_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Kategori</label>
                    <select id="edit_category_id" name="category_id" required
                        class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-3.5 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        <!-- Loaded dynamically via JS -->
                    </select>
                </div>

                <!-- Date Input -->
                <div>
                    <label for="edit_date" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal</label>
                    <input type="date" id="edit_date" name="date" required
                        class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-3.5 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>

                <!-- Amount Input -->
                <div>
                    <label for="edit_amount" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nominal (Rp)</label>
                    <input type="number" id="edit_amount" name="amount" min="1" step="1" required
                        class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-3.5 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>

                <!-- Description Input -->
                <div>
                    <label for="edit_description" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Deskripsi</label>
                    <input type="text" id="edit_description" name="description"
                        class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-3.5 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>

                <!-- Action buttons -->
                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-800">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 border border-slate-700 rounded-xl text-slate-300 text-sm font-semibold hover:bg-slate-800 hover:text-white transition duration-150">
                        Batal
                    </button>
                    <button type="submit" id="saveEditBtn"
                        class="bg-indigo-650 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl text-sm font-semibold shadow-lg hover:shadow-indigo-600/20 transition duration-150">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // JSON Data untuk kategori & master transaksi
    const expenseCategories = @json($expenseCategories);
    const incomeCategories = @json($incomeCategories);
    const allTransactions = @json($transactions);

    // Filter States
    let activeType = 'all';
    let startDate = '';
    let endDate = '';

    // Fungsionalitas Tab Form Input
    function switchFormTab(type) {
        const tabExpenseBtn = document.getElementById('tabExpenseBtn');
        const tabIncomeBtn = document.getElementById('tabIncomeBtn');
        const expenseForm = document.getElementById('expenseForm');
        const incomeForm = document.getElementById('incomeForm');

        if (type === 'expense') {
            tabExpenseBtn.className = "flex-1 pb-3 text-center text-sm font-semibold border-b-2 border-indigo-500 text-white transition-all flex items-center justify-center";
            tabIncomeBtn.className = "flex-1 pb-3 text-center text-sm font-semibold border-b-2 border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-700 transition-all flex items-center justify-center";
            expenseForm.classList.remove('hidden');
            incomeForm.classList.add('hidden');
        } else {
            tabExpenseBtn.className = "flex-1 pb-3 text-center text-sm font-semibold border-b-2 border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-700 transition-all flex items-center justify-center";
            tabIncomeBtn.className = "flex-1 pb-3 text-center text-sm font-semibold border-b-2 border-emerald-500 text-white transition-all flex items-center justify-center";
            expenseForm.classList.add('hidden');
            incomeForm.classList.remove('hidden');
        }
    }

    // Fungsionalitas Filter Transaksi (Gabungan Type dan Month)
    function filterType(type) {
        activeType = type;
        
        // Update styling tombol filter tipe
        const filterAllBtn = document.getElementById('filterAllBtn');
        const filterIncomeBtn = document.getElementById('filterIncomeBtn');
        const filterExpenseBtn = document.getElementById('filterExpenseBtn');
        
        [filterAllBtn, filterIncomeBtn, filterExpenseBtn].forEach(btn => {
            btn.className = "px-3.5 py-1 text-xs font-semibold rounded-lg text-slate-400 hover:text-slate-200 transition duration-150";
        });
        
        if (type === 'all') {
            filterAllBtn.className = "px-3.5 py-1 text-xs font-semibold rounded-lg bg-indigo-650 text-white transition duration-150";
        } else if (type === 'income') {
            filterIncomeBtn.className = "px-3.5 py-1 text-xs font-semibold rounded-lg bg-emerald-600 text-white transition duration-150";
        } else {
            filterExpenseBtn.className = "px-3.5 py-1 text-xs font-semibold rounded-lg bg-rose-600 text-white transition duration-150";
        }
        
        applyFilters();
    }

    function filterDates() {
        startDate = document.getElementById('startDateFilter').value;
        endDate = document.getElementById('endDateFilter').value;
        applyFilters();
    }

    function resetDateFilter() {
        document.getElementById('startDateFilter').value = '';
        document.getElementById('endDateFilter').value = '';
        startDate = '';
        endDate = '';
        applyFilters();
    }

    function applyFilters() {
        const rows = document.querySelectorAll('.transaction-row');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const rowType = row.getAttribute('data-type');
            const rowDate = row.getAttribute('data-date'); // format YYYY-MM-DD
            
            const matchesType = (activeType === 'all' || rowType === activeType);
            
            let matchesDate = true;
            if (startDate && rowDate < startDate) {
                matchesDate = false;
            }
            if (endDate && rowDate > endDate) {
                matchesDate = false;
            }
            
            if (matchesType && matchesDate) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });

        // Tampilkan pesan kosong jika tidak ada transaksi yang cocok
        const noFilteredTransactionsMsg = document.getElementById('noFilteredTransactionsMsg');
        if (noFilteredTransactionsMsg) {
            if (visibleCount === 0 && rows.length > 0) {
                noFilteredTransactionsMsg.classList.remove('hidden');
            } else {
                noFilteredTransactionsMsg.classList.add('hidden');
            }
        }

        // Filter data Master Array JS untuk kalkulasi ulang Ringkasan & Grafik
        const filteredData = allTransactions.filter(t => {
            if (startDate && t.date < startDate) return false;
            if (endDate && t.date > endDate) return false;
            return true;
        });

        let filteredIncome = 0;
        let filteredExpense = 0;

        filteredData.forEach(t => {
            const amount = parseFloat(t.amount);
            if (t.type === 'income') {
                filteredIncome += amount;
            } else {
                filteredExpense += amount;
            }
        });

        const filteredBalance = filteredIncome - filteredExpense;

        // Update Kartu Angka
        document.getElementById('cardBalance').innerText = formatRupiah(filteredBalance, true);
        document.getElementById('cardIncome').innerText = formatRupiah(filteredIncome);
        document.getElementById('cardExpense').innerText = formatRupiah(filteredExpense);

        // Ubah warna text saldo
        const cardBalance = document.getElementById('cardBalance');
        if (filteredBalance >= 0) {
            cardBalance.className = "text-3xl font-black tracking-tight font-mono text-emerald-400";
        } else {
            cardBalance.className = "text-3xl font-black tracking-tight font-mono text-rose-450";
        }

        // Update Charts
        updateCharts(filteredData, filteredIncome, filteredExpense);
    }

    function formatRupiah(amount, isBalance = false) {
        const sign = amount < 0 ? '-' : '';
        const absVal = Math.abs(amount);
        const formatted = 'Rp ' + absVal.toLocaleString('id-ID');
        return sign + formatted;
    }

    // Fungsionalitas Modal Edit Dinamis
    const editModal = document.getElementById('editModal');
    const modalContent = document.getElementById('modalContent');
    const editForm = document.getElementById('editForm');
    const modalTitle = document.getElementById('modalTitle');
    const editCategorySelect = document.getElementById('edit_category_id');
    const editDateInput = document.getElementById('edit_date');
    const editAmountInput = document.getElementById('edit_amount');
    const editDescInput = document.getElementById('edit_description');
    const saveEditBtn = document.getElementById('saveEditBtn');

    function openEditModal(transaction, type) {
        if (type === 'expense') {
            modalTitle.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-rose-450" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Edit Pengeluaran
            `;
            editForm.action = `/expenses/${transaction.id}`;
            saveEditBtn.className = "bg-indigo-650 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl text-sm font-semibold shadow-lg hover:shadow-indigo-600/20 transition duration-150";
            
            editCategorySelect.innerHTML = '';
            expenseCategories.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat.id;
                opt.innerText = cat.name;
                editCategorySelect.appendChild(opt);
            });
        } else {
            modalTitle.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Edit Pemasukan
            `;
            editForm.action = `/incomes/${transaction.id}`;
            saveEditBtn.className = "bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-xl text-sm font-semibold shadow-lg hover:shadow-emerald-600/20 transition duration-150";
            
            editCategorySelect.innerHTML = '';
            incomeCategories.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat.id;
                opt.innerText = cat.name;
                editCategorySelect.appendChild(opt);
            });
        }
        
        editCategorySelect.value = transaction.category_id;
        editDateInput.value = transaction.date;
        editAmountInput.value = Math.round(transaction.amount);
        editDescInput.value = transaction.description || '';
        
        editModal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    }

    function closeEditModal() {
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        setTimeout(() => {
            editModal.classList.add('hidden');
        }, 150);
    }

    // Variabel Global untuk Chart.js Instance
    let ratioChartObj = null;
    let expenseChartObj = null;
    let incomeChartObj = null;

    function updateCharts(filteredData, totalIncome, totalExpense) {
        // Toggle diagram / pesan data kosong
        const ratioChartCanvas = document.getElementById('ratioChart');
        const noChartDataMsg = document.getElementById('noChartDataMsg');
        const breakdownSection = document.getElementById('breakdownSection');
        
        if (totalIncome === 0 && totalExpense === 0) {
            if (ratioChartCanvas) ratioChartCanvas.classList.add('hidden');
            if (noChartDataMsg) noChartDataMsg.classList.remove('hidden');
            if (breakdownSection) breakdownSection.classList.add('hidden');
            return;
        } else {
            if (ratioChartCanvas) ratioChartCanvas.classList.remove('hidden');
            if (noChartDataMsg) noChartDataMsg.classList.add('hidden');
            if (breakdownSection) breakdownSection.classList.remove('hidden');
        }

        // 1. Rasio Chart Update
        if (ratioChartObj) {
            ratioChartObj.data.datasets[0].data = [totalIncome, totalExpense];
            ratioChartObj.update();
        }

        // 2. Expense Category Chart Update
        const expenses = filteredData.filter(t => t.type === 'expense');
        const expenseBreakdown = {};
        expenses.forEach(t => {
            const name = t.category.name;
            expenseBreakdown[name] = (expenseBreakdown[name] || 0) + parseFloat(t.amount);
        });

        const expenseCanvas = document.getElementById('expenseCategoryChart');
        const noExpenseMsg = document.getElementById('noExpenseChartDataMsg');
        
        if (Object.keys(expenseBreakdown).length === 0) {
            if (expenseCanvas) expenseCanvas.classList.add('hidden');
            if (noExpenseMsg) noExpenseMsg.classList.remove('hidden');
        } else {
            if (expenseCanvas) expenseCanvas.classList.remove('hidden');
            if (noExpenseMsg) noExpenseMsg.classList.add('hidden');
            
            if (expenseChartObj) {
                expenseChartObj.data.labels = Object.keys(expenseBreakdown);
                expenseChartObj.data.datasets[0].data = Object.values(expenseBreakdown);
                expenseChartObj.update();
            }
        }

        // 3. Income Category Chart Update
        const incomes = filteredData.filter(t => t.type === 'income');
        const incomeBreakdown = {};
        incomes.forEach(t => {
            const name = t.category.name;
            incomeBreakdown[name] = (incomeBreakdown[name] || 0) + parseFloat(t.amount);
        });

        const incomeCanvas = document.getElementById('incomeCategoryChart');
        const noIncomeMsg = document.getElementById('noIncomeChartDataMsg');

        if (Object.keys(incomeBreakdown).length === 0) {
            if (incomeCanvas) incomeCanvas.classList.add('hidden');
            if (noIncomeMsg) noIncomeMsg.classList.remove('hidden');
        } else {
            if (incomeCanvas) incomeCanvas.classList.remove('hidden');
            if (noIncomeMsg) noIncomeMsg.classList.add('hidden');

            if (incomeChartObj) {
                incomeChartObj.data.labels = Object.keys(incomeBreakdown);
                incomeChartObj.data.datasets[0].data = Object.values(incomeBreakdown);
                incomeChartObj.update();
            }
        }
    }

    // Inisialisasi Chart.js
    document.addEventListener('DOMContentLoaded', function() {
        const initialIncome = {{ $totalIncome }};
        const initialExpense = {{ $totalExpense }};
        const hasData = (initialIncome > 0 || initialExpense > 0);

        const ratioCtx = document.getElementById('ratioChart');
        const expCtx = document.getElementById('expenseCategoryChart');
        const incCtx = document.getElementById('incomeCategoryChart');

        if (hasData) {
            // 1. Rasio Chart
            if (ratioCtx) {
                ratioChartObj = new Chart(ratioCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pemasukan', 'Pengeluaran'],
                        datasets: [{
                            data: [initialIncome, initialExpense],
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.45)', // emerald
                                'rgba(244, 63, 94, 0.45)'    // rose
                            ],
                            borderColor: [
                                'rgba(16, 185, 129, 0.95)',
                                'rgba(244, 63, 94, 0.95)'
                            ],
                            borderWidth: 1.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: '#94a3b8', font: { size: 11 } }
                            }
                        }
                    }
                });
            }

            // 2. Expense Category Chart
            const expBreakdown = @json($expenseChartData);
            if (expCtx && Object.keys(expBreakdown).length > 0) {
                expenseChartObj = new Chart(expCtx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(expBreakdown),
                        datasets: [{
                            data: Object.values(expBreakdown),
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.45)', // Blue
                                'rgba(16, 185, 129, 0.45)', // Emerald
                                'rgba(245, 158, 11, 0.45)', // Amber
                                'rgba(139, 92, 246, 0.45)', // Purple
                                'rgba(244, 63, 94, 0.45)',  // Rose
                                'rgba(20, 184, 166, 0.45)'  // Teal
                            ],
                            borderColor: [
                                'rgba(59, 130, 246, 0.95)',
                                'rgba(16, 185, 129, 0.95)',
                                'rgba(245, 158, 11, 0.95)',
                                'rgba(139, 92, 246, 0.95)',
                                'rgba(244, 63, 94, 0.95)',
                                'rgba(20, 184, 166, 0.95)'
                            ],
                            borderWidth: 1.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: '#94a3b8', font: { size: 10 } }
                            }
                        }
                    }
                });
            }

            // 3. Income Category Chart
            const incBreakdown = @json($incomeChartData);
            if (incCtx && Object.keys(incBreakdown).length > 0) {
                incomeChartObj = new Chart(incCtx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(incBreakdown),
                        datasets: [{
                            data: Object.values(incBreakdown),
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.45)', // Emerald
                                'rgba(6, 182, 212, 0.45)',  // Cyan
                                'rgba(245, 158, 11, 0.45)', // Amber
                                'rgba(99, 102, 241, 0.45)', // Indigo
                                'rgba(100, 116, 139, 0.45)' // Slate
                            ],
                            borderColor: [
                                'rgba(16, 185, 129, 0.95)',
                                'rgba(6, 182, 212, 0.95)',
                                'rgba(245, 158, 11, 0.95)',
                                'rgba(99, 102, 241, 0.95)',
                                'rgba(100, 116, 139, 0.95)'
                            ],
                            borderWidth: 1.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: '#94a3b8', font: { size: 10 } }
                            }
                        }
                    }
                });
            }
        }

        // Terapkan filter awal jika dipanggil
        applyFilters();
    });
</script>
@endsection
