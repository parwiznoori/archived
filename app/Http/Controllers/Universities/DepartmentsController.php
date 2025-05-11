<?php

namespace App\Http\Controllers\Universities;

use App\DataTables\DepartmentsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SystemVariable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;


class DepartmentsController extends Controller
{
    protected $MIN_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR;
    protected $MAX_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR;
    protected $MAX_SEMESTER;
    protected $MIN_SEMESTER;
    public function __construct()
    {        
        $this->middleware('permission:view-department', ['only' => ['index', 'show']]);        
        $this->middleware('permission:create-department', ['only' => ['create','store']]);
        $this->middleware('permission:edit-department', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-department', ['only' => ['destroy']]);

        $this->MIN_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR=SystemVariable::where('name','MIN_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR')->first()->user_value;
        $this->MAX_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR=SystemVariable::where('name','MAX_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR')->first()->user_value;
        $this->MAX_SEMESTER=SystemVariable::where('name','MAX_SEMESTER')->first()->user_value;
        $this->MIN_SEMESTER=SystemVariable::where('name','MIN_SEMESTER')->first()->user_value;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DepartmentsDataTable $dataTable, $university)
    {        
        return $dataTable->render('departments.index', [
            'title' => $university->name,
            'description' => trans('general.departments_list'),
            'university' => $university
        ]);
    }

    public function recover($university, $id)
    {
        // dd($university, $id);
       
        $department=Department::where('id',$id)->withTrashed()->first();
       
       
        if(isset($department->deleted_at))
        {
            $department->restore();
           
        }
       
        return redirect(route('departments.index', $university));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($university)
    {
        $faculties=Faculty::where('university_id',$university->id)->pluck('name', 'id');
        $departmentTypes=DepartmentType::pluck('name', 'id');
       
       
        return view('departments.create', [
            'title' => $university->name,
            'description' => trans('general.create_department'),
            'university' => $university,
            'grades' => Grade::pluck('name', 'id'),
            'faculties' => $faculties,
            'departmentTypes' => $departmentTypes,
            'MIN_SEMESTER' => $this->MIN_SEMESTER,
            'MAX_SEMESTER' => $this->MAX_SEMESTER,
            'MIN_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR' => $this->MIN_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR,
            'MAX_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR' => $this->MAX_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR
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
        $validatedData = $request->validate([            
            'name' => 'required',
            'chairman' => '',
            'department_student_affairs' => '',
            'faculty' => '',
            'grade_id' => 'required',
            'department_eng'=>'',
            'abbreviation_eng'=>'',
            'number_of_semesters'=>'required',
            'min_credits_for_graduated'=>'required',
            'faculty_eng'=>'',
            'department_type_id' => 'required',
            'faculty_id' => 'required'
        ]);
        
        $university->departments()->create($validatedData);

        return redirect(route('departments.index', $university));
    }

    protected function has_data($university,$department)
    {
        $studentsNumbersInDepartment=Student::where('department_id',$department->id)->count();
        $groupsNumbersInDepartment=Group::where('department_id',$department->id)->count();
        $coursesNumbersInDepartment=Course::where('department_id',$department->id)->count();
        $subjectsNumbersInDepartment=Subject::where('department_id',$department->id)->count();

        if($studentsNumbersInDepartment || $groupsNumbersInDepartment || $coursesNumbersInDepartment || $subjectsNumbersInDepartment)
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
    public function show($university,$department)
    {
        
        $studentsNumbersInDepartment=Student::where('department_id',$department->id)->count();
        $groupsNumbersInDepartment=Group::where('department_id',$department->id)->count();
        $coursesNumbersInDepartment=Course::where('department_id',$department->id)->count();
        $subjectsNumbersInDepartment=Subject::where('department_id',$department->id)->count();

        //  dd($university,$studentsNumbersInDepartment);
        $university_name=$department->university_id ? $department->university->name : '';
        $faculty_name=$department->faculty_id ? $department->facultyName->name : '';
        return view('departments.show', [
            'title' => $university_name.'-'.$faculty_name.'-'.$department->name,
            'description' => trans('general.show'),
            'university' => $university,
            'department' => $department,
            'studentsNumbersInDepartment' => $studentsNumbersInDepartment,
            'groupsNumbersInDepartment' => $groupsNumbersInDepartment,
            'coursesNumbersInDepartment' => $coursesNumbersInDepartment,
            'subjectsNumbersInDepartment' => $subjectsNumbersInDepartment,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($university, $department)
    {
        $faculties=Faculty::where('university_id',$university->id)->pluck('name', 'id');
        $departmentTypes=DepartmentType::pluck('name', 'id');

        return view('departments.edit', [
            'title' => $university->name,
            'description' => trans('general.edit_department'),
            'university' => $university,
            'department' => $department,
            'grades' => Grade::pluck('name', 'id'),
            'faculties' => $faculties,
            'departmentTypes' => $departmentTypes,
            'MIN_SEMESTER' => $this->MIN_SEMESTER,
            'MAX_SEMESTER' => $this->MAX_SEMESTER,
            'MIN_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR' => $this->MIN_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR,
            'MAX_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR' => $this->MAX_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR
            
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $university, $department)
    {          
        $validatedData = $request->validate([
            'name' => 'required',
            'chairman' => '',
            'department_student_affairs' => '',
            'faculty' => '',
            'grade_id' => 'required',
            'department_type'=>'',
            'department_eng'=>'',
            'abbreviation_eng'=>'',
            'number_of_semesters'=>'required',
            'min_credits_for_graduated'=>'required',
            'faculty_eng'=>'',
            'department_type_id' => 'required',
            'faculty_id' => 'required'

        ]);
        
        $department->update($validatedData);        

        return redirect(route('departments.index', $university));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($university, $department)
    {
        $hasData = $this->has_data($university,$department);
        if($hasData == 1)
        {
            Session::flash('message', trans('general.this_department_has_data_and_can_not_be_deleted'));
            return redirect(route('departments.index', $university));
        }
        else{
            $department->delete();

            return redirect(route('departments.index', $university));
        }
       
    
        
    }
}
