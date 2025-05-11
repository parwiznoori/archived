<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    // public function index($student,$year,$semester,$isPassed){

    //     dd($student,$year,$semester,$isPassed);
    // }

    public function index(){
        
        // dd(getGrade(86));
        
        return view('test', [
            'title' => trans('general.students'),
            'description' => trans('general.create_student'),
            // 'educationalAndLearningGoals' => $educationalAndLearningGoals
        ]);
    }

    public function store(Request $request){

        // dd($request->all());
        // $request->validate([
        //     'title' => 'required',
        //     'course_id' => 'required',
        //     'teacher_id' => 'required',
        // ]);

        // $course = CoursePolicy::create([
        //     'title' => $request->title,
        //     'course_id' => $course_id,
        //     'teacher_id' => $teacher_id,
        //     'scoring_and_evaluation_methods' => $request->scoring_and_evaluation_methods,
        //     'educational_and_learning_goals' => $request->educational_and_learning_goals,
        //     'teaching_methods' => $request->teaching_methods,
        //     'class_rules' => $request->class_rules,
        // ]);
        


    }
    public function php_info()
    {
        phpinfo();
    }
}
