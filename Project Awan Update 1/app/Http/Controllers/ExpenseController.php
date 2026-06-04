<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Tampilkan halaman utama (Home) dengan daftar pengeluaran, total, dan kategori.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil data pengeluaran milik user saat ini
        $expenses = $user->expenses()
            ->with('category')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Hitung total pengeluaran
        $totalExpense = $expenses->sum('amount');
        
        // Ambil semua kategori untuk form dropdown
        $categories = Category::all();
        
        // Identifikasi nama server / hostname untuk visual bukti Load Balancer
        $serverName = gethostname();
        
        return view('home', compact('expenses', 'totalExpense', 'categories', 'serverName'));
    }

    /**
     * Simpan data pengeluaran baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'date' => ['required', 'date'],
        ]);

        Auth::user()->expenses()->create([
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return redirect()->route('home')->with('success', 'Pengeluaran berhasil ditambahkan!');
    }

    /**
     * Update data pengeluaran.
     */
    public function update(Request $request, Expense $expense)
    {
        // Pastikan expense milik user yang terautentikasi
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'date' => ['required', 'date'],
        ]);

        $expense->update([
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return redirect()->route('home')->with('success', 'Pengeluaran berhasil diperbarui!');
    }

    /**
     * Hapus data pengeluaran.
     */
    public function destroy(Expense $expense)
    {
        // Pastikan expense milik user yang terautentikasi
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $expense->delete();

        return redirect()->route('home')->with('success', 'Pengeluaran berhasil dihapus!');
    }
}
