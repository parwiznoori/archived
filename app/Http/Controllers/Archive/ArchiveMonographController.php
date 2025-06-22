<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archivedata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArchiveMonographController extends Controller
{
    public function show($id)
    {
        $archivedata = Archivedata::findOrFail($id);
        return view('archivedata.archive_monograph', compact('archivedata'));
    }

    public function insert(Request $request, $id)
{
    $request->validate([
        'monograph_date' => 'nullable|string',
        'monograph_number' => 'nullable|string',
        'monograph_title' => 'nullable|string',
        'monograph_doc_date' => 'nullable|string',
        'monograph_doc_number' => 'nullable|string',
        'monograph_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
    ]);

    try {
        DB::transaction(function () use ($request, $id) {
            $archivedata = Archivedata::findOrFail($id);

            $updateData = [
                'monograph_date' => $request->monograph_date,
                'monograph_number' => $request->monograph_number,
                'monograph_title' => $request->monograph_title,
                'monograph_doc_date' => $request->monograph_doc_date,
                'monograph_doc_number' => $request->monograph_doc_number,
                'monograph_desc' => $request->monograph_desc,
            ];

            if ($request->hasFile('monograph_img')) {
                // Delete old image if exists
                if ($archivedata->monograph_img) {
                    $oldImagePath = public_path($archivedata->monograph_img);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Create directory if doesn't exist
                $uploadPath = public_path('uploads/monograph_img');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Process new image
                $image = $request->file('monograph_img');
                $imageName = time().'_'.Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$image->getClientOriginalExtension();
                
                // Move image to public directory
                $image->move($uploadPath, $imageName);
                
                // Save relative path to database
                $updateData['monograph_img'] = '/uploads/monograph_img/'.$imageName;
            }

            $archivedata->update($updateData);
        });

        return back()->with('success', 'معلومات موفقانه اپدیت شد');
    } catch (\Exception $e) {
        return back()->with('error', 'خطا در ذخیره اطلاعات: '.$e->getMessage());
    }
}
}