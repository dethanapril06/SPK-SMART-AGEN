<?php

namespace App\Http\Controllers\CalonAgen;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasis = Notifikasi::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        // Tandai semua sebagai sudah dibaca saat buka halaman
        Notifikasi::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('calon-agen.notifikasi.index', compact('notifikasis'));
    }

    public function read(Notifikasi $notifikasi)
    {
        // Pastikan notifikasi milik user yang login
        abort_if($notifikasi->user_id !== Auth::id(), 403);

        $notifikasi->update(['is_read' => true]);

        return redirect()->route('calon-agen.notifikasi.index');
    }
}