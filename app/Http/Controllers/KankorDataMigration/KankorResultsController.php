<?php

namespace App\Http\Controllers\KankorDataMigration;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Student;
use App\Models\University;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class KankorResultsController extends Controller
{
    public function __construct()
    {        
        $this->middleware('permission:update-department-after-kankor', ['only' => ['show_department', 'store_department']]);         
    }
   

    public function show_university()
    {   
        $universities =  University::pluck('name', 'id');
        $kankorYear = Student::max('kankor_year');
        $kankorResults = Student::distinct('kankor_result')
        ->where('kankor_year',$kankorYear)
        ->whereNull('university_id')
        ->pluck('kankor_result','kankor_result');

        return view('kankor_results.show_university', [
            'title' => trans('general.kankorResults'),
            'description' => trans('general.update_university_id'),
            'kankorResults' => $kankorResults,
            'kankorYear' => $kankorYear,
            'kankor_years' =>  Student::select('kankor_year')->distinct()->orderBy('kankor_year', 'desc')->pluck('kankor_year','kankor_year'),
            'universities' => $universities,
            'describtions' => '',
            'universitySelected' => null,
        ]);
    }


    public function store_university(Request $request)
    { 
        $kankor_year = $request->kankor_year;
        // $kankorResults = $request->kankorResults;
        $university_id = $request->university_id;

        $kankor_results_array = array();
        $i=0;
        foreach($request->kankorResults as $Kankor_result)
        {
            $kankor_results_array[$i] = $Kankor_result;
            $i++;
        }
        // $kankorResults = implode(',', $kankor_results_array);
        // dd($kankor_results_array);


        $students = Student::where('kankor_year',$kankor_year)
        ->whereIn('kankor_result',$kankor_results_array)
        ->whereNull('university_id')
        ->update(['university_id' => $university_id]);

        $counter = $students;
        $describtions = $message = " به تعداد ".$counter."محصل آپدیت شد.";
       
        $universities =  University::pluck('name', 'id');
        $kankorYear = Student::max('kankor_year');
        $kankorResults = Student::distinct('kankor_result')
        ->where('kankor_year',$kankorYear)
        ->whereNull('university_id')
        ->pluck('kankor_result','kankor_result');

        Session::flash('message', $message);
        return redirect(route('kankor_results.show_university'))->withInput();;
        
    }

    public function show_department()
    {   
        
        $kankorYear = Student::max('kankor_year');
        $kankorResults = Student::distinct('kankor_result')
        ->where('kankor_year',$kankorYear)
        ->whereNull('department_id')
        ->pluck('kankor_result','kankor_result');

        return view('kankor_results.show_department', [
            'title' => trans('general.kankorResults'),
            'description' => trans('general.update_department_id'),
            'kankorResults' => $kankorResults,
            'kankorYear' => $kankorYear,
            'universities' => University::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name',
                'id') : [],
            'describtions' => ''
        ]);
    }

    public function store_department(Request $request)
    { 
        $kankor_year = $request->kankor_year;
        // $kankorResults = $request->kankorResults;
        $university_id = $request->university_id;
        $department_id = $request->department_id;

        \DB::transaction(function () use ($request, $kankor_year,$university_id,$department_id) {            
            $students = Student::where('kankor_year',$kankor_year)
            ->where('kankor_result',$request->kankorResults)
            ->where('university_id',$university_id)
            ->update(['department_id' => $department_id]);
            $counter = $students;
            $describtions = $message = " به تعداد ".$counter."محصل آپدیت شد.";
        
            
            Session::flash('message', $message);
            
        });

        return redirect(route('kankor_results.show_department'))->withInput();
        
    }

    public function show_university_by_kankor_results()
    {   
        $universities =  University::pluck('name', 'id');
        $kankorYear = Student::max('kankor_year');
        $kankorResults = Student::distinct('kankor_result')
        ->where('kankor_year',$kankorYear)
        ->pluck('kankor_result','kankor_result');

        return view('kankor_results.show_university_by_kankor_results', [
            'title' => trans('general.kankorResults'),
            'description' => trans('general.update_university_id_by_kankor_results'),
            'kankorResults' => $kankorResults,
            'kankorYear' => $kankorYear,
            'kankor_years' =>  Student::select('kankor_year')->distinct()->orderBy('kankor_year', 'desc')->pluck('kankor_year','kankor_year'),
            'universities' => $universities,
            'describtions' => '',
            'universitySelected' => null,
        ]);
    }

    public function store_university_by_kankor_results(Request $request)
    { 
        $kankor_year = $request->kankor_year;
        $university_id = $request->university_id;

        $kankor_results_array = array();
        $i=0;
        foreach($request->kankorResults as $Kankor_result)
        {
            $kankor_results_array[$i] = $Kankor_result;
            $i++;
        }
        // $kankorResults = implode(',', $kankor_results_array);
        // dd($kankor_results_array);

        \DB::transaction(function () use ($kankor_year,$university_id,$kankor_results_array) {            
           
            $students = Student::where('kankor_year',$kankor_year)
            ->whereIn('kankor_result',$kankor_results_array)
            ->update(['university_id' => $university_id]);
    
            $counter = $students;
            $describtions = $message = " به تعداد ".$counter."محصل آپدیت شد.";
    
            Session::flash('message', $message);
            
        });

      
        return redirect(route('kankor_results.show_university_by_kankor_results'))->withInput();;
        
    }

}
