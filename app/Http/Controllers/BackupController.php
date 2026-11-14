<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $backups = collect(Storage::disk('local')->files($this->directory()))
            ->map(fn (string $path) => basename($path))
            ->filter(fn (string $file) => preg_match('/^[A-Za-z0-9_.-]+\.zip$/D', $file))
            ->sortDesc();

        return view('backup.index', compact('backups'));
    }

    public function store()
    {
        $status = Artisan::call('backup:run', ['--disable-notifications' => true]);
        return back()->with('message', $status === 0 ? 'نسخهٔ پشتیبان ایجاد شد.' : 'ساخت نسخهٔ پشتیبان انجام نشد.');
    }

    public function download(string $file)
    {
        return Storage::disk('local')->download($this->backupPath($file), $file, [
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function destroy(string $file)
    {
        Storage::disk('local')->delete($this->backupPath($file));
        return back()->with('message', 'نسخهٔ پشتیبان حذف شد.');
    }

    private function backupPath(string $file): string
    {
        abort_unless(preg_match('/^[A-Za-z0-9_.-]+\.zip$/D', $file), 404);
        $path = $this->directory() . '/' . $file;
        abort_unless(Storage::disk('local')->exists($path), 404);
        return $path;
    }

    private function directory(): string
    {
        return config('backup.backup.name', config('app.name'));
    }
}
