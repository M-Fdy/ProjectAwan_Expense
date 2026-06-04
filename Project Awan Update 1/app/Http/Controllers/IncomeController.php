<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    /**
     * Simpan data pemasukan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'date' => ['required', 'date'],
        ]);

        Auth::user()->incomes()->create([
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return redirect()->route('home')->with('success', 'Pemasukan berhasil ditambahkan!');
    }

    /**
     * Update data pemasukan.
     */
    public function update(Request $request, Income $income)
    {
        // Pastikan income milik user yang terautentikasi
        if ($income->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'date' => ['required', 'date'],
        ]);

        $income->update([
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return redirect()->route('home')->with('success', 'Pemasukan berhasil diperbarui!');
    }

    /**
     * Hapus data pemasukan.
     */
    public function destroy(Income $income)
    {
        // Pastikan income milik user yang terautentikasi
        if ($income->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $income->delete();

        return redirect()->route('home')->with('success', 'Pemasukan berhasil dihapus!');
    }
}
