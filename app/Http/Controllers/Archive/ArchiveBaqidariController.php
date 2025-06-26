<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archivedata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Baqidari;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;



class ArchiveBaqidariController extends Controller
{

    public function show($id)
    {
        $baqidaris = Baqidari::all();
        $archive = Archivedata::findOrFail($id);
        $baqidari = Baqidari::where('archivedata_id', $id)->first();
        
        return view('archivedata.archive_baqidari', [
            'archive' => $archive,
            'data' => $baqidari,
            'id' => $id,
            'baqidaris' => $baqidaris
        ]);
    }

public function insert(Request $request, $id)
{
    // Validate the request data
    $validated = $request->validate([
        'semester' => 'nullable|in:semester1,semester2,semester3,semester4,semester5,semester6,semester7,semester8',
        'subject' => 'nullable|string|max:255',
        'chance_number' => 'nullable|numeric|between:0,99999999.99',
        'zarib_chance' => 'nullable|numeric|between:0,999.99',
        'chance_number2' => 'nullable|numeric|between:0,99999999.99',
        'monoghraph' => 'nullable|numeric|between:0,99999999.99',
        'zarib_credite' => 'nullable|numeric|between:0,999.99',
        'credit' => 'nullable|numeric|between:0,999.99',
        'total_credit' => 'nullable|numeric|between:0,999.99',
        'total_numbers' => 'nullable|numeric|between:0,999.99',
        'semester_percentage' => 'nullable|numeric|between:0,100',
        'total_credit2' => 'nullable|numeric|between:0,999.99',
        'total_numbers2' => 'nullable|numeric|between:0,999.99',
        'semester_percentage2' => 'nullable|numeric|between:0,100',
        'total_credit3' => 'nullable|numeric|between:0,999.99',
        'total_numbers3' => 'nullable|numeric|between:0,999.99',
        'eight_semester_percentage3' => 'nullable|numeric|between:0,100',
        'total_credit4' => 'nullable|numeric|between:0,999.99',
        'total_numbers4' => 'nullable|numeric|between:0,999.99',
        'eight_semester_percentage4' => 'nullable|numeric|between:0,100',
        'baqidari_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
    ]);

    try {
        // Begin transaction
        DB::beginTransaction();

        // Fetch the related Archivedata record
        $archive = Archivedata::findOrFail($id);

        // Handle image upload if provided
        if ($request->hasFile('baqidari_img')) {
            // Delete the old image if it exists
            if ($archive->baqidari_img) {
                $oldImagePath = public_path($archive->baqidari_img);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Ensure the upload directory exists
            $uploadPath = public_path('uploads/baqidari_img');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Save the new image
            $image = $request->file('baqidari_img');
            $imageName = time() . '_' . Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $image->getClientOriginalExtension();
            $image->move($uploadPath, $imageName);

            // Add the relative path to the validated data
            $validated['baqidari_img'] = '/uploads/baqidari_img/' . $imageName;
        }

        // Format percentage fields
        $percentageFields = [
            'semester_percentage',
            'semester_percentage2',
            'eight_semester_percentage3',
            'eight_semester_percentage4',
        ];
        foreach ($percentageFields as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = str_replace('%', '', $validated[$field]);
            }
        }

        // Add the archivedata_id to the validated data
        $validated['archivedata_id'] = $id;

        // Update or create the Baqidari record
        $baqidari = Baqidari::updateOrCreate(
            ['archivedata_id' => $id],
            $validated
        );

        // Commit the transaction
        DB::commit();

        // Determine the success message
        $message = $baqidari->wasRecentlyCreated ? 
            'اطلاعات باقیداری با موفقیت ایجاد شد!' : 
            'اطلاعات باقیداری با موفقیت بروزرسانی شد!';

        // Redirect back with a success message
        return redirect()
            ->route('archive_baqidari', $id)
            ->with('success', $message);

    } catch (\Exception $e) {
        // Rollback the transaction in case of an error
        DB::rollBack();
        Log::error('Error while saving Baqidari: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        
        return back()
            ->withInput()
            ->with('error', 'خطا در ذخیره اطلاعات: ' . $e->getMessage());
    }
}

}


