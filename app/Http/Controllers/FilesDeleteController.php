<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class FilesDeleteController extends Controller
{
    //
    public function deleteFiles($attachmentID)
    {
            $data = Attachment::find($attachmentID);

            if($data) {
            $attachmentName = $data->file;

            Attachment::where('id',$attachmentID)->delete();

            if($attachmentName)
            {
                $imagexistanse=Storage::exists('\attachments\\' . $attachmentName);

                    if($imagexistanse)
                    {
                          Storage::delete('\attachments\\' . $attachmentName);
                    }
            }
        }
        return redirect()->back();

    }
}
