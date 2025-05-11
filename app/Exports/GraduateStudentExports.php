<?php
namespace App\Exports;

use App\Models\Student;
use App\Models\GraduatedStudent;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class GraduateStudentExports implements FromView 
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $students = GraduatedStudent::select( 
            'graduated_students.graduated_year',
            'graduated_students.manual_graduated',
            'graduated_students.description',
            'students.id',
            'students.form_no',
            'students.name',
            'students.last_name',
            'students.father_name',
            'students.grandfather_name',
            'students.email',
            'students.phone',
            'students.gender',
            'students.kankor_result',
            'provinces.name as province',
            'students.kankor_year',
            'students.kankor_score',
            'students.school_name',
            'students.school_graduation_year',
            'departments.name as department',
            'universities.name as university',
            'grades.name as grade',
            'student_statuses.title as status'
            )
            ->leftJoin('students', 'graduated_students.student_id', '=', 'students.id')
            ->leftJoin('provinces', 'students.province', '=', 'provinces.id')
            ->leftJoin('student_statuses', 'students.status_id', '=', 'student_statuses.id')
            ->leftJoin('grades', 'students.grade_id', '=', 'grades.id')
            ->leftJoin('departments', 'students.department_id', '=', 'departments.id')
            ->leftJoin('universities', 'students.university_id', '=', 'universities.id')
            ->orderBy('students.name');
            

        if (request()->university != null) {
            $students->where('graduated_students.university_id', '=',  request()->university);
        }

        if (request()->department != null) {
            $students->where('graduated_students.department_id', '=',  request()->department);
        }
       

        if (request()->kankor_year != null) {
            $students->where('graduated_students.graduated_year', '=',  request()->kankor_year);
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

        // dd($students);

        return view('reports.students.graduated-students', [
            'students' => $students
        ]);
    }
}
