<?php
namespace App\Exports;

use App\Models\Student;
use App\Models\Transfer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;

class TransferStudentExports implements FromView 
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $students = Transfer::select( 
            'transfers.from_department_id',
            'transfers.to_department_id',
            'transfers.education_year',
            'transfers.semester',
            'transfers.note',
            'transfers.approved',
            'transfers.exception',
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
            DB::raw('CONCAT(from_department.name, "-", from_university.name) as from_department'),
            DB::raw('CONCAT(to_department.name, "-", to_university.name) as to_department'),
            'grades.name as grade',
            'student_statuses.title as status'
            )
            ->join('students', 'students.id', '=', 'student_id')
            ->join('departments as from_department', 'from_department.id', '=', 'from_department_id')
            ->leftJoin('universities as from_university', 'from_university.id', '=', 'from_department.university_id')

            ->join('departments as to_department', 'to_department.id', '=', 'to_department_id')        
            ->leftJoin('universities as to_university', 'to_university.id', '=', 'to_department.university_id')

            ->leftJoin('provinces', 'students.province', '=', 'provinces.id')
            ->leftJoin('student_statuses', 'students.status_id', '=', 'student_statuses.id')
            ->leftJoin('grades', 'students.grade_id', '=', 'grades.id')
            ->orderBy('students.name');
         
           
        if (request()->department != null) {
            $students->where('transfers.to_department_id', '=',  request()->department) 
            ->orWhere('transfers.from_department_id', '=',  request()->department);
        }

        if (request()->university != null) {
            $students->where('to_department.university_id', '=',  request()->university)
            ->orWhere('from_department.university_id', '=',  request()->university);
        }

        if (request()->kankor_year != null) {
            $students->where('transfers.education_year', '=',  request()->kankor_year);
        }  
       

        // if (request()->status != null) {
        //     $students->where('students.status_id', '=', request()->status);
        // }     

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

        //  dd($students);

        return view('reports.students.transfers', [
            'students' => $students
        ]);
    }
}
