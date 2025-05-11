<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\SystemVariable;
use App\Models\Group;
use App\Models\Course;
use App\Models\Score;
use App\Models\Department;
use App\Models\Dropout;
use App\Models\Leave;
use App\Models\Monograph;
use App\Models\Student;
use App\Models\University;
use App\Models\Subject;
use App\Models\Transfer;
use App\Models\StudentSemesterScore;
use App\Models\StudentResult;
use DB;


class StudentsQueryService
{
    public function get_total_students(Request $request,$groupBy = null) {

        $students = Student::leftJoin('universities', 'universities.id', '=', 'students.university_id');

        if ($groupBy == 'departments') 
        {
            $students->leftJoin('departments', 'departments.id', '=', 'students.department_id')
            ->select('universities.id','universities.name','students.department_id','departments.name  as department_name', \DB::raw('count(students.id) as count_students'))
            ->whereNull('departments.deleted_at')
            ;
        }
        else
        {
            $students = $students->select('universities.id','universities.name', \DB::raw('count(students.id) as count_students'));
        }
        $students = $students->where('students.status_id', '>', 0 )
        ->whereNull('universities.deleted_at')
        
        ;

        if (request()->university != null) {
            $students->where('students.university_id', '=',  request()->university);
        }
        
        if (request()->kankor_year != null) {
            $students->where('students.kankor_year', '=',  request()->kankor_year);
        }  

        if (request()->department != null) {
            $students->where('students.department_id', '=',  request()->department);
        }
    
        if (request()->status != null) {
            $students->where('students.status_id', '=', request()->status);
        }     

        if (request()->province != null) {
            $students->where('students.province', '=', request()->province);
        }

        if (request()->grade != null) {
            $students->where('students.grade_id', '=', request()->grade);
        }
        
        if (request()->gender != null) {
            $students->where('students.gender', '=', request()->gender);
        }

        $students->groupBy('students.university_id');

        if ($groupBy == 'departments') {
            $students->groupBy('students.department_id');
        }
        $students->orderBy('universities.name');
        if ($groupBy == 'departments') 
        {
            $students->orderBy('departments.name');
        }
        

        $students = $students->get();

        return $students;
    }

    public function get_total_students_with_leave(Request $request,$groupBy = null) {

        $students = Leave::leftJoin('students', 'leaves.student_id', '=', 'students.id')
        ->leftJoin('universities', 'universities.id', '=', 'leaves.university_id');
        if ($groupBy == 'departments') {
            $students->leftJoin('departments', 'departments.id', '=', 'leaves.department_id')
            ->select('universities.id','universities.name','leaves.department_id','departments.name  as department_name', \DB::raw('count(leaves.student_id) as count_students'))
            ->whereNull('students.deleted_at')
            ->whereNull('departments.deleted_at');
        }
        else
        {
            $students = $students->select('universities.id','universities.name', \DB::raw('count(leaves.student_id) as count_students'));
        }
        $students = $students->where('students.status_id', '>', 0 )
        ->whereNull('universities.deleted_at')
        ->where('leaves.approved', '=' , 1)
        ->withoutGlobalScopes()
        ;

        if (request()->university != null) {
            $students->where('leaves.university_id', '=',  request()->university);
        }
        
        if (request()->kankor_year != null) {
            $students->where('leaves.leave_year', '=',  request()->kankor_year);
        }  

        if (request()->department != null) {
            $students->where('leaves.department_id', '=',  request()->department);
        }
    
        if (request()->status != null) {
            $students->where('students.status_id', '=', request()->status);
        }     

        if (request()->province != null) {
            $students->where('students.province', '=', request()->province);
        }

        if (request()->grade != null) {
            $students->where('students.grade_id', '=', request()->grade);
        }
        
        if (request()->gender != null) {
            $students->where('students.gender', '=', request()->gender);
        }

        $students->groupBy('leaves.university_id');
        if ($groupBy == 'departments') {
            $students->groupBy('leaves.department_id');
        }
        $students->orderBy('universities.name');
        if ($groupBy == 'departments') 
        {
            $students->orderBy('departments.name');
        }
        $students = $students->get();
        
        return $students;
    }

