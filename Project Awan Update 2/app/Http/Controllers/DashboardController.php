<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dasbor keuangan pribadi.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil data transaksi
        $expenses = $user->expenses()->with('category')->get();
        $incomes = $user->incomes()->with('category')->get();
        
        // Hitung total
        $totalExpense = $expenses->sum('amount');
        $totalIncome = $incomes->sum('amount');
        $balance = $totalIncome - $totalExpense;
        
        // Label tipe transaksi
        $expenses->each(function ($item) {
            $item->type = 'expense';
        });
        $incomes->each(function ($item) {
            $item->type = 'income';
        });
        
        // Gabungkan dan urutkan transaksi berdasarkan tanggal terbaru
        $transactions = $expenses->concat($incomes)->sortByDesc(function ($item) {
            return $item->date . ' ' . $item->created_at;
        })->values();
        
        // Ambil kategori untuk dropdown form
        $expenseCategories = Category::where('type', 'expense')->get();
        $incomeCategories = Category::where('type', 'income')->get();
        
        // Breakdown data untuk diagram Chart.js
        $expenseChartData = $expenses->groupBy('category.name')->map(function ($group) {
            return $group->sum('amount');
        });
        
        $incomeChartData = $incomes->groupBy('category.name')->map(function ($group) {
            return $group->sum('amount');
        });
        
        $serverName = gethostname();
        
        return view('home', compact(
            'transactions',
            'totalExpense',
            'totalIncome',
            'balance',
            'expenseCategories',
            'incomeCategories',
            'expenseChartData',
            'incomeChartData',
            'serverName'
        ));
    }
}
