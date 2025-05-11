<?php

namespace App\Http\Controllers\Archive;

use App\DataTables\ArchiveDataTable;
use App\Http\Controllers\Controller;
use App\Models\Archivedata;
use App\Models\ArchiveDocType;
use DB;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;


class ArchiveDocTypeController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware('permission:view-archive', ['only' => ['index', 'show']]);
//        $this->middleware('permission:create-archive', ['only' => ['create', 'store']]);
//        $this->middleware('permission:edit-archive', ['only' => ['edit', 'update', 'updateStatus', 'update_groups']]);
//        $this->middleware('permission:delete-archive', ['only' => ['destroy']]);
//
//    }





    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'archiveDataId' => 'required',
            'doc_type' => 'required',
            // Uncomment the lines below if you want to validate doc_number, doc_description, and doc_file
            // 'doc_number' => 'required',
            // 'doc_description' => 'required',
            // 'doc_file' => 'required|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:1000000',
        ]);

        // Define the primary upload directory
        $primaryDirectory = 'archivedocfiles/';
        $primaryFullPath = public_path($primaryDirectory);

        // Create the directory if it doesn't exist
        if (!file_exists($primaryFullPath)) {
            mkdir($primaryFullPath, 0777, true);
        }

        // Initialize fileName as null, in case no file is uploaded
        $fileName = null;

        // Get the uploaded file (if it exists)
        $file = $request->file('doc_file');

        // Check if the file is uploaded
        if ($file) {
            // Get the original file name
            $originalFileName = $file->getClientOriginalName();
            $fileName = $originalFileName;  // Assign the original file name

            // Define the destination path
            $destinationPath = $primaryFullPath . '/' . $fileName;

            // Check if the file already exists and rename it if necessary
            if (file_exists($destinationPath)) {
                $fileExtension = $file->getClientOriginalExtension();
                $baseName = pathinfo($originalFileName, PATHINFO_FILENAME);
                $fileName = $baseName . '_' . time() . '.' . $fileExtension;
                $destinationPath = $primaryFullPath . '/' . $fileName;
            }

            // Move the file to the desired location
            $file->move($primaryFullPath, $fileName);
        }

        // Use a transaction to handle the archive creation
        $archivedoc = \DB::transaction(function () use ($request, $primaryDirectory, $fileName) {
            return ArchiveDocType::create([
                'archivedata_id' => $request->archiveDataId,
                'doc_type' => $request->doc_type,
                'doc_number' => $request->doc_number,
                'doc_description' => $request->doc_description,
                'doc_file' => $fileName ? $primaryDirectory . $fileName : null, // Store the relative file path in the database (null if no file uploaded)
            ]);
        });

        return redirect()->back()->with('success', 'موفقانه معلومات اسناد درچ گردید');
    }




    public function show($archivedata)
    {
        $archiveId = $archivedata;

        // Retrieve the archivedata name and the associated ArchiveDocType records
        $archivedata = ArchiveData::find($archivedata); // Assuming ArchiveData is the model for archivedata
        if (!$archivedata) {
            return redirect()->back()->with('error', 'Archivedata not found.');
        }

        $archivedataName = $archivedata->name; // Assuming 'name' is the field for archivedata name
        $archiveDocType = ArchiveDocType::where('archivedata_id',$archiveId)->get();

        // Pass variables to the view
        return View('archivedocuments.create', compact('archiveDocType', 'archiveId', 'archivedata', 'archivedataName'));
    }







    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Find the document type by ID
        $docType = ArchiveDocType::findOrFail($id);

        // Return the edit view with the document type data
        return view('archivedocuments.edit', compact('docType'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        // Validate the input
        $request->validate([
            'doc_type' => 'required',
            'doc_number' => 'required',
            'doc_description' => 'required',
            'doc_file' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:1000000', // Make doc_file optional
        ]);

        // Find the existing document type by ID
        $archivedoc = ArchiveDocType::findOrFail($id);

        // Define the primary upload directory
        $primaryDirectory = 'archivedocfiles/';
        $primaryFullPath = public_path($primaryDirectory);

        // Create the directory if it doesn't exist
        if (!file_exists($primaryFullPath)) {
            mkdir($primaryFullPath, 0777, true);
        }

        // Check if a new file has been uploaded
        if ($request->hasFile('doc_file')) {
            $file = $request->file('doc_file');
            $originalFileName = $file->getClientOriginalName();
            $fileName = $originalFileName;
            $destinationPath = $primaryFullPath . '/' . $fileName;

            // Check if the file already exists and rename the new file
            if (file_exists($destinationPath)) {
                $fileExtension = $file->getClientOriginalExtension();
                $baseName = pathinfo($originalFileName, PATHINFO_FILENAME);
                $fileName = $baseName . '_' . time() . '.' . $fileExtension;
                $destinationPath = $primaryFullPath . '/' . $fileName;
            }

            // Move the file to the public directory
            $file->move($primaryFullPath, $fileName);

            // Update the document type with the new file path
            $archivedoc->doc_file = $primaryDirectory . $fileName;
        }

        // Update the other fields
        $archivedoc->doc_type = $request->doc_type;
        $archivedoc->doc_number = $request->doc_number;
        $archivedoc->doc_description = $request->doc_description;

        // Save the changes
        $archivedoc->save();

        return redirect()->route('archive_doc_type3', ['id' => $archivedoc->id])->with('success', 'موفقانه اسناد اپدیت گردید');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */




    public function destroy($id)
    {

        // Find the document type by ID
        $archivedoc = ArchiveDocType::findOrFail($id);

        // Delete the associated file if it exists
        if (file_exists(public_path($archivedoc->doc_file))) {
            unlink(public_path($archivedoc->doc_file)); // Remove the file from the server
        }

        // Delete the record from the database
        $archivedoc->delete();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'موفقانه اسناد حذف گردید');
    }


}

