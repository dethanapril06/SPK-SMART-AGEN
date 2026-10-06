<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DraftUploadService
{
    /**
     * Simpan file-file yang diunggah ke storage sementara/draft
     * dan catat informasinya di session.
     */
    public function captureUploads(Request $request, array $keys, string $sessionPrefix = 'draft_uploads'): void
    {
        $sessionId = session()->getId();

        foreach ($keys as $key) {
            if ($request->hasFile($key) && $request->file($key)->isValid()) {
                $file = $request->file($key);
                $ext = $file->getClientOriginalExtension();
                $originalName = $file->getClientOriginalName();
                $filename = Str::random(30) . '.' . $ext;
                $path = $file->storeAs("drafts/{$sessionId}/{$key}", $filename, 'public');

                session()->put("{$sessionPrefix}.{$key}", [
                    'path' => $path,
                    'name' => $originalName,
                    'size' => $file->getSize(),
                ]);
            }
        }
    }

    /**
     * Cek apakah ada draft file di session.
     */
    public function hasDraft(string $key, string $sessionPrefix = 'draft_uploads'): bool
    {
        $draft = session()->get("{$sessionPrefix}.{$key}");
        return !empty($draft) && !empty($draft['path']) && Storage::disk('public')->exists($draft['path']);
    }

    /**
     * Dapatkan info draft file.
     */
    public function getDraft(string $key, string $sessionPrefix = 'draft_uploads'): ?array
    {
        if ($this->hasDraft($key, $sessionPrefix)) {
            return session()->get("{$sessionPrefix}.{$key}");
        }
        return null;
    }

    /**
     * Pindahkan file dari draft atau dari request ke lokasi permanen.
     */
    public function storePermanen(Request $request, string $key, string $targetDir, string $sessionPrefix = 'draft_uploads'): ?string
    {
        // 1. Jika ada file baru di-upload di request saat ini
        if ($request->hasFile($key) && $request->file($key)->isValid()) {
            return $request->file($key)->store($targetDir, 'public');
        }

        // 2. Jika ada draft di session
        if ($this->hasDraft($key, $sessionPrefix)) {
            $draft = session()->get("{$sessionPrefix}.{$key}");
            $draftPath = $draft['path'];
            $filename = basename($draftPath);
            $newPath = "{$targetDir}/{$filename}";

            // Pindahkan dari drafts/ ke targetDir
            Storage::disk('public')->move($draftPath, $newPath);

            // Bersihkan session untuk key ini
            session()->forget("{$sessionPrefix}.{$key}");

            return $newPath;
        }

        return null;
    }

    /**
     * Hapus semua draft file di session.
     */
    public function clearDrafts(string $sessionPrefix = 'draft_uploads'): void
    {
        $sessionId = session()->getId();
        Storage::disk('public')->deleteDirectory("drafts/{$sessionId}");
        session()->forget($sessionPrefix);
    }
}
