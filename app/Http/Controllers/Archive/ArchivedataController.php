<?php

namespace App\Http\Controllers\Archive;

use App\DataTables\ArchivedataDataTable;
use App\DataTables\SelectForNameUpdatDataTable;
use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Archivedata;
use App\Models\Archivedatastatus;
use App\Models\ArchiveDepartment;
use App\Models\Archiveimage;
use App\Models\Archiveqcstatus;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Grade;
use App\Models\SemesterType;
use App\Models\University;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Baqidari;


class ArchivedataController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-archivedata', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-archivedata', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-archivedata', ['only' => ['edit', 'update', 'updateStatus', 'update_groups']]);
        $this->middleware('permission:delete-archivedata', ['only' => ['destroy']]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ArchivedataDataTable $dataTable)
    {
        $archivedataRecords = Archivedata::all();

        return $dataTable->render('archivedata.index', [
            'title' => trans('general.search'),
            'description' => trans('general.archivesearch'),
            'archivedataRecords' => $archivedataRecords,
        ]);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */

//create function
    public function create()
    {
        $archivedataRecords = Archivedata::all();

        $archive_id = auth()->user()->archive_id;
        $archives = null;

        if ($archive_id > 0) {
            $archives = Archive::where('id', $archive_id)->pluck('book_name', 'id');
        } else {
            $archives = Archive::pluck('book_name', 'id');
        }

        // Retrieve other data needed for the view
        $semester_types = SemesterType::pluck('name', 'id');
        $grades = Grade::pluck('name', 'id');

        $universities = University::pluck('name', 'id');
        $faculties = Faculty::pluck('name', 'id');
        $departments = Department::pluck('name', 'id');




        return view('archivedata.create', [
            'title' => trans('general.archivedata'),
            'description' => trans('general.create_archivedata'),
            'archives' => $archives ?? null,
            'archiveimages' => Archiveimage::pluck('id'),
            'archiveimage' => old('archiveimage') != '' ? Archiveimage::where('id', old('archiveimage'))->pluck('id') : [],
            'semester_types' => $semester_types,
            'semester_type' => old('semester_type') != '' ? SemesterType::where('id', old('semester_type'))->pluck('name', 'id') : [],
            'grades' => $grades,
            'grade' => old('grade') != '' ? Grade::where('id', old('grade'))->pluck('name', 'id') : [],
            'archivedataRecords' => $archivedataRecords,
            'universities' => $universities,
            'faculties' => $faculties,
            'faculty' => old('faculty') != '' ? Faculty::where('id', old('faculty'))->pluck('name', 'id') : [],
            'departments' => $departments,
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],

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
        $request->validate([
            'archivedata_id' => 'required',
            'archive_image_id' => 'required',
            'department_id' => 'required',
            'name' => 'required',
            // 'last_name' => 'required',
            'father_name' => 'required',
//            'grandfather_name' => 'required',
//            'school' => 'required',
//            'school_graduation_year' => 'required',
//            'tazkira_number' => 'required',
//            'birth_date' => 'required',
//            'birth_place' => 'required',
//            'time' => 'required',
            // 'kankor_id' => 'required',
            // 'semester_type_id' => 'required',
            'year_of_inclusion' => 'required',
            'graduated_year' => 'required',
            // 'transfer_year' => 'required',
            // 'leave_year' => 'required',
            // 'failled_year' => 'required',
//            'monograph_date' => 'required',
//            'monograph_number' => 'required',
            // 'monograph_title' => 'required',
//            'averageOfScores' => 'required',
//            'grade_id' => 'required',
//            'description' => 'required',



        ]);



        $department=Department::where('id',$request->department_id)->first();
        \DB::transaction(function () use ($department, $request) {



            $archivedata = Archivedata::create([
                'university_id' => $department->university_id,
                'faculty_id' => $department->faculty_id,
                'department_id' => $department->id,
                'column_number' => $request->column_number,
                'archive_id' => $request->archivedata_id,
                'archiveimage_id' => $request->archive_image_id,
                'name' => $request->name,
                'last_name' => $request->last_name,
                'father_name' => $request->father_name,
                'grandfather_name' => $request->grandfather_name,
                'school' => $request->school,
                'school_graduation_year' => $request->school_graduation_year,
                'tazkira_number' => $request->tazkira_number,
                'birth_date' => $request->birth_date,
                'birth_place' => $request->birth_place,
                'time' => $request->time,
                'kankor_id' => $request->kankor_id,
                'semester_type_id' => $request->semester_type_id,
                'year_of_inclusion' => $request->year_of_inclusion,
                'graduated_year' => $request->graduated_year,
                'transfer_year' => $request->transfer_year,
                'leave_year' => $request->leave_year,
                'failled_year' => $request->failled_year,
                'monograph_date' => $request->monograph_date,
                'monograph_number' => $request->monograph_number,
                'monograph_title' => $request->monograph_title,
                'averageOfScores' => $request->averageOfScores,
                'grade_id' => $request->grade_id,
                'description' => $request->description,

            ]);


        });



        

        $archiveImageRecord = Archiveimage::where('id', $request->archive_image_id)->first();
        $archiveRecord = Archive::where('id', $request->archivedata_id)->first();
        $dataCount = Archivedata::where('archiveimage_id', $request->archive_image_id)->count();

        if ($dataCount == $archiveImageRecord->total_students) {
            $archiveImageRecord->status_id = 4;
            $archiveImageRecord->save();
        } else {
            $archiveImageRecord->status_id = 3;
            $archiveImageRecord->save();


            $archiveRecord->status_id = 3;
            $archiveRecord->save();
        }



        return $this->archiveBookDataEntryPageData($request->archive_image_id, $request->archivedata_id, $request->column_number);
    }


     public function selectForNameUpdate(SelectForNameUpdatDataTable $dataTable)
    {
        $records = Archivedata::all();

        return $dataTable->render('archivedata.select-for-update', [
            'title' => trans('general.search'),
            'description' => trans('general.archivesearch'),
            'records' => $records,
        ]);
    }



   public function showEditNameForm(Archivedata $archivedata)
{
    // Authorize the action
    $this->authorize('update-name', $archivedata);
    
    return view('archivedata.edit-name', ['archiveData' => $archivedata]);
}

public function updateName(Request $request, Archivedata $archivedata)
{
    $request->validate([
        'name' => 'nullable|string|max:255',
        'father_name' => 'nullable|string|max:255',
        'grandfather_name' => 'nullable|string|max:255',
        'birth_date' => 'nullable|date',
        'updateName_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'updateName_desc' => 'nullable|string'
    ]);

    try {
        DB::transaction(function () use ($request, $archivedata) {
            // Store original values for history
            $originalValues = $archivedata->getOriginal();
            $fields = ['name', 'father_name', 'grandfather_name', 'birth_date', 'updateName_desc'];

            foreach ($fields as $field) {
                if ($request->has($field) && $request->$field !== null) {
                    $archivedata->{"previous_$field"} = $originalValues[$field] ?? null;
                    $archivedata->$field = $request->$field;
                }
            }

            // Handle file upload for `updateName_img`
            if ($request->hasFile('updateName_img')) {
                // Delete the old image if it exists
                if ($archivedata->updateName_img) {
                    $oldImagePath = public_path($archivedata->updateName_img);
                    if (file_exists($oldImagePath)) {
                        @unlink($oldImagePath);
                    }
                }

                // Define upload directory
                $uploadPath = public_path('uploads/updateName_img');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Generate unique file name
                $image = $request->file('updateName_img');
                $imageName = Str::random(40) . '.' . $image->getClientOriginalExtension();

                // Move the file to the destination
                $image->move($uploadPath, $imageName);
                $archivedata->updateName_img = '/uploads/updateName_img/' . $imageName;
            }

            $archivedata->save();
        });

        return response()->json([
            'success' => 'اطلاعات با موفقیت به روز رسانی شد',
            'new_values' => $archivedata->fresh()->toArray()
        ]);
    } catch (\Exception $e) {
        \Log::error('Update failed: ' . $e->getMessage());
        return response()->json([
            'error' => 'خطا در ذخیره سازی',
            'details' => 'An unexpected error occurred while updating the data.'
        ], 500);
    }
}






    public function loadPage($bookId)
    {
        $archive_id = Archiveimage::where('archive_id', $bookId)->get();
        return response()->json(array('pages' => $archive_id), 200);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $archivedata = Archivedata::find($id);
        $archives = Archive::pluck('book_name', 'id');
        $semester_types = SemesterType::pluck('name', 'id');
        $grades = Grade::pluck('name', 'id');

        return view('archivedata.edit', compact('archivedata', 'archives', 'semester_types', 'grades'));
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
        $request->validate([
            'archivedata_id' => 'required',
            'archive_image_id' => 'required',
            'name' => 'required',
            // 'last_name' => 'required',
            'father_name' => 'required',
//            'grandfather_name' => 'required',
//            'school' => 'required',
//            'school_graduation_year' => 'required',
//            'tazkira_number' => 'required',
//            'birth_date' => 'required',
//            'birth_place' => 'required',
//            'time' => 'required',
            // 'kankor_id' => 'required',
            // 'semester_type_id' => 'required',
            'year_of_inclusion' => 'required',
            'graduated_year' => 'required',
            // 'transfer_year' => 'required',
            // 'leave_year' => 'required',
            // 'failled_year' => 'required',
//            'monograph_date' => 'required',
//            'monograph_number' => 'required',
            // 'monograph_title' => 'required',
//            'averageOfScores' => 'required',
//            'grade_id' => 'required',
//            'description' => 'required',



        ]);


        $department=Department::where('id',$request->department_id)->first();
        \DB::transaction(function () use ($department,$request, $id) {
            $archivedata = Archivedata::find($id);

            if ($archivedata->qc_status_id == 2 || $archivedata->qc_status_id == 3) {
                session()->flash('error', 'این رکورد قابل اپدیت نیست زیرا قبلاً  به کنترل کیفیت معرفی شده است.');
                return back();
            }

            $archivedata->update([
                'university_id' => $department->university_id,
                'faculty_id' => $department->faculty_id,
                'department_id' => $department->id,
                'column_number' => $request->column_number,
                'archive_id' => $request->archivedata_id,
                'archiveimage_id' => $request->archive_image_id,
                'name' => $request->name,
                'last_name' => $request->last_name,
                'father_name' => $request->father_name,
                'grandfather_name' => $request->grandfather_name,
                'school' => $request->school,
                'school_graduation_year' => $request->school_graduation_year,
                'tazkira_number' => $request->tazkira_number,
                'birth_date' => $request->birth_date,
                'birth_place' => $request->birth_place,
                'time' => $request->time,
                'kankor_id' => $request->kankor_id,
                'semester_type_id' => $request->semester_type_id,
                'year_of_inclusion' => $request->year_of_inclusion,
                'graduated_year' => $request->graduated_year,
                'transfer_year' => $request->transfer_year,
                'leave_year' => $request->leave_year,
                'failled_year' => $request->failled_year,
                'monograph_date' => $request->monograph_date,
                'monograph_number' => $request->monograph_number,
                'monograph_title' => $request->monograph_title,
                'averageOfScores' => $request->averageOfScores,
                'grade_id' => $request->grade_id,
                'description' => $request->description,

            ]);
        });

        $archiveImageRecord = Archiveimage::where('id', $request->archive_image_id)->first();
        $archiveRecord = Archive::where('id', $request->archivedata_id)->first();
        $dataCount = Archivedata::where('archiveimage_id', $request->archive_image_id)->count();

        if ($dataCount == $archiveImageRecord->total_students) {
            $archiveImageRecord->status_id = 4;
            $archiveImageRecord->save();
        } else {
            $archiveImageRecord->status_id = 3;
            $archiveImageRecord->save();


            $archiveRecord->status_id = 3;
            $archiveRecord->save();
        }

        // return $this->archiveBookDataEntryPageData($request->archive_image_id, $request->archivedata_id, $request->column_number);
        return redirect()->back();

    }





    // app/Http/Controllers/YourController.php
    public function show($id)
    {
        //baqidari part
        $baqidaris = Baqidari::all();
        $archive = Archivedata::findOrFail($id);
        $baqidari = Baqidari::where('archivedata_id', $id)->first();


        // Fetch the specific archivedata record with the related archiveimage
        $archivedata = Archivedata::with('archiveimage')->findOrFail($id);

        // Fetch all archivedata records (if needed)
        $archivedataRecords = Archivedata::all();

        // Fetch archives based on the authenticated user's archive_id
        $archive_id = auth()->user()->archive_id;
        $archives = $archive_id > 0
            ? Archive::where('id', $archive_id)->pluck('book_name', 'id')
            : Archive::pluck('book_name', 'id');

        // Fetch other related data
        $semester_types = SemesterType::pluck('name', 'id');
        $grades = Grade::pluck('name', 'id');
        $universities = University::pluck('name', 'id');
        $faculties = Faculty::pluck('name', 'id');
        $departments = Department::pluck('name', 'id');
        $archivedatastatus = Archivedatastatus::pluck('status', 'id');
        $archiveqcstatus = Archiveqcstatus::pluck('qc_status', 'id');
        // Pass data to the view
        return view('archivedata.details', [
            'title' => trans('general.archivedata'),
            'description' => trans('general.create_archivedata'),
            'archivedata' => $archivedata, // The specific record to display
            'archivedataRecords' => $archivedataRecords, // All records (if needed)
            'archives' => $archives,
            'semester_types' => $semester_types,
            'grades' => $grades,
            'universities' => $universities,
            'faculties' => $faculties,
            'departments' => $departments,
            'archivedatastatus' => $archivedatastatus,
            'archiveqcstatus' => $archiveqcstatus,
            'archiveimage' => $archivedata->archiveimage, // Pass the related archiveimage
            
            //baqidari part
            'archive' => $archive,
            'data' => $baqidari,
            'id' => $id,
            'baqidaris' => $baqidaris
        ]);
    }







// این فنگشن مربوط سرچ صفحات میشود
    public function archiveBookDataEntrySearch(Request $request)
    {

        $archiveRecord = Archive::find($request->archiveId);

        // "Check user authentication when inserting a page number in the search box."
        if($archiveRecord==null || $archiveRecord->de_user_id==null || $archiveRecord->de_user_id!=auth()->user()->id){
            return back()->with('error', 'قابل دسترس نیست');
        }

        $archiveImage = Archiveimage::where('archive_id', $request->archiveId)
            ->where('book_pagenumber', $request->pageNo)->orderBy('id', 'asc')->first();

        if ($archiveImage == null) {
            return redirect()->back()->with('error', 'لطفا نمبر صفحه را وارد کنید!');

        }
        

//        // Check the qc_status_id
//        if ($archiveImage->qc_status_id == 2 || $archiveImage->qc_status_id == 3) {
//            session()->flash('error', 'این رکورد قابل اپدیت نیست زیرا قبلاً به کنترل کیفیت معرفی شده است.');
//            return back();
//        }

        $archives = Archive::pluck('book_name', 'id');
        $semester_types = SemesterType::pluck('name', 'id');
        $grades = Grade::pluck('name', 'id');


        $total_student = $this->totalStudent();
        $archivedata = [];

        if ($archiveImage->status_id == 3) {

            $archivedata = Archivedata::where('archiveimage_id', $archiveImage->id)->get();
        }
        $archiveData1 = null;
        $column_number = null;

        $archivedataRecords = Archivedata::where('archiveimage_id', $archiveImage->id)->get();
        $totalPages = Archiveimage::where('archive_id', $request->archiveId)->count();

        $archiveimagerejectqc = Archiveimage::where('qc_status_id', 4)->where('archive_id',$request->archiveId)->get();

        return view('archivedata.create', compact('archivedataRecords', 'column_number', 'archiveData1', 'total_student', 'archiveImage', 'archiveRecord', 'archivedata', 'archives', 'semester_types', 'grades' ,'totalPages','archiveimagerejectqc'));
    }

    public function archiveBookDataEntry($id)
    {

        $archiveRecord = Archive::find($id);
        $archiveImage = Archiveimage::where('archive_id', $id)->whereIn('status_id', [1, 3])->orderBy('id', 'asc')->first();

        // "Check user authentication in url page number
        if($archiveRecord==null || $archiveRecord->de_user_id==null || $archiveRecord->de_user_id!=auth()->user()->id){
            return back();
        }


        if ($archiveImage == null) {
            $archiveImage = Archiveimage::where('archive_id', $id)->orderBy('id', 'asc')->first();
        }
        $archives = Archive::pluck('book_name', 'id');
        $semester_types = SemesterType::pluck('name', 'id');
        $grades = Grade::pluck('name', 'id');


        $total_student = $this->totalStudent();
        $archivedata = [];

        if ($archiveImage->status_id == 3) {

            $archivedata = Archivedata::where('archiveimage_id', $archiveImage->id)->get();
        }
        $archiveData1 = null;
        $column_number = null;

        $archivedataRecords = Archivedata::where('archiveimage_id', $archiveImage->id)->get();
        $totalPages = Archiveimage::where('archive_id', $id)->count();


        $archiveimagerejectqc = Archiveimage::where('qc_status_id', 4)->where('archive_id', $id)->get();



        return view('archivedata.create', compact('archivedataRecords', 'column_number', 'archiveData1', 'total_student', 'archiveImage', 'archiveRecord',
            'archivedata', 'archives', 'semester_types', 'grades', 'totalPages','archiveimagerejectqc'));
    }

    // این فنگشن مربوط صفحات قبلی و بعدی است
    public function archiveBookDataEntryPage($pageId, $id, $step)
    {
        $archiveRecord = Archive::find($id);

//        if ($archiveRecord->qc_status_id == 2 || $archiveRecord->qc_status_id == 3) {
//            session()->flash('error', 'این رکورد قابل اپدیت نیست زیرا قبلاً  به کنترل کیفیت معرفی شده است.');
//            return back();
//        }

        // "Check user authentication in url page number
        if($archiveRecord==null || $archiveRecord->de_user_id==null || $archiveRecord->de_user_id!=auth()->user()->id){
            return back();
        }


        $archiveImageCurrent = Archiveimage::find($pageId);
        // Check if $archiveImageCurrent is null before proceeding
        if ($archiveImageCurrent === null) {

            return redirect()->back()->with('error', 'No archive image found with the given page ID.');
        }


        $CEHCK = Archiveimage::where('archive_id', $id)
            ->where('status_id', [1, 3])
            ->orderBy('id', 'asc')->first();

        $CEHCKData = Archivedata::where('archive_id', $id)
            ->where('status_id', [1, 3])->first();


        if($CEHCK!=null && $CEHCKData!=null){
            $archiveRecord->update([
                'status_id' => '3',
            ]);
        }else{
            $archiveRecord->update([
                'status_id' => '4',
            ]);
        }

        $archiveImage = Archiveimage::where('archive_id', $id)
            ->where('book_pagenumber', ($archiveImageCurrent->book_pagenumber + $step))
            ->orderBy('id', 'asc')->first();

        // Check if $archiveImage is null before accessing its properties
        if ($archiveImage === null) {
            // all image entry
            $archiveImageIsComplete = Archiveimage::where('archive_id', $id)
                ->where('status_id', [1, 3])
                ->orderBy('id', 'asc')->first();
            if ($archiveImageIsComplete == null || $CEHCKData==null) {

                $archiveUpdate = Archive::find($id);
                $archiveUpdate->update([
                    'status_id' => '4',
                ]);
                return redirect()->back()->with('error', ' صفحه آخر کتاب است');
            } else {
                $archiveImage = $archiveImageIsComplete;
            }
        }

        $archives = Archive::pluck('book_name', 'id');
        $semester_types = SemesterType::pluck('name', 'id');
        $grades = Grade::pluck('name', 'id');

        $total_student = $this->totalStudent();


        $archiveData = [];
        if ($archiveImage->status_id == 3) {
            $archiveData = Archivedata::where('archiveimage_id', $archiveImage->id)->get();
        }

        $archiveData1 = null;
        $column_number = null;


        $archivedataRecords = Archivedata::where('archiveimage_id', $archiveImage->id)->get();

        $totalPages = Archiveimage::where('archive_id', $id)->count();


        $archiveimagerejectqc = Archiveimage::where('qc_status_id', 4)->where('archive_id', $id)->get();

        return view('archivedata.create', compact('archivedataRecords', 'column_number', 'archiveData1', 'total_student',
            'archiveImage', 'archiveRecord', 'archiveData', 'archives', 'semester_types', 'grades', 'totalPages','archiveimagerejectqc'));
    }

//ای بخش مربوط ایجاد کالم در صفحه برای محصل است
    public function archiveBookDataEntryPageData($pageId, $id, $column_number)
    {
        $archiveData1 =
            Archivedata::where('archive_id', $id)
                ->where('archiveimage_id', $pageId)
                ->where('column_number', $column_number)
                ->first();

        $archiveRecord = Archive::find($id);

        // Check the qc_status_id
        if ($archiveRecord->qc_status_id == 2 || $archiveRecord->qc_status_id == 3) {
            session()->flash('error', 'این رکورد قابل اپدیت نیست زیرا قبلاً به کنترل کیفیت معرفی شده است.');
            return back();
        }


        $archiveImageCurrent = Archiveimage::where('id', $pageId)->first();
        $archiveImage = Archiveimage::where('archive_id', $id)
        ->where('book_pagenumber', ($archiveImageCurrent->book_pagenumber))->orderBy('id', 'asc')->first();
        $archives = Archive::pluck('book_name', 'id');
        $semester_types = SemesterType::pluck('name', 'id');
        $grades = Grade::pluck('name', 'id');
        $total_student = $this->totalStudent();


        $departments = Department::join('archive_departments', 'departments.id', '=', 'archive_departments.department_id')
            ->join('faculties', 'departments.faculty_id', '=', 'faculties.id') // Ensure the `faculties` table is joined
            ->where('archive_departments.archive_id', $archiveRecord->id)
            ->select(
                'departments.id',
                \DB::raw('CONCAT(departments.name, " [ پوهنځی : ", faculties.name , "] ") as text')
            )
            ->pluck('text', 'departments.id'); // Pluck the concatenated text with department ID as key



        $archiveData = [];
        if ($archiveImage->status_id == 3) {
            $archiveData = Archivedata::where('archiveimage_id', $archiveImage->id)->get();
        }
        $archivedataRecords = Archivedata::where('archiveimage_id', $archiveImage->id)->get();

        // Count the total number of pages
        $totalPages = Archiveimage::where('archive_id', $id)->count();

        $archiveimagerejectqc = Archiveimage::where('qc_status_id', 4)->where('archive_id', $id)->get();

        return view('archivedata.create', compact('archivedataRecords', 'column_number', 'archiveData1', 'total_student', 'archiveImage',
            'archiveRecord', 'archiveData', 'archives', 'semester_types', 'grades','departments', 'totalPages','archiveimagerejectqc'));


    }
// این فنگشن مربطو فاقد محصل میشود
    public function archiveBookDataEntryPageReject($pageId, $id, $step)
    {
        $archiveImageCurrent = Archiveimage::find($pageId);

        // Check the qc_status_id
        if ($archiveImageCurrent->qc_status_id == 2 || $archiveImageCurrent->qc_status_id == 3) {
            session()->flash('error', 'این رکورد قابل اپدیت نیست زیرا قبلاً به کنترل کیفیت معرفی شده است.');
            return back();
        }

        DB::delete('DELETE FROM archivedatas WHERE archiveimage_id = ?', [$pageId]);
        $archiveImageCurrent->update([
            'status_id' => '2',
            'total_students' => '0',
        ]);
        return $this->archiveBookDataEntryPage($pageId, $id, 1);
    }

    public function backRedirect(){
        return back();
    }
// این فنگشن مربوط به تایید و اضافه کردن کالم محصل میشود
    public function archiveBookDataEntryApprove(Request $request)
    {
        $archiveImageCurrent = Archiveimage::find($request->approve_page_id);
        $statusImage = 1;
        if ($archiveImageCurrent->total_students > $request->approve_total_student) {
            //archive data delete
            $v = ($archiveImageCurrent->total_students) - ($request->approve_total_student);
            DB::delete('DELETE FROM archivedatas WHERE archiveimage_id = ? and column_number >= ? ',
                [$request->approve_page_id, $archiveImageCurrent->total_students]);

        }

        // Check the qc_status_id
        if ($archiveImageCurrent->qc_status_id == 2 || $archiveImageCurrent->qc_status_id == 3) {
            session()->flash('error', 'این رکورد قابل اپدیت نیست زیرا قبلاً به کنترل کیفیت معرفی شده است.');
            return back();
        }


        $archiveDataCount = Archivedata::where('archiveimage_id', $request->approve_page_id)->count();
        if ($archiveDataCount == 0) {
            $statusImage = 1;
        } else {
            $statusImage = 3;
        }
        if ($archiveDataCount == $request->approve_total_student) {
            $statusImage = 4;
        }

        // Update total_students
        $archiveImageCurrent->update([
            'total_students' => $request->approve_total_student,
            'status_id' => $statusImage,
        ]);


        if ($archiveDataCount) {
            // Assuming $dataCompleted is a condition to check if the data is completed
            echo "Data has been successfully completed.";
        }

        return $this->archiveBookDataEntryPage($request->approve_page_id, $request->approve_archive_id, 0);

    }


    private function totalStudent()
    {
        $total_student = new \stdClass();
        $total_student->{'1'} = 1;
        $total_student->{'2'} = 2;
        $total_student->{'3'} = 3;
        $total_student->{'4'} = 4;
        $total_student->{'5'} = 5;
        $total_student->{'6'} = 6;
        $total_student->{'7'} = 7;
        $total_student->{'8'} = 8;
        $total_student->{'9'} = 9;
        $total_student->{'10'} = 10;
        return $total_student;
    }


    /**
     * Show the form for remove the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $archivedata = Archivedata::find($id);
        $archivedata->delete();

        return redirect()->route('archivedata.index')->with('success', 'Archived data deleted successfully');

//            DB::delete('DELETE FROM archivedatas WHERE id = ?', [$id]);
//           return redirect()->route('archivedata.index')->with('success', 'Archived data deleted successfully');


    }


}