    public function get_total_students_with_dropouts(Request $request,$groupBy = null) {

        // $students = Dropout::select('universities.id','universities.name', \DB::raw('count(dropouts.student_id) as count_students'))
        // ->leftJoin('students', 'dropouts.student_id', '=', 'students.id')
        // ->leftJoin('universities', 'universities.id', '=', 'dropouts.university_id')
        // ->where('students.status_id', '>' , 0)
        // ->where('dropouts.approved', '=' , 1)
        // ->withoutGlobalScopes();

        $students = Dropout::leftJoin('students', 'dropouts.student_id', '=', 'students.id')
        ->leftJoin('universities', 'universities.id', '=', 'dropouts.university_id');

        if ($groupBy == 'departments') {
            $students->leftJoin('departments', 'departments.id', '=', 'students.department_id')
            ->select('universities.id','universities.name','students.department_id','departments.name  as department_name', \DB::raw('count(dropouts.student_id) as count_students'))
            ->whereNull('students.deleted_at')
            ->whereNull('departments.deleted_at');
        }
        else
        {
            $students = $students->select('universities.id','universities.name', \DB::raw('count(dropouts.student_id) as count_students'));
        }
        $students = $students->where('students.status_id', '=' , 3)
        ->whereNull('universities.deleted_at')
        ->where('dropouts.approved', '=' , 1)
        ->withoutGlobalScopes()
        ;

        if (request()->university != null) {
            $students->where('dropouts.university_id', '=',  request()->university);
        }
        
        if (request()->kankor_year != null) {
            $students->where('dropouts.year', '=',  request()->kankor_year);
        }  

        if (request()->department != null) {
            $students->where('students.department_id', '=',  request()->department);
        }
    
        if (request()->status != null) {
            $students->where('students.status_id', '=', request()->status);
        }     

        if (request()->province != null) {
            $students->where('students.province', '=', request()->province);
        }

        if (request()->grade != null) {
            $students->where('students.grade_id', '=', request()->grade);
        }
        
        if (request()->gender != null) {
            $students->where('students.gender', '=', request()->gender);
        }

        $students->groupBy('dropouts.university_id');
        if ($groupBy == 'departments') {
            $students->groupBy('students.department_id');
        }
        $students->orderBy('universities.name');
        if ($groupBy == 'departments') 
        {
            $students->orderBy('departments.name');
        }
        $students = $students->get();
        
        return $students;
    }

    public function get_total_students_with_transfers_from(Request $request,$groupBy = null) {

        // $students = Transfer::select('universities.id','universities.name', \DB::raw('count(transfers.student_id) as count_students'))
        // ->leftJoin('students', 'transfers.student_id', '=', 'students.id')
        // ->leftJoin('departments', 'transfers.from_department_id', '=', 'departments.id')
        // ->leftJoin('universities', 'departments.university_id', '=', 'universities.id')
        // ->where('students.status_id', '>' , 0)
        // ->where('transfers.approved', '=' , 1)
        // ->withoutGlobalScopes();

        $students = Transfer::leftJoin('students', 'transfers.student_id', '=', 'students.id')
        ->leftJoin('departments', 'transfers.from_department_id', '=', 'departments.id')
        ->leftJoin('universities', 'departments.university_id', '=', 'universities.id');

        if ($groupBy == 'departments') {
            $students->select('universities.id','universities.name','transfers.from_department_id as department_id','departments.name  as department_name', \DB::raw('count(transfers.student_id) as count_students'))
            ->whereNull('students.deleted_at')
            ;
        }
        else
        {
            $students = $students->select('universities.id','universities.name', \DB::raw('count(transfers.student_id) as count_students'));
        }
        $students = $students->where('students.status_id', '>' , 0)
        ->whereNull('universities.deleted_at')
        ->whereNull('departments.deleted_at')
        ->where('transfers.approved', '=' , 1)
        ->withoutGlobalScopes()
        ;

        if (request()->university != null) {
            $students->where('departments.university_id', '=',  request()->university);
        }
        
        if (request()->kankor_year != null) {
            $students->where('transfers.education_year', '=',  request()->kankor_year);
        }  

        if (request()->department != null) {
            $students->where('transfers.from_department_id', '=',  request()->department);
        }
    
        if (request()->status != null) {
            $students->where('students.status_id', '=', request()->status);
        }     

        if (request()->province != null) {
            $students->where('students.province', '=', request()->province);
        }

        if (request()->grade != null) {
            $students->where('students.grade_id', '=', request()->grade);
        }
        
        if (request()->gender != null) {
            $students->where('students.gender', '=', request()->gender);
        }

        $students->groupBy('departments.university_id');
        if ($groupBy == 'departments') {
            $students->groupBy('transfers.from_department_id');
        }
        $students->orderBy('universities.name');
        $students->orderBy('departments.name');
        $students = $students->get();

        
        return $students;
    }

