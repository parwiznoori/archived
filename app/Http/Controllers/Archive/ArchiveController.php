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
use App\Exports\ArchiveExcelTemplateExport;
use Illuminate\Support\Str;
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
    public function create()
    {
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
            'universities' => $universities,
            'departments' => Department::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
            'archiveyears' => $archiveyears,
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
        // Validate the input
        $request->validate([
            'university_id' => 'required',
            'book_description' => 'required',
            'book_name' => 'required',
            'path.*' => 'required|image|mimes:pdf,jpeg,png,jpg,gif,svg|max:1000000', 
        ]);

        // Initialize archive variable
        $archive = null;

        // Use a transaction to handle the archive creation
        \DB::transaction(function () use ($request, &$archive) {
            $archive = Archive::create([
                'university_id' => $request->university_id,
                'archive_year_id' => $request->archive_year_id,
                'book_pagenumber' => $request->book_pagenumber,
                'book_description' => $request->book_description,
                'book_name' => $request->book_name,
            ]);

            // Attach departments to the archive (many-to-many relationship)
            if ($request->has('department_id')) {
                $departmentIds = $request->department_id;

                $archiveId = $archive->id;
                foreach ($request->department_id as $dpt) {
                    $department = Department::where('id', $dpt)->first();
                    $departInsert = ArchiveDepartment::create([
                        'university_id' => $request->university_id,
                        'faculty_id' => $department->faculty_id,
                        'archive_id' => $archiveId,
                        'department_id' => $dpt
                    ]);
                }
            }
        });

        // Convert PDF to JPG and get the page count
        $pageCount = PDFToJPGController::convert($request, $archive);

        // If the archive was created successfully
        if ($archive != null) {
            // Create a directory for archive files
            $directory = public_path() . '/archivefiles/' . $archive->id . '-' . $archive->book_name;

            // Array to hold all archive images data
            $archiveImages = [];

            // Loop through each page count and generate the image paths
            for ($i = 1; $i <= $pageCount; $i++) {
                $archiveImages[] = [
                    'book_pagenumber' => $i,
                    'archive_id' => $archive->id,
                    'status_id' => 1,
                    'path' => '/archivefiles/' . $archive->id . '-' . $archive->book_name . '/' . $i . '.jpg',
                ];
            }

            // Bulk insert archive images into the database
            Archiveimage::insert($archiveImages);
        }

        // Redirect to the archive index page
        return redirect(route('archive.index'));
    }

    public function show($id)
    {
        $archive = $this->findArchiveWithAccessCheck($id);
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
        $archive = $this->findArchiveWithAccessCheck($archive_id);

        // Retrieve departments related to the given archive_id
        $departments = ArchiveDepartment::where('archive_id', $archive_id)
            ->pluck('department_id');

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
        $archive = $this->findArchiveWithAccessCheck($archive_id);

        $request->validate([
            'university_id' => 'required',
            'book_description' => 'required',
            'book_name' => 'required',
            'path.*' => 'required|image|mimes:pdf,jpeg,png,jpg,gif,svg|max:1000000',
        ]);

        $pageCount = 0;

        \DB::transaction(function () use ($pageCount, $request, $archive) {
            $archive->update([
                'university_id' => $request->university_id,
                'archive_year_id' => $request->archive_year_id,
                'book_pagenumber' => $request->book_pagenumber,
                'book_description' => $request->book_description,
                'book_name' => $request->book_name,
            ]);
            
            ArchiveDepartment::where('archive_id', $archive->id)->delete();

            // Attach departments to the archive (many-to-many relationship)
            if ($request->has('department_id')) {
                foreach ($request->department_id as $dpt) {
                    $department = Department::where('id', $dpt)->first();
                    ArchiveDepartment::create([
                        'university_id' => $request->university_id,
                        'faculty_id' => $department->faculty_id,
                        'archive_id' => $archive->id,
                        'department_id' => $dpt
                    ]);
                }
            }

            if ($request->path != "" && $request->path != null) {
                // Convert PDF to JPG and get the page count
                $pageCount = PDFToJPGController::convert($request, $archive);
            }

            if ($request->path != "" && $request->path != null) {
                $directory = public_path() . '/archivefiles/' . $archive->id . '-' . $archive->book_name;

                // Delete existing archive images
                Archiveimage::where('archive_id', $archive->id)->delete();

                // Upload new archive images
                $archiveImages = [];
                for ($i = 1; $i <= $pageCount; $i++) {
                    $archiveImages[] = [
                        'book_pagenumber' => $i,
                        'archive_id' => $archive->id,
                        'status_id' => 1,
                        'path' => '/archivefiles/' . $archive->id . '-' . $archive->book_name . '/' . $i . '.jpg',
                    ];
                }

                Archiveimage::insert($archiveImages);
            }
        });

        return redirect(route('archive.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($archive_id)
    {
        $archive = $this->findArchiveWithAccessCheck($archive_id);

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
        }

        return redirect(route('archive.index'));
    }

    /**
     * Helper method to find archive with access check
     */
    private function findArchiveWithAccessCheck($archive_id)
    {
        // ابتدا آرشیو را پیدا کنید
        $archive = Archive::find($archive_id);
        
        if (!$archive) {
            \Log::warning('Archive not found', [
                'archive_id' => $archive_id,
                'user_id' => auth()->id(),
                'url' => request()->url()
            ]);
            abort(404, 'آرشیو مورد نظر یافت نشد');
        }

        // بررسی دسترسی کاربر
        $userUniversities = auth()->user()->universities->pluck('id');
        
        if (!auth()->user()->hasRole('super-admin') && 
            !$userUniversities->contains($archive->university_id)) {
            \Log::warning('User cannot access archive', [
                'archive_id' => $archive_id,
                'user_id' => auth()->id(),
                'archive_university' => $archive->university_id,
                'user_universities' => $userUniversities->toArray()
            ]);
            abort(403, 'شما دسترسی به این آرشیو ندارید');
        }

        return $archive;
    }

    public function resetQcUser(Archive $archive)
    {
        // Authorization check
        if (auth()->user()->can('reset-qc-user')) {
            $archive->update([
                'qc_user_id' => null,
                'qc_status_id' => 1
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

        $archive->update([
            'de_user_id' => null,
            'status_id' => 1
        ]);

        return redirect()->back()->with('success', 'DE User reset successfully.');
    }



    // بقیه متدها بدون تغییر...


    public function viewCsv(Request $request, $archiveId)
    {
        $archive = Archive::findOrFail($archiveId); // Fetch the specific archive by ID


        // "Check user authentication in url page number
        if($archive==null || $archive->de_user_id==null || $archive->de_user_id!=auth()->user()->id){
            return back();
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
            'totalPages' => $totalPages,


        ]);
    }


    public function downloadTemplate()
    {
        return Excel::download(new ArchiveExcelTemplateExport, 'archive_template.csv');
    }



    public function import(Request $request)
    {
        $batchId = Str::uuid(); // Generate a unique batch ID

        // Validate the request
        $validated = $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:9048',
        ]);

        try {
            if ($request->hasFile('csv_file') && $request->file('csv_file')->isValid()) {
                $file = $request->file('csv_file');

                // Store the file
                $path = $file->storeAs('uploadscsv/Csvfile_results', 'results_' . time() . '.' . $file->getClientOriginalExtension());

                $data = array_map('str_getcsv', file($file->getRealPath()));

                $successfullyInserted = 0; // Counter for successful rows
                $errors = []; // Collect errors for problematic rows
                $newStatus = true;
                $imageRecordId=0;
                foreach ($data as $index => $row) {
                    $rowErrors = [];
                    if ($index === 0) {
                        // Skip the header row
                        continue;
                    }

                    // Ensure the row has the correct number of columns
                    if (count($row) < 28) {
                        $errors[] = "Row " . ($index + 1) . " has incomplete data.";
                        continue;
                    }

                    // Find the matching archive image
                    $archiveimage = Archiveimage::where('archive_id', $row[0])
                        ->where('book_pagenumber', $row[1])
                        ->first();

                    // Find the matching archive image
                    $archive = Archive::where('id', $row[0])
                        ->first();

                    $archiveRole = ArchiveRole::where('archive_id', $row[0])->first();



                    if (!$archiveimage) {
                        $errors[] = "Row " . ($index + 1) . ": No matching Archiveimage found.";
                        continue;
                    }

                    try {
                        // Update archive image status
                        if ($row[2] == 0) {
                            $archiveimage->status_id = 2;

                            $archiveimage->save();

                            continue;
                        } else {
                            if ($imageRecordId!=$archiveimage->id) {
                                $newStatus = false;
                                $imageRecordId=$archiveimage->id;
                                $archiveimage->total_students = 1;
                            } else {
                                $archiveimage->total_students += 1;
                            }

                            // Update statuses
                            $archiveimage->status_id = 4;
                            $archive->status_id = 4;
                            $archiveRole->status_id = 4;
//                            $archiveRole->qc_status_id = 3;

                        }
                        $archiveRole->save();
                        $archiveimage->save();
                        $archive->save();




                        // Create the Archivedata record
                        $archivedata = Archivedata::create([
                            'archive_id' => $row[0],
                            'archiveimage_id' => $archiveimage->id,
                            'column_number' => $row[2],
                            'university_id' => $row[3],
                            'faculty_id' => $row[4],
                            'department_id' => $row[5],
                            'grade_id' => $row[6],
                            'semester_type_id' => $row[7],
                            'name' => $row[8],
                            'last_name' => $row[9],
                            'father_name' => $row[10],
                            'grandfather_name' => $row[11],
                            'school' => $row[12],
                            'school_graduation_year' => $row[13],
                            'tazkira_number' => $row[14],
                            'birth_date' => $row[15],
                            'birth_place' => $row[16],
                            'time' => $row[17],
                            'kankor_id' => $row[18],
                            'year_of_inclusion' => $row[19],
                            'graduated_year' => $row[20],
                            'transfer_year' => $row[21],
                            'leave_year' => $row[22],
                            'failled_year' => $row[23],
                            'monograph_date' => $row[24],
                            'monograph_number' => $row[25],
                            'monograph_title' => $row[26],
                            'averageOfScores' => $row[27],
                            'status_id' => 4,
                            'qc_status_id' => 1,
                            'description' => 'File Uploaded By System',
                        ]);

                        // Log the upload
                        \DB::table('uploaded_csv_logs')->insert([
                            'archivedata_id' => $archivedata->id,
                            'batch_id' => $batchId,
                            'created_at' => now(),
                        ]);

                        $successfullyInserted++;
                    } catch (\Exception $e) {
                        $errors[] = "Row " . ($index + 1) . ": " . " این فایل مشکل دارد";
                        continue;
                    }
                }

                // Prepare success and error messages
                $message = "به تعداد " . $successfullyInserted . " محصل آپلود شد.";
                if (!empty($errors)) {
                    $message .= " خطا: " . implode('; ', $errors);
                }
                Session::flash('message', $message);

                return redirect()->route('archive.view');
            } else {
                return redirect()->back()->withErrors(['csv_file' => trans('general.invalid_file')]);
            }
        } catch (\Exception $e) {
            \Log::error('CSV Upload Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back();
        }
    }

    public function undoLastUpload()
    {
        try {
            // Get the latest batch_id
            $lastBatch = \DB::table('uploaded_csv_logs')->latest('created_at')->value('batch_id');

            if (!$lastBatch) {
                return redirect()->back()->withErrors(['message' => trans('اپلود معلومات پیدا نشد')]);
            }

            \DB::beginTransaction();
            // Fetch student IDs associated with the batch
            $studentIds = \DB::table('uploaded_csv_logs')
                ->where('batch_id', $lastBatch)
                ->select('archivedata_id')
                ->get();

                for($i=0;$i<count($studentIds);$i++){


                // Get the first Archivedata record based on the student IDs
                $data = Archivedata::where('id', $studentIds[$i]->archivedata_id)->first();




                if ($data) {
                    // Update Archiveimage record
                    $archiveimage = Archiveimage::find($data->archiveimage_id);

                    if ($archiveimage) {
                        $archiveimage->update([
                            'total_students' => 0,
                            'status_id' => 1,
                        ]);
                    } else {
                        Log::warning('Archiveimage not found for ID: ' . $data->archiveimage_id);
                    }

                    // Update Archive record
                    $archive = Archive::find($data->archive_id);




                    if ($archive) {
                        $archive->update([
                            'status_id' => 1,
                        ]);
                    } else {
                        Log::warning('Archive not found for ID: ' . $data->archive_id);
                    }
                } else {
                    Log::warning('No Archivedata found for the provided student IDs.');
                }


                    if (in_array($archive->qc_status_id, [2, 3, 4])) {
                        session()->flash('error', 'این رکورد قابل لغو نیست زیرا قبلاً به کنترل کیفیت معرفی شده است.');
                        return back();
                    }

                // Delete the Archivedata records and log entries
                $deletedCount = Archivedata::where('id', $studentIds[$i]->archivedata_id)->forceDelete();
            }



            \DB::table('uploaded_csv_logs')->where('batch_id', $lastBatch)->delete();

            \DB::commit();

            // Flash success message
            $message = trans('  محصلین این کتاب حذف شد.', ['count' => $deletedCount]);
            Session::flash('message', $message);

            return redirect()->route('archive.view');
        } catch (\Exception $e) {
            \DB::rollback();
            Log::error('Error undoing upload: ' . $e->getMessage());

            return redirect()->back()->withErrors([
                'message' => trans('general.upload_undo_failed'),
            ]);
        }
    }




}

