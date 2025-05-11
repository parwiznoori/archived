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




class ArchiveDocFormController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function create()
    {
        $archiveFormTemplate = ArchiveFormTemplate::all();
        $universities = University::pluck('name', 'id');
        $faculties = Faculty::pluck('name', 'id');
        $departments = Department::pluck('name', 'id');

        return view('archiveform.create', [
            'title' => trans('general.archive_doc_form_manage'),
            'description' => trans('general.archive_doc_form'),
            'universities' => $universities,
            'faculties' => $faculties,
            'departments' => $departments,
            'archiveFormTemplate' => $archiveFormTemplate,
        ]);
    }






    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */



    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'form_name' => 'required|string|max:255',
            'status'    => 'nullable|boolean',
            'variable'  => 'required|array',
            'content'   => 'required|string',
        ]);

        // If 'status' is not checked, set it to 0
        $validatedData['status'] = $request->has('status') ? 1 : 0;

        // Convert the 'variable' array to a comma-separated string
        $validatedData['variable'] = implode(',', $validatedData['variable']);

        // Create a new record in the database
        ArchiveFormTemplate::create($validatedData);



        // Redirect or return a response
        return redirect()->route('archive_doc_form.create')
            ->with('success', trans('.معلومات درج سیستم گردید'));
    }








    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {

        $archiveFormTemplate = ArchiveFormTemplate::find($id);
        return view('archiveform.edit', [
            'title' => trans('general.archive_doc_form_manage'),
            'description' => trans('general.archive_doc_form_edit'),
            'archiveFormTemplate' => $archiveFormTemplate, // Single template, not a collection
        ]);
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
        // Validate the incoming request data
        $validatedData = $request->validate([
            'form_name' => 'required|string|max:255',
            'status'    => 'nullable|boolean',
            'variable'  => 'required|array',
            'content'   => 'required|string',
        ]);

        // If 'status' is not checked, set it to 0
        $validatedData['status'] = $request->has('status') ? 1 : 0;

        // Convert the 'variable' array to a comma-separated string
        $validatedData['variable'] = implode(',', $validatedData['variable']);

        // Retrieve the existing template by ID
        $archiveFormTemplate = ArchiveFormTemplate::find($id);


        // Update the template with the validated data
        $archiveFormTemplate->update($validatedData);

        // Redirect with success message
        return back()->with('success', 'معلومات موفقانه اپدیت شد');

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */




    public function destroy($id)
    {
        // Find the archive by ID
        $archiveFormTemplate = ArchiveFormTemplate::find($id);

        // Check if the archive exists, if not, return an error response
        if (!$archiveFormTemplate) {
            return back()->with('error', 'مورد یافت نشد');
        }

        // Delete the archive
        $archiveFormTemplate->delete();

        // Redirect back with success message
        return back()->with('success', 'مورد موفقانه حذف شد');
    }




}


