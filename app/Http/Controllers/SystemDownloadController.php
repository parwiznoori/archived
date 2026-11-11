<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class SystemDownloadController extends Controller
{
    public function download($file)
    {
        $attachment = Attachment::where('attachable_type', (new Announcement())->getMorphClass())
            ->findOrFail($file);
        $filename = basename(str_replace('\\', '/', $attachment->file));
        abort_if($filename === '' || in_array($filename, ['.', '..'], true), 404);

        $path = 'attachments/' . $filename;
        abort_unless(Storage::exists($path), 404);
        $downloadName = basename(str_replace('\\', '/', $attachment->extension));

        return Storage::download($path, $downloadName ?: $filename, ['X-Content-Type-Options' => 'nosniff']);
    }
}
