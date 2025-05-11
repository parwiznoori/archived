<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Province;
use App\Models\Student;
use App\Models\University;
use Illuminate\Http\Request;

class HomeController extends Controller
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
    public function index($kankorYear = null)
    {        
        $kankorYear = $kankorYear ?? \App\Models\Student::max('kankor_year');
        $university_id= auth()->user()->university_id;
        
        $kankorYears = Student::select('kankor_year')->distinct()->where('kankor_year','>',0)->orderBy('kankor_year', 'desc')->get();
        // dd($kankorYears,$kankorYear);
        $studentsByStatus = Department::with(['studentsByStatus' => function ($students) use ($kankorYear){
            $students->where('kankor_year' , $kankorYear);
        }])->get(); 
        // echo "<pre>";
        // print_r($studentsByStatus);
        // exit;
        $universityName = '';
        $allFaculties = [];
        $nomberOfFaculties = 0;
        $facultyName = '';

        if( auth()->user()->allUniversities() ) {                        
            $allUniversities = University::get();
            $universitesList =  University::pluck('name', 'id');
            $allFaculties = Faculty::get();
            $nomberOfFaculties = count($allFaculties);
        }
        else 
        {            
            $allUniversities = University::where('id', auth()->user()->university_id)->get();
            $universitesList =  University::where('id',$university_id)->pluck('name', 'id');
            $universityName = $allUniversities->first()->name;
            $nomberOfFaculties = Department::distinct('faculty_id')->count();
            if($nomberOfFaculties == 1)
            {
               $faculty_id = Department::distinct('faculty_id')->first();
               $faculty = Faculty::where('id',$faculty_id->faculty_id)->first();
               $facultyName =  $faculty->name;
            }
        }

        //this query returns some basic states such as total universities, departments, students count by status
        $allProvinces = Province::get();
        $allDepartments = Department::get();
        $allFaculties = Faculty::get();
       
        

        $allStudents = Student::where('kankor_year', $kankorYear)->count();


        $totalStudentsByStatus = Student::select(\DB::raw('count(students.id) as students_count'),'status_id')
            ->where('kankor_year', $kankorYear)
            ->groupBy('status_id')
            ->get();        

        $provinces = Student::select('provinces.name as province', \DB::raw('count(students.id) as count'))
        ->leftJoin('provinces', 'provinces.id', '=', 'students.province')
        ->groupBy('provinces.name')
        ->where('kankor_year', $kankorYear)
        ->withoutGlobalScopes()
        ->get();
        
        $universities = Student::leftJoin('universities', 'universities.id', '=', 'university_id')
            ->select('universities.name', \DB::raw('count(students.id) as count'))
            ->where('kankor_year', $kankorYear)
            ->groupBy('universities.name')
            ->with('university')
            ->withoutGlobalScopes()
            ->get();

        // to take the first city for province data manipulation
        $city = $allProvinces->first();

        // to take the first university for university data manipulation
        $university = $allUniversities->first();

        // this query is used to fetch students of a specific city in all the universities
        $provinceStudentsInUnis = Student::leftJoin('universities', 'universities.id', '=', 'university_id')
            ->select('universities.name', \DB::raw('count(students.id) as std_count'))
            ->where('province', $city->id)
            ->where('kankor_year', $kankorYear)
            ->orderBy('std_count', 'asc')
            ->groupBy('universities.name')
            ->withoutGlobalScopes()
            ->get();


        //  This query is used to fetch data of a specific university grouped by provinces
        $uniStudentsFromProvinces = Student::leftJoin('provinces', 'provinces.id', '=', 'province')
            ->select('provinces.name', \DB::raw('count(students.id) as std_count'))
            ->where('university_id', $university->id)
            ->where('kankor_year', $kankorYear)
            ->orderBy('std_count', 'asc')
            ->groupBy('provinces.name')
            ->withoutGlobalScopes()
            ->get(); 

        $statuses = \DB::table('student_statuses')->orderBy('id', 'desc')->get();

   
        return view('home', [
            'title' => trans('general.dashboard'),
            'statuses' => $statuses,
            'city' => $city->name,
            'provinces' => $provinces,
            'uniName' => $university->name,
            'universityName' => $universityName,
            'universities' => $universities,
            'studentsByStatus' => $studentsByStatus,
            'uniSpecStudents' => $provinceStudentsInUnis,
            'proSpecStudents' => $uniStudentsFromProvinces,
            'allUniversities' => $allUniversities,
            'universitesList' => $universitesList,
            'allDepartments' => $allDepartments,
            'departments' => Department::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
            'allFaculties' => $allFaculties,
            'nomberOfFaculties' => $nomberOfFaculties,
            'facultyName' => $facultyName,
            'allProvinces' => $allProvinces,
            'allStudents' => $allStudents,
            'studentsByStatusCount' => $totalStudentsByStatus,
            'kankorYears' => $kankorYears,
            'current_kankor_year' => $kankorYear
        ]);
    }


    public function updateData(Request $request) {
        // Check if the request is made to fetch province specific data
        if($request->pro) {
            // dd($request->all());

        // this query is used to fetch students of a specific city in all the universities

            $provinceStudentsInUnis = Student::leftJoin('universities', 'universities.id', '=', 'university_id')
                ->select('universities.name', \DB::raw('count(students.id) as std_count'))
                ->where('province', $request->pro)
                ->where('kankor_year', $request->year)
                ->orderBy('std_count', 'asc')
                ->groupBy('universities.name')
                ->withoutGlobalScopes()
                ->get();

            $meta = Province::select('name')->where('id',$request->pro)->get();
        

            return response()->json(array('specData'=> $provinceStudentsInUnis, 'meta' => $meta), 200);

        }

        // Check if the request is made to fetch university specific data
        if($request->uni) {

                //  This query is used to fetch data of a specific university grouped by provinces
                $uniStudentsFromProvinces = Student::leftJoin('provinces', 'provinces.id', '=', 'province')
                    ->select('provinces.name', \DB::raw('count(students.id) as std_count'))
                    ->where('university_id', $request->uni)
                    ->where('kankor_year', $request->year)
                    ->orderBy('std_count', 'asc')
                    ->groupBy('provinces.name')
                    ->withoutGlobalScopes()
                    ->get();

                $meta = University::select('name')->where('id',$request->uni)->get();
                


                return response()->json(array('specData'=> $uniStudentsFromProvinces, 'meta' => $meta), 200);

        } else {

            return response()->json('Request could not be processed', 404);
        }


    }
}