<?php

namespace App\Http\Controllers\Archive;

use App\DataTables\ArchiveDataTable;
use App\Http\Controllers\Controller;
use App\Imports\ArchivedatasExelImport;
use App\Models\Archive;
use App\Models\Archivedata;
use App\Models\Archiveqcstatus;
use App\Models\ArchiveRole;
use App\Models\ArchiveYear;
use App\Models\ArchiveDepartment;
use App\Models\Archiveimage;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\University;
use Illuminate\Support\Facades\Session;
use DB;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Exports\ArchiveExcelTemplateExport;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Facades\Log;



class ArchiveController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-archive', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-archive', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-archive', ['only' => ['edit', 'update', 'updateStatus', 'update_groups']]);
        $this->middleware('permission:delete-archive', ['only' => ['destroy']]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ArchiveDataTable $dataTable)
    {

        return $dataTable->render('archive.index', [
            'title' => trans('general.archive'),
            'description' => trans('general.archive_list')
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function create(Request $request)
    {
        $submissionToken = $request->query('submission_token');
        if (!is_string($submissionToken) || !Str::isUuid($submissionToken)) {
            return redirect()->route('archive.create', ['submission_token' => (string) Str::uuid()]);
        }

        if ($this->archiveSubmissionExists($this->archiveSubmissionKey($submissionToken))) {
            return $this->archiveAlreadyRegistered();
        }

        $university_id = auth()->user()->university_id;

        if ($university_id > 0) {

            $universities = University::where('id', $university_id)->pluck('name', 'id');

        } else {


            $universities = University::pluck('name', 'id');
        }
        $archiveyears = ArchiveYear::pluck('year', 'id');

        return view('archive.create', [

            'title' => trans('general.archive'),
            'description' => trans('general.create_archive'),
            'submissionToken' => $submissionToken,
            'universities' => $universities,
//            'faculties' => Faculty::pluck('name', 'id'),
//            'faculty' => old('faculty') != '' ? Faculty::where('id', old('faculty'))->pluck('name', 'id') : [],
            'departments' => Department::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
            'archiveyears' => $archiveyears,
//            'grade' => old('grade') != '' ? Grade::where('id', old('grade'))->pluck('name', 'id') : [],
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
        $submission = $request->validate(['submission_token' => 'required|uuid']);
        $submissionToken = $submission['submission_token'];
        $creationKey = $this->archiveSubmissionKey($submissionToken);

        if ($this->archiveSubmissionExists($creationKey)) {
            return $this->archiveAlreadyRegistered();
        }

        // Validate the input
        $request->validate([
            'university_id' => 'required',
            'book_description' => 'required',
            'archive_year_id'  => 'required|array',
            'book_name' => 'required',
        ]);

        // Initialize archive variable
        $archive = null;

        try {
            $request->validate(['path' => 'required|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:1000000']);

            // Reserve the request key before converting the file. A concurrent retry
            // can only insert its own row if this transaction rolls back.
            DB::transaction(function () use ($request, $creationKey, &$archive) {
                $archive = new Archive([
                    'university_id' => $request->university_id,
                    'book_pagenumber' => $request->book_pagenumber,
                    'book_description' => $request->book_description,
                    'book_name' => $request->book_name,
                ]);
                $archive->creation_request_key = $creationKey;
                $archive->save();

                if ($request->filled('archive_year_id')) {
                    $years = array_filter($request->archive_year_id);
                    if (!empty($years)) {
                        $archive->archiveYears()->attach($years);
                    }
                }

                // Attach departments to the archive (many-to-many relationship)
                if ($request->has('department_id')) {
                    $archiveId = $archive->id;
                    foreach ($request->department_id as $dpt) {
                        $department = Department::where('id', $dpt)->first();
                        if ($department) {
                            ArchiveDepartment::create([
                                'university_id' => $request->university_id,
                                'faculty_id' => $department->faculty_id,
                                'archive_id' => $archiveId, // This will be updated after with a full date
                                'department_id' => $dpt,
                            ]);
                        }
                    }
                }

                $this->saveScanPages($request, $archive, replace: false);
            });
        } catch (ValidationException $e) {
            return redirect()->route('archive.create', ['submission_token' => $submissionToken])
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Throwable $e) {
            if ($e instanceof QueryException
                && in_array($e->errorInfo[0] ?? null, ['23000', '23505'], true)
                && $this->archiveSubmissionExists($creationKey)) {
                return $this->archiveAlreadyRegistered();
            }

            \Log::error('Archive store error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->route('archive.create', ['submission_token' => $submissionToken])
                ->withInput()
                ->withErrors(['path' => 'خطا در آپلود یا تبدیل فایل. لطفاً دوباره تلاش کنید.']);
        }

        // تخصیص خودکار کتاب تازه‌ثبت‌شده به کاربرِ درج‌کننده (نقش Data Entry)
        // تا در صفحه archiverole همان یوزر به عنوان درج‌کننده نمایش داده شود
        if (!is_null($archive) && auth()->check()) {
            try {
                $dataEntryRole = \App\Models\Role::withoutGlobalScopes()
                    ->where('name', 'Data_Entry')
                    ->where('archive_type', 2)
                    ->first();

                if ($dataEntryRole && auth()->user()->hasRole('Data_Entry')) {
                    $archive->update(['de_user_id' => auth()->id()]);

                    ArchiveRole::firstOrCreate(
                        [
                            'archive_id' => $archive->id,
                            'role_id'    => $dataEntryRole->id,
                            'user_id'    => auth()->id(),
                        ],
                        [
                            'status_id'    => 1,
                            'qc_status_id' => 1,
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ]
                    );
                }
            } catch (\Exception $e) {
                \Log::warning('Auto-assign book to DE user failed: ' . $e->getMessage(), [
                    'archive_id' => $archive->id,
                    'user_id'    => auth()->id(),
                ]);
            }
        }

        // Redirect to the archive index page
        return redirect(route('archive.index'));
    }

    private function archiveSubmissionKey(string $token): string
    {
        return hash('sha256', auth()->id() . ':' . strtolower($token));
    }

    private function archiveSubmissionExists(string $key): bool
    {
        return DB::table('archives')->useWritePdo()->where('creation_request_key', $key)->exists();
    }

    private function archiveAlreadyRegistered()
    {
        return redirect()->route('archive.index')->with('message', trans('archiveqc.book_already_registered'));
    }

    public function show($id)
    {
        $archive = Archive::findOrFail($id);
        return view('archive.show', compact('archive'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($archive_id)
    {
        $archive = Archive::findOrFail($archive_id);

// Retrieve departments related to the given archive_id
        $departments = ArchiveDepartment::where('archive_id', $archive_id)
            ->pluck('department_id');  // Using pluck to get only the department_id values


// Assign the retrieved departments to the archive object
        $archive->departments = $departments;

        $university_id = auth()->user()->university_id;

        if ($university_id > 0) {

            $universities = University::where('id', $university_id)
                ->whereNull('deleted_at')->pluck('name', 'id');

        } else {


            $universities = University::pluck('name', 'id');
        }


        // Retrieve existing archive images
        $archiveImages = Archiveimage::where('archive_id', $archive->id)->get();
        $archiveyears = ArchiveYear::pluck('year', 'id');
        return view('archive.edit', [
            'title' => trans('general.archive'),
            'description' => trans('general.edit_archive'),
            'archive' => $archive,
            'universities' => $universities,
//            'faculties' => Faculty::pluck('name', 'id'),
//            'faculty' => old('faculty') != '' ? Faculty::where('id', old('faculty'))->pluck('name', 'id') : [],
            'departments' => Department::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
            'archiveImages' => $archiveImages,
            'archiveyears' => $archiveyears,
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $archive_id)
    {
        $request->validate([
            'university_id' => 'required',
//            'faculty_id' => 'required',
//            'department_id' => 'required',
            'archive_year_id'  => 'required|array',
//            'book_pagenumber' => 'required',
            'book_description' => 'required',
            'book_name' => 'required',
            'path' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:1000000',
        ]);

        $archive = Archive::findOrFail($archive_id);

        \DB::transaction(function () use ($request, $archive) {
            $archive->update([
                'university_id' => $request->university_id,
//                'faculty_id' => $request->faculty_id,
//                'department_id' => $request->department_id,
//                'book_year' => $request->book_year,
                'book_pagenumber' => $request->book_pagenumber,
                'book_description' => $request->book_description,
                'book_name' => $request->book_name,
            ]);


             if ($request->filled('archive_year_id')) {
                $years = array_filter($request->archive_year_id);
                if (!empty($years)) {
                    $archive->archiveYears()->sync($years);
                }
               }


            ArchiveDepartment::where('archive_id', $archive->   id)->delete();

            // // Attach departments to the archive (many-to-many relationship)
            // if ($request->has('department_id')) {
            //     $departmentIds = $request->department_id;

            //     $archiveId = $archive->id;
            //     foreach ($request->department_id as $dpt) {
            //         $department=Department::where('id',$dpt)->first();
            //         $departInsert = ArchiveDepartment::create([
            //                 'university_id' => $request->university_id,
            //                 'faculty_id' => $department->faculty_id,
            //                 'archive_id' => $archiveId, // This will be updated after with a full date
            //                 'department_id' => $dpt]
            //         );

            //     }
            // }

              if ($request->has('department_id')) {

                $departmentIds = (array) $request->department_id;  // تبدیل به آرایه

                foreach ($departmentIds as $dpt) {

                    $department = Department::findOrFail($dpt);

                    ArchiveDepartment::create([
                        'university_id' => $request->university_id,
                        'faculty_id' => $department->faculty_id,
                        'archive_id' => $archive->id,
                        'department_id' => $dpt,
                    ]);
                }
            }

//            if ($request->book_year) {
//                // Append '-01-01' to the provided year to form a full date
//                $book_year = $request->book_year . '';
//                $archive->book_year = $book_year;
//                $archive->save();
//            }

 
            if ($request->hasFile('path')) {
                $this->saveScanPages($request, $archive, replace: true);
            }
        });

        return redirect(route('archive.index'));
    }


    private function saveScanPages(Request $request, Archive $archive, bool $replace): void
    {
        $paths = PDFToJPGController::convert($request->file('path'), $archive);

        if ($replace) {
            Archiveimage::where('archive_id', $archive->id)->delete();
        }

        $pages = [];
        foreach ($paths as $index => $path) {
            $pages[] = [
                'book_pagenumber' => $index + 1,
                'archive_id' => $archive->id,
                'status_id' => 1,
                'path' => $path,
            ];
        }

        Archiveimage::insert($pages);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */


    public function resetQcUser(Archive $archive)
    {
        // Authorization check
        if (auth()->user()->can('reset-qc-user')) {
          

             $archive->update([
                'qc_user_id' => null,
                'qc_status_id' => 1 // Set to a valid status ID instead of null
            ]);
          
            return redirect()->back()->with('success', 'QC User reset successfully.');
        }

        return redirect()->back()->with('error', 'Unauthorized action.');
    }


       


        public function resetDeUser(Archive $archive)
        {
            if (!auth()->user()->can('reset-de-user')) {
                return redirect()->back()->with('error', 'Unauthorized action.');
            }
            // Find a default status (e.g., "unassigned" status with ID 1)
            $archive->update([
                'de_user_id' => null,
                'status_id' => 1 // Set to a valid status ID instead of null
            ]);

            return redirect()->back()->with('success', 'DE User reset successfully.');
        }



    public function destroy($archive_id)
    {
        $archive = Archive::findOrFail($archive_id);

        \DB::transaction(function () use ($archive) {
            // Delete associated archive images
            Archiveimage::where('archive_id', $archive->id)->delete();

            // Delete the archive record
            $archive->delete();
        });

        // Delete associated directory and files
        $directory = public_path() . '/archivefiles/' . $archive->book_name;
        if (is_dir($directory)) {
            \File::deleteDirectory($directory);
            echo "Directory '$directory' and its contents deleted successfully.";
        } else {
            echo "Directory '$directory' does not exist.";
        }

        return redirect(route('archive.index'));

    }



    public function viewCsv(Request $request, $archiveId)
    {
        $archive = Archive::findOrFail($archiveId); // Fetch the specific archive by ID


        // "Check user authentication in url page number
        // if($archive==null || $archive->de_user_id==null || $archive->de_user_id!=auth()->user()->id ){
        //     return back();
        // }

        $user = auth()->user();
        // Check if user has the right role OR owns the resource
        if ($user->hasRole('system-developer')) {
            // Software Developer can access any archive - no further checks needed
        } elseif ($archive == null || $archive->de_user_id == null || $archive->de_user_id != $user->id) {
            // Regular users must pass ownership check
            return back()->with('error', 'Access denied.');
        }

      


        $universities = University::pluck('name', 'id');
        $faculties = Faculty::pluck('name', 'id');
        $departments = Department::pluck('name', 'id');
        $grades = Grade::pluck('name', 'id');

        // Get related faculties and departments for the given archiveId
        $archiveDepartments = ArchiveDepartment::where('archive_id', $archiveId)
            ->with(['faculty', 'department'])
            ->get();





        $archiveImage = ArchiveImage::where('archive_id', $archiveId); // Fetch the archive image for the given ID.

        // Check if $archiveImage exists
        if ($archiveImage) {
            // Calculate the total number of pages for the book
            $totalPages = ArchiveImage::where('archive_id', $archiveId)->count();
        } else {
            $totalPages = 0; // Fallback if $archiveImage does not exist
        }




        return view('archive.csvfile', [
            'title' => trans('general.archive'),
            'description' => trans('general.upload_csv'),
            'universities' => $universities,
            'faculties' => $faculties,
            'departments' => $departments,
            'grades' => $grades,
            'archive' => $archive, // Pass the specific archive details
            'archiveDepartments' => $archiveDepartments, // Pass the related faculties and departments
            'archiveImage' => $archiveImage,
            'archiveImages' => $archive->images()->with(['archivedatas' => function ($query) {
                $query->select('id', 'archiveimage_id', 'name', 'last_name', 'column_number')->orderBy('column_number');
            }])->orderBy('book_pagenumber')->get(),
            'totalPages' => $totalPages,


        ]);
    }


    public function downloadTemplate()
    {
        return Excel::download(new ArchiveExcelTemplateExport, 'archive_template.csv');
    }



    public function import(Request $request)
    {
        $validated = $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:9216',
            'archive_id' => 'required|integer|min:1',
        ], [
            'csv_file.required' => 'لطفاً فایل CSV را انتخاب کنید.',
            'csv_file.mimes' => 'فقط فایل CSV قابل قبول است.',
            'csv_file.max' => 'حجم فایل نباید بیشتر از ۹ مگابایت باشد.',
        ]);
        $archiveId = (int) $validated['archive_id'];
        $this->authorizeCsvArchive(Archive::findOrFail($archiveId));
        try {
            $report = (new \App\Services\ArchiveCsvImporter)->import(
                $request->file('csv_file')->getRealPath(),
                $archiveId,
                fn ($archive) => $this->authorizeCsvArchive($archive)
            );
            return redirect()->route('archive.view', ['archiveId' => $archiveId])->with('csv_report', $report);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Archive CSV import failed', ['archive_id' => $archiveId, 'exception' => $e]);
            return redirect()->route('archive.view', ['archiveId' => $archiveId])
                ->with('error', 'خواندن فایل متوقف شد. ردیف‌های درج‌شده محفوظ هستند؛ فایل را بررسی و دوباره آپلود کنید. موارد موجود تکرار نمی‌شوند.');
        }
    }
// تابع برای حذف آخرین آپلود (بدون پارامتر)
public function undoLastUpload()
{
    try {
        \Log::info('=== UNDO LAST UPLOAD START ===');

        // 1. دریافت آخرین batch_id از لاگ‌ها
        $lastBatch = \DB::table('uploaded_csv_logs')
            ->whereNotNull('batch_id')
            ->latest('created_at')
            ->value('batch_id');

        if (!$lastBatch) {
            \Log::warning('No batch found for undo');
            return redirect()->back()->with('error', 'آپلود معلومات پیدا نشد');
        }

        \Log::info('Last batch found', ['batch_id' => $lastBatch]);

        \DB::beginTransaction();

        // 2. دریافت تمام رکوردهای این batch
        $logs = \DB::table('uploaded_csv_logs')
            ->where('batch_id', $lastBatch)
            ->get();

        if ($logs->isEmpty()) {
            \DB::rollback();
            return redirect()->back()->with('error', 'لاگ‌های آپلود پیدا نشد');
        }

        $deletedCount = 0;
        $affectedPages = [];

        // 3. حذف هر رکورد
        foreach ($logs as $log) {
            if ($log->archivedata_id) {
                // پیدا کردن archivedata
                $archivedata = \App\Models\Archivedata::find($log->archivedata_id);
                
                if ($archivedata) {
                    // کاهش تعداد محصلین در صفحه
                    $archiveimage = \App\Models\Archiveimage::find($archivedata->archiveimage_id);
                    if ($archiveimage) {
                        $newTotal = max(0, $archiveimage->total_students - 1);
                        $archiveimage->update([
                            'total_students' => $newTotal,
                            'status_id' => $newTotal > 0 ? 4 : 1,
                        ]);
                        
                        $affectedPages[$archivedata->archiveimage_id] = true;
                    }
                    
                    // حذف archivedata
                    $archivedata->forceDelete();
                    $deletedCount++;
                }
            }
            
            // حذف لاگ
            \DB::table('uploaded_csv_logs')->where('id', $log->id)->delete();
        }

        // 4. آپدیت وضعیت صفحات
        foreach (array_keys($affectedPages) as $imageId) {
            $archiveimage = \App\Models\Archiveimage::find($imageId);
            if ($archiveimage) {
                // اگر صفحه خالی شد، وضعیت را به 1 تغییر بده
                if ($archiveimage->total_students == 0) {
                    $archiveimage->update(['status_id' => 1]);
                }
                
                // آپدیت وضعیت کتاب
                $archive = \App\Models\Archive::find($archiveimage->archive_id);
                if ($archive && $archive->status_id != 1) {
                    // بررسی آیا کتاب هنوز محصل دارد
                    $remainingStudents = \App\Models\Archivedata::where('archive_id', $archive->id)->count();
                    if ($remainingStudents == 0) {
                        $archive->update(['status_id' => 1]);
                        
                        // آپدیت نقش آرشیو
                        $archiveRole = \App\Models\ArchiveRole::where('archive_id', $archive->id)->first();
                        if ($archiveRole) {
                            $archiveRole->update(['status_id' => 1]);
                        }
                    }
                }
            }
        }

        \DB::commit();

        \Log::info('=== UNDO LAST UPLOAD COMPLETE ===', [
            'batch_id' => $lastBatch,
            'deleted_records' => $deletedCount,
            'affected_pages' => count($affectedPages)
        ]);

        return redirect()->back()->with('success', "✅ آخرین آپلود با موفقیت حذف شد. ({$deletedCount} رکورد حذف شد)");

    } catch (\Exception $e) {
        \DB::rollback();
        \Log::error('Error undoing last upload: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()->with('error', 'خطا در حذف آخرین آپلود: ' . $e->getMessage());
    }
}

// تابع جدید برای حذف کل کتاب (با پارامتر)
private function authorizeCsvArchive(Archive $archive)
{
    $user = auth()->user();
    abort_unless((int) $archive->de_user_id === (int) $user->id || $user->hasRole('system-developer'), 403);
}

public function undoBookUpload($archiveId)
{
    $this->authorizeCsvArchive(Archive::findOrFail($archiveId));

    try {
        \Log::info('=== UNDO BOOK UPLOAD START ===', ['archive_id' => $archiveId]);

        // بررسی وجود آرشیو
        DB::beginTransaction();
        $archive = Archive::lockForUpdate()->find($archiveId);
        
        if (!$archive) {
            DB::rollBack();
            return redirect()->back()->with('error', 'کتاب مورد نظر یافت نشد');
        }
        $this->authorizeCsvArchive($archive);

        // بررسی وضعیت QC
        if (in_array((int) $archive->qc_status_id, [2, 3, 4], true)
            || $archive->images()->whereIn('qc_status_id', [2, 3, 4])->exists()) {
            DB::rollBack();
            return redirect()->back()->with('error', 'این کتاب قابل لغو نیست زیرا قبلاً به کنترل کیفیت معرفی شده است.');
        }

        // 1. شمارش محصلین قبل از حذف
        $studentCount = Archivedata::where('archive_id', $archiveId)->count();
        
        if ($studentCount === 0) {
            \DB::rollback();
            return redirect()->back()->with('error', 'هیچ محصلی برای این کتاب یافت نشد');
        }

        // 2. دریافت image_id های مورد استفاده
        $pageStats = Archivedata::where('archive_id', $archiveId)
            ->select('archiveimage_id', \DB::raw('COUNT(*) as student_count'))
            ->groupBy('archiveimage_id')
            ->get()
            ->pluck('student_count', 'archiveimage_id')
            ->toArray();

        // 3. دریافت IDهای archivedata قبل از حذف
        $archivedataIds = Archivedata::where('archive_id', $archiveId)
            ->pluck('id')
            ->toArray();

        // 4. حذف لاگ‌های مربوطه اگر IDها وجود دارند
        if (!empty($archivedataIds) && \Schema::hasTable('uploaded_csv_logs')) {
            \DB::table('uploaded_csv_logs')
                ->whereIn('archivedata_id', $archivedataIds)
                ->delete();
        }

        // 5. حذف رکوردهای Archivedata
        $deleted = Archivedata::where('archive_id', $archiveId)->forceDelete();
        
        if (!$deleted) {
            \DB::rollback();
            return redirect()->back()->with('error', 'خطا در حذف رکوردها');
        }

        // 6. آپدیت Archiveimage ها
        foreach ($pageStats as $imageId => $removedCount) {
            $archiveimage = $archive->images()->find($imageId);
            if ($archiveimage) {
                $newTotal = $archiveimage->archivedatas()->where('archive_id', $archiveId)->count();
                $archiveimage->update([
                    'total_students' => $newTotal,
                    'status_id' => $newTotal > 0 ? 4 : 1,
                ]);
            }
        }

        // 7. آپدیت وضعیت کتاب
        $archive->update(['status_id' => 1]);
        
        // 8. آپدیت نقش آرشیو اگر وجود دارد
        $archiveRole = ArchiveRole::where('archive_id', $archiveId)->first();
        if ($archiveRole) {
            $archiveRole->update(['status_id' => 1]);
        }

        \DB::commit();

        \Log::info('=== UNDO BOOK UPLOAD COMPLETE ===', [
            'archive_id' => $archiveId,
            'deleted_students' => $studentCount,
            'affected_pages' => count($pageStats),
            'book_name' => $archive->book_name
        ]);

        // مهم: فقط از یک روش استفاده کنید
        return redirect()->route('archive.view', $archiveId)
            ->with('success', "✅ تمام {$studentCount} محصل کتاب '{$archive->book_name}' با موفقیت حذف شدند.");

    } catch (\Exception $e) {
        \DB::rollback();
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            throw $e;
        }
        \Log::error('Error undoing book upload: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'archive_id' => $archiveId
        ]);

        return redirect()->back()->with('error', 'خطا در حذف محصلان کتاب.');
    }
}




}
