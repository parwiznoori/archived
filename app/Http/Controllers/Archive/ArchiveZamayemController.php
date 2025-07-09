<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archivedata;
use App\Models\Zamayem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArchiveZamayemController extends Controller
{
    public function show($id)
    {
        $archive = Archivedata::findOrFail($id);
        $zamayems = Zamayem::where('archivedata_id', $id)->get();
        
        return view('archivedata.archive_zamayem', [
            'archive' => $archive,
            'zamayems' => $zamayems,
            'id' => $id
        ]);
    }

    public function insert(Request $request, $id)
{
    $request->validate([
        'title' => 'nullable|string|max:255',
        'zamayem_img.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
    ]);

    if ($request->hasFile('zamayem_img')) {
        // Create directory if it doesn't exist
        $uploadPath = public_path('uploads/zamayem_images');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        foreach ($request->file('zamayem_img') as $image) {
            $filename = Str::random(20) . '.' . $image->getClientOriginalExtension();
            
            // Move image to public directory
            $image->move($uploadPath, $filename);
            
            // Save relative path in database
            Zamayem::create([
                'archivedata_id' => $id,
                'title' => $request->title,
                'zamayem_img' => 'uploads/zamayem_images/' . $filename,
            ]);
        }
    }

    return redirect()->route('archive_zamayem', $id)
        ->with('success', 'عکس موفقانه اپلود گردید.');
}

 public function destroy($id)
{
    $zamayem = Zamayem::findOrFail($id);
    $archiveId = $zamayem->archivedata_id;
    
    // Delete the image file from public directory
    if ($zamayem->zamayem_img && file_exists(public_path($zamayem->zamayem_img))) {
        unlink(public_path($zamayem->zamayem_img));
    }
    
    $zamayem->delete();
    
    return redirect()->route('archive_zamayem', $archiveId)
        ->with('success', 'عکس موفقانه حذف شد');
}
}