    public function get_total_students_with_transfers_to(Request $request,$groupBy = null) 
    {

        // $students = Transfer::select('universities.id','universities.name', \DB::raw('count(transfers.student_id) as count_students'))
        // ->leftJoin('students', 'transfers.student_id', '=', 'students.id')
        // ->leftJoin('departments', 'transfers.to_department_id', '=', 'departments.id')
        // ->leftJoin('universities', 'departments.university_id', '=', 'universities.id')
        // ->where('students.status_id', '>' , 0)
        // ->where('transfers.approved', '=' , 1)
        // ->withoutGlobalScopes();

        $students = Transfer::leftJoin('students', 'transfers.student_id', '=', 'students.id')
        ->leftJoin('departments', 'transfers.to_department_id', '=', 'departments.id')
        ->leftJoin('universities', 'departments.university_id', '=', 'universities.id');

        if ($groupBy == 'departments') {
            $students->select('universities.id','universities.name','transfers.to_department_id as department_id','departments.name as department_name', \DB::raw('count(transfers.student_id) as count_students'))
            ->whereNull('students.deleted_at');
        }
        else
        {
            $students = $students->select('universities.id','universities.name', \DB::raw('count(transfers.student_id) as count_students'));
        }
        $students = $students->where('students.status_id', '>' , 0)
        ->whereNull('universities.deleted_at')
        ->whereNull('departments.deleted_at')
        ->where('transfers.approved', '=' , 1)
        ->withoutGlobalScopes()
        ;

        if (request()->university != null) {
            $students->where('departments.university_id', '=',  request()->university);
        }
        
        if (request()->kankor_year != null) {
            $students->where('transfers.education_year', '=',  request()->kankor_year);
        }  

        if (request()->department != null) {
            $students->where('transfers.to_department_id', '=',  request()->department);
        }
    
        if (request()->status != null) {
            $students->where('students.status_id', '=', request()->status);
        }     

        if (request()->province != null) {
            $students->where('students.province', '=', request()->province);
        }

        if (request()->grade != null) {
            $students->where('students.grade_id', '=', request()->grade);
        }
        
        if (request()->gender != null) {
            $students->where('students.gender', '=', request()->gender);
        }

        $students->groupBy('departments.university_id');
        if ($groupBy == 'departments') {
            $students->groupBy('transfers.to_department_id');
        }
        $students->orderBy('universities.name');
        $students->orderBy('departments.name');
        $students = $students->get();

        return $students;
    }

    public function get_total_students_by_province(Request $request) 
    {

        $students = Student::leftJoin('provinces', 'provinces.id', '=', 'province')
        ->select('provinces.name', \DB::raw('count(students.id) as count_students'))
        ->orderBy('count_students', 'desc')
        ->groupBy('provinces.name')
        ->withoutGlobalScopes()
        ; 

        if (request()->university != null) {
            $students->where('students.university_id', '=',  request()->university);
        }
        
        if (request()->kankor_year != null) {
            $students->where('students.kankor_year', '=',  request()->kankor_year);
        }  

        if (request()->department != null) {
            $students->where('students.department_id', '=',  request()->department);
        }
    
        if (request()->status != null) {
            $students->where('students.status_id', '=', request()->status);
        }     

        if (request()->province != null) {
            $students->where('students.province', '=', request()->province);
        }

        if (request()->grade != null) {
            $students->where('students.grade_id', '=', request()->grade);
        }
        
        if (request()->gender != null) {
            $students->where('students.gender', '=', request()->gender);
        }

        $students = $students->get();
        
        return $students;
    }

