<?php

namespace App\Http\Controllers\Universities;

use App\DataTables\FacultyDataTable;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;

class FacultyController extends Controller
{
    public function __construct()
    {        
         $this->middleware('permission:view-faculty', ['only' => ['index', 'show']]);        
         $this->middleware('permission:create-faculty', ['only' => ['create','store']]);
         $this->middleware('permission:edit-faculty', ['only' => ['edit','update']]);
         $this->middleware('permission:delete-faculty', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FacultyDataTable $dataTable, $university)
    {        
        return $dataTable->render('faculties.index', [
            'title' => $university->name,
            'description' => trans('general.list').' '.trans('models/faculty.plural'),
            'university' => $university
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($university)
    {
        
        // echo "<pre>";
        // print_r($facultiesFromDatabase);

        $faculties=Faculty::all()->count();
        $i=0;
       
        if(!$faculties)
        {
            $facultiesFromDatabase=Department::where('deleted_at',Null)
            ->select('faculty','faculty_eng','university_id','chairman')
            ->distinct()
            ->groupBy('faculty')
            ->groupBy('university_id')
            ->get();

            foreach($facultiesFromDatabase as $faculty)
            {
                if($faculty->faculty)
                {
                   
                    $newFaculty=new Faculty();
                    $newFaculty->university_id=$faculty->university_id;
                    $newFaculty->name=$faculty->faculty;
                    $newFaculty->name_en=$faculty->faculty_eng;
                    $newFaculty->chairman=$faculty->chairman;
                    $newFaculty->save();
                   // echo "$i++- faculty ".$faculty->faculty." is inserted to faculty table <br>";

                }
               
            }
        }
     
        
        return view('faculties.create', [
            'title' => $university->name,
            'description' => trans('crud.add_new').' '.trans('models/faculty.singular'),
            'university' => $university
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $university)
    {
        $university_id=$university->id;
        $name=$request->name;
        $validatedData = $request->validate([            
            'name' => [
                'required',
                 Rule::unique('faculties')->where(function ($query) use($university_id,$name) {
                    return 
                    $query->where('university_id', $university_id);
                 })
            ],
            'chairman' => '',
            'name_en' => ''
        ]);
        
        $university->faculties()->create($validatedData);

        return redirect(route('faculties.index', $university));
    }

    protected function has_data($university,$faculty_id)
    {
        $studentsNumbersInDepartment=Department::where('faculty_id',$faculty_id)->count();

        if($studentsNumbersInDepartment >= 1 )
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($faculty)
    {
        exit();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($university, $faculties)
    {
        $faculty = Faculty::find($faculties);

        return view('faculties.edit', [
            'title' => $university->name,
            'description' => trans('crud.edit').' '.trans('models/faculty.singular'),
            'university' => $university,
            'faculty' => $faculty
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $university, $faculty_id)
    {   $university_id=$university->id;
        $name=$request->name;        
        $validatedData = $request->validate([
            'name' => [
                'required',
                 Rule::unique('faculties')->where(function ($query) use($university_id,$name,$faculty_id) {
                    return $query->where('university_id', $university_id);
                 })->ignore($faculty_id)
            ],
            'chairman' => '',
            'name_en' => ''
        ]);

        $faculty = Faculty::find($faculty_id);

        if (empty($faculty)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('faculties.index'));
        }

        
        $faculty->update($validatedData);        

        return redirect(route('faculties.index', $university));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($university, $faculty_id)
    {
        $hasData = $this->has_data($university,$faculty_id);
        if($hasData == 1)
        {
            Session::flash('message', trans('general.this_faculty_has_data_and_can_not_be_deleted'));
            return redirect(route('faculties.index', $university));
        }
        else{
            $faculty = Faculty::find($faculty_id);
            $faculty->delete();
            return redirect(route('faculties.index', $university));
        }
        
    }

     /*
    return list of corses by subject
    */
    public function departments_list($university, $faculty)
    {
        $faculty = Faculty::find($faculty);
        $departmensListByFaculty=array();
        $i=0;
        $departmensList=Department::select('departments.name as name','departments.department_eng as name_eng','departments.abbreviation_eng as abbreviation_eng','departments.number_of_semesters as number_of_semesters',
        'departments.id', 'grades.name as grade_name', 'departments.chairman','departments.department_type_id','department_types.name as department_type_name', 'departments.department_student_affairs','min_credits_for_graduated')
        ->leftJoin('grades', 'grades.id', '=', 'grade_id')
        ->leftJoin('department_types', 'department_types.id', '=', 'departments.department_type_id')
        ->where('departments.faculty_id',$faculty->id)
        ->orderBy('departments.name')
        ->get();

        foreach($departmensList as $department)
        {
            $departmensListByFaculty[$i]['id']=$department->id;
            $departmensListByFaculty[$i]['name']=$department->name;
            $departmensListByFaculty[$i]['name_eng']=$department->name_eng;
            $departmensListByFaculty[$i]['abbreviation_eng']=$department->abbreviation_eng;
            $departmensListByFaculty[$i]['department_type_name']=$department->department_type_name;
            $departmensListByFaculty[$i]['grade_name']=$department->grade_name;
            $departmensListByFaculty[$i]['number_of_semesters']=$department->number_of_semesters;
            $departmensListByFaculty[$i]['min_credits_for_graduated']=$department->min_credits_for_graduated;
            $i++;
        }
       
        
        return view('faculties.department-list', [
            'title' => $university->name." - ".$faculty->name,
            'description' => trans('general.departments_list'),
            'departmensListByFaculty' => $departmensListByFaculty,
            'faculty' => $faculty,
            'university' => $university
        ]);

    }
}
