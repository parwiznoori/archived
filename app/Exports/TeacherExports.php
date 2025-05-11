<?php

namespace App\Exports;

use App\Models\Teacher;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class TeacherExports implements FromView
{
    
    public function view(): View
    {

        $teachers = Teacher::select( 
            'teachers.id',
            'teachers.name',
            'teachers.last_name',
            'teachers.father_name',
            'teachers.grandfather_name',
            'teachers.email',
            'teachers.phone',
            'teachers.birthdate',
            'teachers.marital_status',
            'teachers.gender',
            'provinces.name as province',
            'teacher_academic_ranks.title as teacher_acadaemic_rank',
            'departments.name as department',
            'universities.name as university',
            'teachers.degree',
            'teachers.type',
            'teachers.active'
        )
        ->leftJoin('provinces', 'teachers.province', '=', 'provinces.id')
        ->leftJoin('teacher_academic_ranks', 'teachers.academic_rank_id', '=', 'teacher_academic_ranks.id')
        ->leftJoin('departments', 'teachers.department_id', '=', 'departments.id')
        ->leftJoin('universities', 'teachers.university_id', '=', 'universities.id')
        ->orderBy('id', 'desc');
            

        if (request()->university != '') {
            $teachers->where('teachers.university_id', '=',  request()->university);
        }

        if (request()->department != '') {
            $teachers->where('teachers.department_id', '=',  request()->department);
        }
         

        if (request()->province != '') {
            $teachers->where('teachers.province', '=', request()->province);
        }

        if (request()->academic_rank_id != '') {
            $teachers->where('teachers.academic_rank_id', '=', request()->academic_rank_id);
        }

        if (request()->marital_status != '') {
            $teachers->where('teachers.marital_status', '=', request()->marital_status);
        }

        if (request()->degree != '') {
            $teachers->where('teachers.degree', '=', request()->degree);
        }

        if (request()->gender != '') {
            $teachers->where('teachers.gender', '=', request()->gender);
        }

        if (request()->type != '') {
            $teachers->where('teachers.type', '=', request()->type);
        }

        if (request()->active != '') {
            $teachers->where('teachers.active', '=', request()->active);
        }

        $teachers = $teachers->get();

        return view('reports.teachers.create', [
            'teachers' => $teachers
        ]);
    }
}
