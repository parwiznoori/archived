<?php

namespace App\Http\Controllers\Archive;

use App\Exports\AllArchiveExports;
use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Archivedata;
use App\Models\Archivedatastatus;
use App\Models\ArchiveDocType;
use App\Models\Archiveqcstatus;
use App\Models\ArchiveYear;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\University;
use DB;
use PDF;
//use Excel;
use Illuminate\Http\Request;
use App\Exports\ReportResultExport;
use Maatwebsite\Excel\Facades\Excel;



class ArchivereportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        // Fetch book names and their counts from the Archive model
        $reportarchive = Archive::select('book_name', \DB::raw('COUNT(*) as count'))
            ->groupBy('book_name')
            ->get();

        // Fetch archive names and their counts from the Archivedata model
        $reportarchivedata = Archivedata::select('name', \DB::raw('COUNT(*) as count'))
            ->groupBy('name')
            ->get();

        $reportarchivedoc = ArchiveDocType::select('archivedata_id')
            ->groupBy('archivedata_id')
            ->get()
            ->count();


        // Fetch counts for specific archive statuses
        $reportarchivestatus = Archive::select('status_id', \DB::raw('COUNT(*) as count'))
            ->whereIn('status_id', [2, 3, 4]) // Ensure to fetch relevant status IDs
            ->groupBy('status_id')
            ->get();

        // Fetch counts for specific archive QC statuses
        $reportarchiveqcstatus = Archive::select('qc_status_id', \DB::raw('COUNT(*) as count'))
            ->whereIn('qc_status_id', [2, 3, 4]) // Ensure to fetch relevant status IDs
            ->groupBy('qc_status_id')
            ->get();

        // Total counts
        $totalArchive = $reportarchive->sum('count'); // Total count of archives
        $totalArchivedata = $reportarchivedata->sum('count'); // Total count of archived data

        // Pass data to the view
        return view('archivereport.archive_report', [
            'reportarchive' => $reportarchive,
            'reportarchivedata' => $reportarchivedata,
            'reportarchivedoc' => $reportarchivedoc,
            'totalArchivestatus' => $reportarchivestatus, // Pass the collection directly
            'totalArchiveqcstatus' => $reportarchiveqcstatus,
            'totalArchive' => $totalArchive,
            'totalArchivedata' => $totalArchivedata,
        ]);
    }


    public function report2(Request $request)
    {
        $university_id = auth()->user()->university_id;

        // Fetch universities based on the authenticated user's university ID
        if ($university_id > 0) {
            $universities = University::where('id', $university_id)->pluck('name', 'id');
        } else {
            $universities = University::pluck('name', 'id');
        }
        $archiveyears = ArchiveYear::pluck('year', 'id');
        $reportarchivedoc = ArchiveDocType::pluck('doc_type', 'id');

        // Return the view with the necessary data
        return view('archivereport.archive_report2', [
            'title' => trans('general.archive'),
            'description' => trans('general.create_archive'),
            'universities' => $universities,
            'faculties' => Faculty::pluck('name', 'id'),
            'faculty' => old('faculty') != '' ? Faculty::where('id', old('faculty'))->pluck('name', 'id') : [],
            'departments' => Department::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
            'grades' => Grade::pluck('name', 'id'),  // Corrected grades line
            'archiveyears' => $archiveyears,
            'reportarchivedoc' => $reportarchivedoc,

        ]);


    }








    
    public function reportresult(Request $request)
    {


        
            if($request->report_type=='2' || $request->reporttype=='3'){
           return $this->reportTypeDetail($request);
        }else{
           return $this->reportType1($request);
        }
    }

    
    public function reportTypeDetail(Request $request)
    {

           // Initialize arrays for where conditions, select fields, and group by fields
           $wheres = [];
           $select = [];
           $headerTable = []; 
   
           // Check for each filter and add corresponding conditions, select, and group by fields using array_push()
   
           // Check for book_year
           if ($request->end_archive_year_id != null && $request->end_archive_year_id != '') {
               array_push($wheres, ['archives.archive_year_id', '<=', $request->end_archive_year_id]);
               array_push($wheres, ['archives.archive_year_id', '>=', $request->start_archive_year_id]);
               array_push($select, 'archives.archive_year_id'); 
               array_push($headerTable, 'سال');
   
           }
   
           // Check for university_id
           if ($request->university_id != null && $request->university_id != '') {
               array_push($wheres, ['archives.university_id', '=', $request->university_id]);
               array_push($select, 'archives.university_id');
               array_push($headerTable, 'پوهنتون');
           }
   
           // Check for faculty_id
           if ($request->faculty_id != null && $request->faculty_id != '') {
               array_push($wheres, ['archive_departments.faculty_id', '=', $request->faculty_id]);
               array_push($select, 'archive_departments.faculty_id');
               array_push($headerTable, 'پوهنحی');
           }
   
           // Check for department_id
           if ($request->department_id != null && $request->department_id != '') {
               array_push($wheres, ['archive_departments.department_id', '=', $request->department_id]);
               array_push($select, 'archive_departments.department_id');
               array_push($headerTable, 'دیپارتمنت');
           }
   
           // Check for status_id
           if ($request->status_id != null && $request->status_id != '') {
               array_push($wheres, ['archives.status_id', '=', $request->status_id]);
               array_push($select, 'archives.status_id');
                array_push($headerTable, 'وضعیت');
           }
   
           // Check for qc_status_id
           if ($request->qc_status_id != null && $request->qc_status_id != '') {
               array_push($wheres, ['archives.qc_status_id', '=', $request->qc_status_id]);
               array_push($select, 'archives.qc_status_id');
               array_push($headerTable, 'موافقه وعدم موافقه');
   
           }
   
           if ($request->grade_id != null && $request->grade_id != '') {
               array_push($wheres, ['archivedatas.grade_id', '=', $request->grade_id]);
               array_push($select, 'archivedatas.grade_id');
               array_push($headerTable, 'درجه');
           }
   
          if ($request->doc_type != null && $request->doc_type != '') {
              array_push($wheres, ['archive_doc_types.doc_type', '=', $request->doc_type]);
              array_push($select, 'archive_doc_types.doc_type');
              array_push($headerTable, 'اسناد');
          }
   
          array_push($headerTable, 'نام');
          array_push($headerTable, 'تخلص');
          array_push($headerTable, 'نام پدر');
          array_push($headerTable, 'نام پدر کلان');
          array_push($headerTable, 'سال شمولیت');
          array_push($headerTable, 'سال فراغت');
          array_push($headerTable, 'تاریخ تولد');
          array_push($headerTable, 'ادی کانکور');
          array_push($headerTable, 'نوع سمستر');
          array_push($select, 'archivedatas.name');
          array_push($select, 'archivedatas.last_name');
          array_push($select, 'archivedatas.father_name');
          array_push($select, 'archivedatas.grandfather_name');
          array_push($select, 'archivedatas.year_of_inclusion');
          array_push($select, 'archivedatas.graduated_year');
          array_push($select, 'archivedatas.birth_date');
          array_push($select, 'archivedatas.kankor_id');
          array_push($select, 'archivedatas.semester_type_id');

   
           // Add a count to the select fields
   //        array_push($select, DB::raw('COUNT(*) as total_count'));
           $reporttype = $request->reporttype;
           
           if ($request->reporttype == '2') {
               // Build the main query using the accumulated conditions
               if ($request->doc_type != null && $request->doc_type != '') {
   //                dd($request);
                   $results = DB::table('archives')
                       ->join('archivedatas', 'archives.id', '=', 'archivedatas.archive_id')
                       ->join('archive_departments', 'archives.id', '=', 'archive_departments.archive_id') // Adjust the foreign key names if necessary
                       ->join('archive_doc_types', 'archivedatas.id', '=', 'archive_doc_types.archivedata_id') // Adjust the foreign key names if necessary
                       ->select(DB::raw('COUNT(DISTINCT archivedata_id) as archivedata_count')) // Count distinct archivedata_id
                       ->where($wheres) // Apply conditions
                       ->get();
   
               } else {
                   $results = DB::table('archives')
                       ->join('archivedatas', 'archives.id', '=', 'archivedatas.archive_id')
                       ->join('archive_departments', 'archives.id', '=', 'archive_departments.archive_id')// Adjust the foreign key names if necessary
                     // Select the fields dynamically as before
                     ->select($select)
                       ->where($wheres) // Apply conditions
                       ->get();
   
   
               }
           } else {
               // Build the main query using the accumulated conditions
               $results = DB::table('archives')
   //                ->join('archive_departments', 'archives.id', '=', 'archive_departments.archive_id')// Adjust the foreign key names if necessary
   
                   ->select($select) // Select the fields dynamically as before
                   ->where($wheres) // Apply conditions
                   ->get();
           }
   
   
           if ($request->start_archive_year_id != null && $request->end_archive_year_id != null) {
               foreach ($results as $res) {
                   $archiveyears = ArchiveYear::where('id', $res->archive_year_id)->first();
                   $res->archive_year_id = $archiveyears->year;
               }
           }
   
   // Get the authenticated user's university_id
           $university_id = auth()->user()->university_id;
   
   // Fetch universities based on the authenticated user's university ID
           $universities = $university_id > 0
               ? University::where('id', $university_id)->pluck('name', 'id')
               : University::pluck('name', 'id');
   
           $grades = Grade::pluck('name', 'id');
           $archiveyears = ArchiveYear::pluck('year', 'id');
           $reportarchivedoc = ArchiveDocType::pluck('doc_type', 'id');
   

           foreach ($results as $res) {
               // Get the university name by using the university_id from each result
               if (isset($res->university_id)) {
                $university = University::find($res->university_id);
                $res->university_id = $university ? $university->name : 'Unknown'; // Fallback to 'Unknown' if not found
            } // Get the university name by using the university_id from each result
           
   
               // Get the department name using the department_id from each result
               if (isset($res->department_id)) {
                   $department = Department::find($res->department_id);
                   $res->department_id = $department ? $department->name : 'Unknown'; // Fallback to 'Unknown' if not found
               }
   
               // Get the faculty name using the faculty_id from each result
               if (isset($res->faculty_id)) {
                   $faculty = Faculty::find($res->faculty_id);
                   $res->faculty_id = $faculty ? $faculty->name : 'Unknown'; // Fallback to 'Unknown' if not found
               }
   
               // Get the grade name using the grade_id from each result
               if (isset($res->grade_id)) {
                   $grade = Grade::find($res->grade_id);
                   $res->grade_id = $grade ? $grade->name : 'Unknown'; // Fallback to 'Unknown' if not found
               }
   
               if (isset($res->status_id)) {
                   $status = Archivedatastatus::find($res->status_id);
                   $res->status_id = $status ? $status->status : 'Unknown'; // Fallback to 'Unknown' if not found
               }
   
               if (isset($res->qc_status_id)) {
                   $qc_status = Archiveqcstatus::find($res->qc_status_id);
                   $res->qc_status_id = $qc_status ? $qc_status->qc_status : 'Unknown'; // Fallback to 'Unknown' if not found
               }
   
               
   
   
           }
   
   
   
   
           return Excel::download(new AllArchiveExports($results,$headerTable), 'all-Archives.xlsx');
   
   
   

    }
    public function reportType1(Request $request)
    {

        // Initialize arrays for where conditions, select fields, and group by fields
        $wheres = [];
        $select = [];
        $headerTable = [];
        $group = [];

        // Check for each filter and add corresponding conditions, select, and group by fields using array_push()

        // Check for book_year
        if ($request->end_archive_year_id != null && $request->end_archive_year_id != '') {
            array_push($wheres, ['archives.archive_year_id', '<=', $request->end_archive_year_id]);
            array_push($wheres, ['archives.archive_year_id', '>=', $request->start_archive_year_id]);
            array_push($select, 'archives.archive_year_id');
            array_push($group, 'archives.archive_year_id');
            array_push($headerTable, 'سال');

        }

        // Check for university_id
        if ($request->university_id != null && $request->university_id != '') {
            array_push($wheres, ['archives.university_id', '=', $request->university_id]);
            array_push($select, 'archives.university_id');
            array_push($group, 'archives.university_id');
            array_push($headerTable, 'پوهنتون');
        }

        // Check for faculty_id
        if ($request->faculty_id != null && $request->faculty_id != '') {
            array_push($wheres, ['archive_departments.faculty_id', '=', $request->faculty_id]);
            array_push($select, 'archive_departments.faculty_id');
            array_push($group, 'archive_departments.faculty_id');
            array_push($headerTable, 'پوهنحی');
        }

        // Check for department_id
        if ($request->department_id != null && $request->department_id != '') {
            array_push($wheres, ['archive_departments.department_id', '=', $request->department_id]);
            array_push($select, 'archive_departments.department_id');
            array_push($group, 'archive_departments.department_id');
            array_push($headerTable, 'دیپارتمنت');
        }

        // Check for status_id
        if ($request->status_id != null && $request->status_id != '') {
            array_push($wheres, ['archives.status_id', '=', $request->status_id]);
            array_push($select, 'archives.status_id');
            array_push($group, 'archives.status_id');
            array_push($headerTable, 'وضعیت');
        }

        // Check for qc_status_id
        if ($request->qc_status_id != null && $request->qc_status_id != '') {
            array_push($wheres, ['archives.qc_status_id', '=', $request->qc_status_id]);
            array_push($select, 'archives.qc_status_id');
            array_push($group, 'archives.qc_status_id');
            array_push($headerTable, 'موافقه وعدم موافقه');

        }

        if ($request->grade_id != null && $request->grade_id != '') {
            array_push($wheres, ['archivedatas.grade_id', '=', $request->grade_id]);
            array_push($select, 'archivedatas.grade_id');
            array_push($group, 'archivedatas.grade_id');
            array_push($headerTable, 'درجه');
        }

       if ($request->doc_type != null && $request->doc_type != '') {
           array_push($wheres, ['archive_doc_types.doc_type', '=', $request->doc_type]);
           array_push($select, 'archive_doc_types.doc_type');
           array_push($group, 'archive_doc_types.doc_type');
           array_push($headerTable, 'اسناد');
       }

       array_push($select, DB::raw('COUNT(1)')); 
       array_push($headerTable, 'تعداد');


        // Add a count to the select fields
//        array_push($select, DB::raw('COUNT(*) as total_count'));
        $reporttype = $request->reporttype;
        if ($request->reporttype == '2') {
            // Build the main query using the accumulated conditions
            if ($request->doc_type != null && $request->doc_type != '') {
//                dd($request);
                $results = DB::table('archives')
                    ->join('archivedatas', 'archives.id', '=', 'archivedatas.archive_id')
                    ->join('archive_departments', 'archives.id', '=', 'archive_departments.archive_id') // Adjust the foreign key names if necessary
                    ->join('archive_doc_types', 'archivedatas.id', '=', 'archive_doc_types.archivedata_id') // Adjust the foreign key names if necessary
                    ->select(DB::raw('COUNT(DISTINCT archivedata_id) as archivedata_count')) // Count distinct archivedata_id
                    ->where($wheres) // Apply conditions
                    ->groupBy($group) // Group by fields as per your filters
                    ->get();

            } else {
                $results = DB::table('archives')
                    ->join('archivedatas', 'archives.id', '=', 'archivedatas.archive_id')
                    ->join('archive_departments', 'archives.id', '=', 'archive_departments.archive_id')// Adjust the foreign key names if necessary
                    ->select($select) // Select the fields dynamically as before
                    ->where($wheres) // Apply conditions
                    ->groupBy($group) // Group by fields as per your filters
                    ->get();


            }
        } else {
            // Build the main query using the accumulated conditions
            $results = DB::table('archives')
//                ->join('archive_departments', 'archives.id', '=', 'archive_departments.archive_id')// Adjust the foreign key names if necessary

                ->select($select) // Select the fields dynamically as before
                ->where($wheres) // Apply conditions
                ->groupBy($group) // Group by fields as per your filters
                ->get();
        }


        if ($request->start_archive_year_id != null && $request->end_archive_year_id != null) {
            foreach ($results as $res) {
                $archiveyears = ArchiveYear::where('id', $res->archive_year_id)->first();
                $res->archive_year_id = $archiveyears->year;
            }
        }

// Get the authenticated user's university_id
        $university_id = auth()->user()->university_id;

// Fetch universities based on the authenticated user's university ID
        $universities = $university_id > 0
            ? University::where('id', $university_id)->pluck('name', 'id')
            : University::pluck('name', 'id');

        $grades = Grade::pluck('name', 'id');
        $archiveyears = ArchiveYear::pluck('year', 'id');
        $reportarchivedoc = ArchiveDocType::pluck('doc_type', 'id');


//
//                return view('archivereport.archive_report2', [
//            'title' => trans('general.archive'),
//            'description' => trans('general.create_archive'),
//            'universities' => $universities,
//            'faculties' => Faculty::pluck('name', 'id'),
//            'faculty' => old('faculty') != '' ? Faculty::where('id', old('faculty'))->pluck('name', 'id') : [],
//            'departments' => Department::pluck('name', 'id'),
//            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
//            'grades' => $grades, // Pass grades to the view
//            'results' => $results,
//            'reporttype' => $reporttype,
//            'archiveyears' => $archiveyears,
//            'reportarchivedoc' => $reportarchivedoc,
//        ]);





        foreach ($results as $res) {
            // Get the university name by using the university_id from each result
            if (isset($res->university_id)) {
                $university = University::find($res->university_id);
                $res->university_id = $university ? $university->name : 'Unknown'; // Fallback to 'Unknown' if not found
            }

            // Get the department name using the department_id from each result
            if (isset($res->department_id)) {
                $department = Department::find($res->department_id);
                $res->department_id = $department ? $department->name : 'Unknown'; // Fallback to 'Unknown' if not found
            }

            // Get the faculty name using the faculty_id from each result
            if (isset($res->faculty_id)) {
                $faculty = Faculty::find($res->faculty_id);
                $res->faculty_id = $faculty ? $faculty->name : 'Unknown'; // Fallback to 'Unknown' if not found
            }

            // Get the grade name using the grade_id from each result
            if (isset($res->grade_id)) {
                $grade = Grade::find($res->grade_id);
                $res->grade_id = $grade ? $grade->name : 'Unknown'; // Fallback to 'Unknown' if not found
            }

            if (isset($res->status_id)) {
                $status = Archivedatastatus::find($res->status_id);
                $res->status_id = $status ? $status->status : 'Unknown'; // Fallback to 'Unknown' if not found
            }

            if (isset($res->qc_status_id)) {
                $qc_status = Archiveqcstatus::find($res->qc_status_id);
                $res->qc_status_id = $qc_status ? $qc_status->qc_status : 'Unknown'; // Fallback to 'Unknown' if not found
            }

            


        }




        return Excel::download(new AllArchiveExports($results,$headerTable), 'all-Archives.xlsx');



    }








}







