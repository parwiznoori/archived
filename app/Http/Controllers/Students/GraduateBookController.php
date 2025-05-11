<?php

namespace App\Http\Controllers\Students;

use App\DataTables\GraduateBookDataTable;
use App\Http\Controllers\Controller;
use App\Jobs\CreateGraduateBookPdf;
use App\Models\Department;
use App\Models\Grade;
use App\Models\GraduateBooksPdf;
use App\Models\SystemVariable;
use App\Models\University;
use App\Services\GraduatesBookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use McPDF;

class GraduateBookController extends Controller
{
    protected $graduatesBookService;

    public function __construct(GraduatesBookService $graduatesBookService)
    {
        $this->graduatesBookService = $graduatesBookService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GraduateBookDataTable $dataTable)
    {        
        return $dataTable->render('graduate-book.index', [
            'title' => trans('general.graduates-book'),
            'description' => trans('general.list')            
        ]);
    }

    public function show($id)
    {
        $graduateBooksPdf = GraduateBooksPdf::find($id);
        return view('graduate-book.show', [
            'title' => trans('general.graduate_book_description'),
            'graduateBooksPdf' => $graduateBooksPdf
        ]);
    }

    public function createForm()
    {
        $MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first();
        $MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first();
        $MIN_SEMESTER=SystemVariable::where('name','MIN_SEMESTER')->first();
        $MAX_SEMESTER=SystemVariable::where('name','MAX_SEMESTER')->first();
        $date=explode(' ',jdate()); //current date and time
        $currentDate=explode('-',$date[0]); //current date 
        $files = [];

        if (file_exists( resource_path ("views/graduate-book/pdf") )) {
            $files = File::allFiles( resource_path ("views/graduate-book/pdf"));
        }   

        return view('graduate-book.create', [
            'title' => trans('general.graduates-book'),
            'description' => trans('general.create'),
            'universities' => University::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name',
                'id') : [],
            'MIN_YEAR_KANKOR' => $MIN_YEAR_KANKOR->user_value ,
            'MAX_YEAR_KANKOR' => $MAX_YEAR_KANKOR->user_value ,
            'MIN_SEMESTER' => $MIN_SEMESTER->user_value ,
            'MAX_SEMESTER' => $MAX_SEMESTER->user_value ,
            'currentDate' => $currentDate,
            'files' => $files,
            'grades' => Grade::pluck('name', 'id'),
        ]);
    }

    public function pdf(Request $request)
    {
        $user_id = auth()->user()->id;
        $university = University::find($request->university);
        $department = Department::find($request->department);
        $year = $request->graduate_year;
        $grade_id = $request->grade_id;

        CreateGraduateBookPdf::dispatch($university,$department,$year,$grade_id,$this->graduatesBookService,$user_id);
        return redirect(route('graduate-book.index'))->with('message', trans('general.generate_of_graduate_book_pdf_is_running_in_background_and_took_some_times_please_wait_and_refresh_this_page_after_some_few_minutes_later'));;
    }

    /*
    download zip file
    */
    public function download(Request $request,$id)
    {
        $graduateBooksPdf = GraduateBooksPdf::find($id);
        $path = $graduateBooksPdf->fileName;
        return response()->download($path);
    }
}