    public function get_total_students_by_university(Request $request) 
    {

        // this query is used to fetch students of a specific city in all the universities
        $students = Student::leftJoin('universities', 'universities.id', '=', 'university_id')
            ->select('universities.name', \DB::raw('count(students.id) as count_students'))
            ->orderBy('count_students', 'desc')
            ->groupBy('universities.name')
            ->withoutGlobalScopes()
            ;

        if (request()->university != null) {
            $students->where('students.university_id', '=',  request()->university);
        }
        
        if (request()->kankor_year != null) {
            $students->where('students.kankor_year', '=',  request()->kankor_year);
        }  

        if (request()->department != null) {
            $students->where('students.department_id', '=',  request()->department);
        }
    
        if (request()->status != null) {
            $students->where('students.status_id', '=', request()->status);
        }     

        if (request()->province != null) {
            $students->where('students.province', '=', request()->province);
        }

        if (request()->grade != null) {
            $students->where('students.grade_id', '=', request()->grade);
        }
        
        if (request()->gender != null) {
            $students->where('students.gender', '=', request()->gender);
        }

        $students = $students->get();
        
        return $students;
    }

    public function get_total_students_by_department(Request $request) 
    {

        // this query is used to fetch students of a specific city in all the departments
        $students = Student::leftJoin('departments', 'departments.id', '=', 'department_id')
            ->select('departments.name', \DB::raw('count(students.id) as count_students'))
            ->orderBy('count_students', 'desc')
            ->groupBy('departments.name')
            ->withoutGlobalScopes()
            ;

        if (request()->university != null) {
            $students->where('students.university_id', '=',  request()->university);
        }
        
        if (request()->kankor_year != null) {
            $students->where('students.kankor_year', '=',  request()->kankor_year);
        }  

        if (request()->department != null) {
            $students->where('students.department_id', '=',  request()->department);
        }
    
        if (request()->status != null) {
            $students->where('students.status_id', '=', request()->status);
        }     

        if (request()->province != null) {
            $students->where('students.province', '=', request()->province);
        }

        if (request()->grade != null) {
            $students->where('students.grade_id', '=', request()->grade);
        }
        
        if (request()->gender != null) {
            $students->where('students.gender', '=', request()->gender);
        }

        $students = $students->get();
        
        return $students;
    }

    public function get_total_students_by_gender(Request $request) 
    {

        // this query is used to fetch students of a specific city in all the departments
        $students = Student::select('gender', \DB::raw('count(students.id) as count_students'))
            ->orderBy('count_students', 'desc')
            ->groupBy('gender')
            ->withoutGlobalScopes()
            ;

        if (request()->university != null) {
            $students->where('students.university_id', '=',  request()->university);
        }
        
        if (request()->kankor_year != null) {
            $students->where('students.kankor_year', '=',  request()->kankor_year);
        }  

        if (request()->department != null) {
            $students->where('students.department_id', '=',  request()->department);
        }
    
        if (request()->status != null) {
            $students->where('students.status_id', '=', request()->status);
        }     

        if (request()->province != null) {
            $students->where('students.province', '=', request()->province);
        }

        if (request()->grade != null) {
            $students->where('students.grade_id', '=', request()->grade);
        }
        
        if (request()->gender != null) {
            $students->where('students.gender', '=', request()->gender);
        }

        $students = $students->get();
        
        return $students;
    }

    public function get_total_students_by_grade(Request $request) 
    {
        $students = Student::leftJoin('grades', 'grades.id', '=', 'grade_id')
            ->select('grades.name', \DB::raw('count(students.id) as count_students'))
            ->orderBy('count_students', 'desc')
            ->groupBy('grades.name')
            ->withoutGlobalScopes()
            ;

        if (request()->university != null) {
            $students->where('students.university_id', '=',  request()->university);
        }
        
        if (request()->kankor_year != null) {
            $students->where('students.kankor_year', '=',  request()->kankor_year);
        }  

        if (request()->department != null) {
            $students->where('students.department_id', '=',  request()->department);
        }
    
        if (request()->status != null) {
            $students->where('students.status_id', '=', request()->status);
        }     

        if (request()->province != null) {
            $students->where('students.province', '=', request()->province);
        }

        if (request()->grade != null) {
            $students->where('students.grade_id', '=', request()->grade);
        }
        
        if (request()->gender != null) {
            $students->where('students.gender', '=', request()->gender);
        }

        $students = $students->get();
        
        return $students;
    }

}