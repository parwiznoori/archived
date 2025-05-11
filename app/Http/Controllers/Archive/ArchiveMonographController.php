<?php

namespace App\Http\Controllers\Archive;

use App\DataTables\ArchiveDataTable;
use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Archivedata;
use App\Models\ArchiveDocType;
use App\Models\ArchiveFormPrint;
use App\Models\ArchiveFormTemplate;
use App\Models\Archiveimage;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\SemesterType;
use App\Models\University;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;




class ArchiveMonographController extends Controller
{

    public function show($id)
    {
        $archivedata = Archivedata::find($id);

        return view('archivedata.archive_monograph', compact('archivedata'));
    }




    public function insert(Request $request, $id)
    {
        $request->validate([
            'monograph_date' => 'required|string',
            'monograph_number' => 'required|string',
            'monograph_title' => 'required|string',
            'monograph_doc_date' => 'required|string',
            'monograph_doc_number' => 'required|string',
        ]);

        \DB::transaction(function () use ($request, $id) {
            $archivedata = Archivedata::findOrFail($id);

            $archivedata->update([
                'monograph_date' => $request->monograph_date,
                'monograph_number' => $request->monograph_number,
                'monograph_title' => $request->monograph_title,
                'monograph_doc_date' => $request->monograph_doc_date,
                'monograph_doc_number' => $request->monograph_doc_number,
            ]);
        });


        return back()->with('success', 'معلومات موفقانه اپدیت شد');


    }

}


