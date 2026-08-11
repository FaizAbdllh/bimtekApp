<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogSistem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogSistemController extends Controller
{
    /**
     * Display a listing of system logs.
     */
    public function index(Request $request): View
    {
        $query = LogSistem::with('user')->orderBy('created_at', 'desc');

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('created_at', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('created_at', '<=', $request->sampai_tanggal);
        }

        // Search by pesan
        if ($request->filled('search')) {
            $query->where('pesan', 'like', '%'.$request->search.'%');
        }

        $logs = $query->paginate(25)->withQueryString();

        // Get users for filter dropdown
        $users = \App\Models\User::orderBy('name')->get();

        // Get statistics with single query
        $statistics = LogSistem::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN level = 'info' THEN 1 ELSE 0 END) as info,
            SUM(CASE WHEN level = 'warning' THEN 1 ELSE 0 END) as warning,
            SUM(CASE WHEN level = 'error' THEN 1 ELSE 0 END) as error,
            SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today
        ")->first();

        return view('admin.log-sistem.index', compact('logs', 'users', 'statistics'));
    }

    /**
     * Display the specified log.
     */
    public function show(LogSistem $logSistem): View
    {
        $logSistem->load('user');

        return view('admin.log-sistem.show', compact('logSistem'));
    }

    /**
     * Remove the specified log from storage.
     */
    public function destroy(LogSistem $logSistem)
    {
        $logSistem->delete();

        return redirect()->route('admin.log-sistem.index')
            ->with('success', 'Log berhasil dihapus.');
    }

    /**
     * Clear all logs (with confirmation).
     */
    public function clearAll(Request $request)
    {
        $request->validate([
            'confirm' => 'required|in:HAPUS SEMUA LOG',
        ], [
            'confirm.required' => 'Silakan ketik konfirmasi.',
            'confirm.in' => 'Ketik "HAPUS SEMUA LOG" dengan benar untuk mengkonfirmasi.',
        ]);

        $count = LogSistem::count();
        LogSistem::truncate();

        // Log action itself
        LogSistem::info("Admin IT menghapus semua log sistem ({$count} records)");

        return redirect()->route('admin.log-sistem.index')
            ->with('success', "Semua log sistem ({$count} records) berhasil dihapus.");
    }

    /**
     * Export logs to CSV.
     */
    public function export(Request $request)
    {
        $query = LogSistem::with('user')->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('created_at', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('created_at', '<=', $request->sampai_tanggal);
        }
        if ($request->filled('search')) {
            $query->where('pesan', 'like', '%'.$request->search.'%');
        }

        $logs = $query->get();

        $filename = 'Log_Sistem_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($file, ['No', 'Waktu', 'Level', 'User', 'Pesan']);

            // Data
            $no = 1;
            foreach ($logs as $log) {
                fputcsv($file, [
                    $no++,
                    $log->created_at->format('Y-m-d H:i:s'),
                    strtoupper($log->level),
                    $log->user ? $log->user->name : 'System',
                    $log->pesan,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
