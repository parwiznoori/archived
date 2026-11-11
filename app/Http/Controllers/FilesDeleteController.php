<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class FilesDeleteController extends Controller
{
    public function deleteFiles($attachmentID)
    {
        $attachment = Attachment::where('attachable_type', (new Announcement())->getMorphClass())
            ->findOrFail($attachmentID);
        $filename = basename(str_replace('\\', '/', $attachment->file));

        if ($filename !== '' && !in_array($filename, ['.', '..'], true)) {
            $path = 'attachments/' . $filename;
            abort_if(Storage::exists($path) && !Storage::delete($path), 500);
        }

        $attachment->delete();

        return redirect()->back();
    }
}
