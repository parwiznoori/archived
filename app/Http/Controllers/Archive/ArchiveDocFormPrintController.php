<?php

namespace App\Http\Controllers\Archive;

use App\DataTables\ArchiveDataTable;
use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Archivedata;
use App\Models\ArchiveDocType;
use App\Models\ArchiveFormPrint;
use App\Models\ArchiveFormTemplate;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\University;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;




class ArchiveDocFormPrintController extends Controller
{

    public function show($archivedataid)
    {
        // Fetch the Archivedata record
        $archivedataid = Archivedata::find($archivedataid);

        if (!$archivedataid) {
            return redirect()->back()->with('error', 'Archivedata not found');
        }
        $archiveFormPrint = ArchiveFormPrint::all();
        $archiveFormTypes = ArchiveFormTemplate::where('status', true)->get();

        // Add the form_name to each ArchiveFormPrint record
        foreach ($archiveFormPrint as $print) {
            // Get the form_name from the related ArchiveFormTemplate
            $formTemplate = ArchiveFormTemplate::find($print->archive_form_temp_id);
            $print->form_name = $formTemplate ? $formTemplate->form_name : 'No Form Name';
        }
        $universities = University::where('id', $archivedataid->university_id)->first();
        $faculties = Faculty::where('id', $archivedataid->faculty_id)->first();
        $departments = Department::where('id', $archivedataid->department_id)->first();

        $archivedataid->university_id=$universities->name;
        $archivedataid->faculty_id=$faculties->name;
        $archivedataid->department_id=$departments->name;

        // Return the view with the data
        return view('archiveformprint.create', [
            'title' => trans('general.archive_doc_form_manage'),
            'description' => trans('general.archive_doc_form'),
            'archivedataid' => $archivedataid,
            'archiveFormPrint' => $archiveFormPrint,
            'archiveFormTypes' => $archiveFormTypes,
            'universities' => $universities,
            'faculties' => $faculties,
            'departments' => $departments,
        ]);
    }






    public function store(Request $request, $archivedataid)
    {
        // Validate the input
        $validatedData = $request->validate([
            'archive_form_temp_id' => 'required|exists:archive_form_templates,id',  // Ensure it's in the templates table
            'text' => 'required',
        ]);

        // Fetch the Archivedata record
        $archivedataid = Archivedata::find($archivedataid);

        if (!$archivedataid) {
            return redirect()->back()->with('error', 'Archivedata not found');
        }

        // Create a new ArchiveFormPrint record with validated data
        ArchiveFormPrint::create([
            'archive_form_temp_id' => $validatedData['archive_form_temp_id'],  // Use the validated field
            'content' => $validatedData['text'],  // Use validated text
            'archivedata_id' => $archivedataid->id,
        ]);

        // Fetch all ArchiveFormTemplates
        $archiveFormTypes = ArchiveFormTemplate::all();
        $archiveFormPrint = ArchiveFormPrint::all();


        return back();
        // Return success message with data
//        return view('archiveformprint.create', [
//            'success' => __('معلومات درج سیستم گردید'),  // Success message
//            'archiveFormTypes' => $archiveFormTypes,
//            'archiveFormPrint' => $archiveFormPrint,
//            'archivedataid' => $archivedataid,
//        ]);
    }

public  function printform($archivedatid){



    $record = ArchiveFormPrint::findOrFail($archivedatid);
    $content = ArchiveFormPrint::findOrFail($archivedatid)->content;
//    $id = ArchiveFormPrint::findOrFail($archivedatid);
    // Load the view and generate a PDF in A4 portrait format
    $pdf = PDF::loadView('archiveformprint.archivedoc_all', compact('content','archivedatid'), [], [
        'mode'                   => 'utf-8',
        'format'          => 'A4-P', // Portrait orientation
        'default_font'    => 'times-new-roman', // Set the default font
        'custom_font_dir' => base_path('resources/fonts/'), // Custom font directory
        'custom_font_data' => [
            'times-new-roman' => [ // Define custom font
                'R'  => 'times.ttf', // Regular font file
                'B'  => 'timesbd.ttf',    // Bold font file (optional)
                'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
                'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
                'unAGlyphs' => true
            ],
        ],
    ]);

    // Stream the PDF to the browser with a dynamic filename
    return $pdf->stream('ArchivedataForm-' . $archivedatid . '.pdf');
}


    public function destroy($id)
    {
        // Find the ArchiveFormPrint record
        $archiveFormPrint = ArchiveFormPrint::find($id);

        // Check if the record exists
        if (!$archiveFormPrint) {
            return redirect()->back()->with('error', 'Record not found.');
        }

        // Delete the record
        $archiveFormPrint->delete();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Record deleted successfully.');
    }



}


