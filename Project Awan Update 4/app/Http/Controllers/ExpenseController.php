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

    /**
     * Ekspor seluruh data transaksi pemasukan dan pengeluaran asli ke file Excel.
     */
    public function exportExcel()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Tarik data asli dari database
        $expenses = $user->expenses()->with('category')->get();
        $incomes = $user->incomes()->with('category')->get();
        
        // Tandai tipe transaksi
        $expenses->each(fn($item) => $item->type = 'Pengeluaran');
        $incomes->each(fn($item) => $item->type = 'Pemasukan');
        
        // Gabungkan dan urutkan transaksi berdasarkan tanggal terbaru
        $transactions = $expenses->concat($incomes)->sortByDesc(fn($item) => $item->date . ' ' . $item->created_at);
        
        $filename = "laporan_keuangan_" . date('Ymd_His') . ".xls";
        
        $headers = [
            "Content-Type"        => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $callback = function() use($transactions) {
            $file = fopen('php://output', 'w');
            
            // Output template HTML/Excel dengan meta xml agar Excel memuat gridline & formatting angka
            $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!--[if gte mso 9]>
<xml>
<x:ExcelWorkbook>
<x:ExcelWorksheets>
<x:ExcelWorksheet>
<x:Name>Laporan Keuangan</x:Name>
<x:WorksheetOptions>
<x:DisplayGridlines/>
</x:WorksheetOptions>
</x:ExcelWorksheet>
</x:ExcelWorksheets>
</x:ExcelWorkbook>
</xml>
<![endif]-->
<style>
    th {
        background-color: #4f46e5;
        color: #ffffff;
        font-weight: bold;
        border: 1px solid #d1d5db;
        padding: 8px;
        font-family: sans-serif;
    }
    td {
        border: 1px solid #e5e7eb;
        padding: 8px;
        font-family: sans-serif;
    }
    .number {
        mso-number-format: "\#\,\#\#0";
        text-align: right;
    }
    .text-center {
        text-align: center;
    }
    .pemasukan {
        color: #10b981;
        font-weight: bold;
    }
    .pengeluaran {
        color: #f43f5e;
        font-weight: bold;
    }
</style>
</head>
<body>
<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Tipe</th>
            <th>Kategori</th>
            <th>Deskripsi</th>
            <th>Nominal (Rp)</th>
        </tr>
    </thead>
    <tbody>';
            
            foreach ($transactions as $transaction) {
                $typeClass = $transaction->type === 'Pemasukan' ? 'pemasukan' : 'pengeluaran';
                $dateFormatted = \Carbon\Carbon::parse($transaction->date)->format('d/m/Y');
                
                $html .= '
        <tr>
            <td class="text-center">' . htmlspecialchars($dateFormatted) . '</td>
            <td class="' . $typeClass . '">' . htmlspecialchars($transaction->type) . '</td>
            <td>' . htmlspecialchars($transaction->category->name) . '</td>
            <td>' . htmlspecialchars($transaction->description ?? '-') . '</td>
            <td class="number">' . (int)$transaction->amount . '</td>
        </tr>';
            }
            
            $html .= '
    </tbody>
</table>
</body>
</html>';
            
            fwrite($file, $html);
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
