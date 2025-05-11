<?php

namespace App\Http\Controllers\Students\AuthStudent;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Score;
use App\Models\Student;
use App\Models\StudentStatus;
use App\Models\University;

class allDetailController extends Controller
{

    public function index($student)
    {



        $scores = Score::where('scores.student_id', $student->id)
            ->with('course')
            ->with('subject')
            ->orderBy('semester')
            ->get();


        $student = Student::find(auth('student')->user()->id);
        return view(
            'students.auth_students.allDetail',
            [
                'title' => trans('general.students'),
                'description' => trans('general.all_details_of_student'),
                'student' => $student,
                'universities' => University::pluck('name', 'id'),
                'statuses' => StudentStatus::whereIn('id', [1, 2])->pluck('title', 'id'),

                'grades' => Grade::pluck('name', 'id'),
                'scores' => $scores,
            ]
        );

    }


    public function group($student)
    {
        $scores = Score::where('scores.student_id', $student->id)
        ->with('course')
        ->with('subject')
        ->orderBy('semester')
        ->get();


        $student = Student::find(auth('student')->user()->id);
        return view(
        'students.auth_students.groupandclass',
        [
            'title' => trans('general.students'),
            'description' => trans('general.groupandclass'),
            'student' => $student,
            'universities' => University::pluck('name', 'id'),
            'statuses' => StudentStatus::whereIn('id', [1, 2])->pluck('title', 'id'),

            'grades' => Grade::pluck('name', 'id'),
            'scores' => $scores,
        ]
    );
        }



        public function leave($student)
        {
            $scores = Score::where('scores.student_id', $student->id)
            ->with('course')
            ->with('subject')
            ->orderBy('semester')
            ->get();
    
    
        $student = Student::find(auth('student')->user()->id);
        return view(
            'students.auth_students.leave',
            [
                'title' => trans('general.students'),
                'description' => trans('general.leave'),
                'student' => $student,
                'universities' => University::pluck('name', 'id'),
                'statuses' => StudentStatus::whereIn('id', [1, 2])->pluck('title', 'id'),
    
                'grades' => Grade::pluck('name', 'id'),
                'scores' => $scores,
            ]
        );
            }

}