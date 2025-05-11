<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Support\Str;
use Lang;

class SystemDownloadController extends Controller
{
    //
    public function download($file , array $headers = array())
    {
        $attachment = Attachment::find($file);
        $filename = $attachment->file;

        if($filename) {

            $filename = Str::ascii(basename($filename));
            $downloadFIleName = $attachment->extension;
            $path = storage_path('app').'/attachments/'. $filename;
            if(file_exists($path))
            {
                return response()->download($path, $downloadFIleName , $headers);
            }
            else{
                $message= \Lang::get('error.file_not_exist');
                $message='فایل مورد نظر موجود نیست';
                abort(404,$message);           
            }
            
            


        }
        return "فایل وجود ندارد";
    }
}
