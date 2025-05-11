<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Score;
use Illuminate\Http\Request;

class ScoresController extends Controller
{
    public function __invoke(Request $request, $course)
    {               
        if ( (auth('user')->check() and ! auth()->user()->can('edit-course')) OR (auth('teacher')->check() and auth('teacher')->user()->id != $course->teacher_id)) {
            return response()->json([
                'success' => false, 
                'id' => $request->id, 
                'total' => " ",
                'message' => trans('general.you_do_not_have_the_right_permissin_for_doing_this_action')
            ], 400);
        }

        $validator = \Validator::make($request->all(), [ 
            'present' => 'nullable|between:0,99.99|min:0|max:20',
            'absent' => 'nullable|between:0,99.99|min:0|max:20',
            'homework' => 'nullable|between:0,99.99|min:0|max:20|required_without_all:present,absent,classwork,midterm,final,chance_two,chance_three,chance_four',
            'classwork' => 'nullable|between:0,99.99|min:0|max:20',
            'midterm' => 'nullable|between:0,99.99|min:0|max:30',
            'final' => 'nullable|between:0,99.99|min:0|max:100', 
            'chance_two' => 'nullable|between:0,99.99|min:0|max:100', 
            'chance_three' => 'nullable|between:0,99.99|min:0|max:100', 
            'chance_four' => 'nullable|between:0,99.99|min:0|max:100', 
        ]);

        if ($validator->fails()) {
            $errors = "";
            foreach ($validator->errors()->messages() as $message) {
                $errors .= $message[0];
            }
            
            return response()->json([
                'success' => false, 
                'id' => $request->id, 
                'total' => " ",
                'message' => $errors
            ], 400);
        }
        
        $score = Score::find($request->id);
        $data = [
            'student_id' => $request->get('student_id'), 
            'course_id' => $request->get('course_id'),       
            'subject_id' => $request->get('subject_id'),
            'semester' => $request->get('semester'),            
            'present' => $request->get('present'),            
            'absent' => $request->get('absent'),            
            'homework' => $request->get('homework') != '' ? $request->get('homework') : null,
            'classwork' => $request->get('classwork') != '' ? $request->get('classwork') : null,
            'midterm' => $request->get('midterm') != '' ? $request->get('midterm') : null,
            'final' => $request->get('final') != '' ? $request->get('final') : null,
            'chance_two' => ($request->has('chance_two') and $request->get('chance_two') != '') ? $request->get('chance_two') : null,
            'chance_three' => ($request->has('chance_three') and $request->get('chance_three') != '') ? $request->get('chance_three') : null,
            'chance_four' => ($request->has('chance_four') and $request->get('chance_four') != '') ? $request->get('chance_four') : null,
        ];

        $score = \DB::transaction(function () use ($request, $data, $score) {            
            if ($score) {
                if ($data['homework'] == '' and $data['classwork'] == '' and $data['midterm'] == '' and $data['final'] == '' and $data['chance_two'] == '' and $data['chance_three'] == '' and $data['chance_four'] == '') {
                    $score->delete();
                } else {
                    $score->update($data);
                }                                
            } else {
                $score = Score::create($data);                
            } 
        
            return $score;
        });

        return response()->json([
            'success' => true, 
            'id' => ($score and $score->deleted_at == NULL) ? $score->id : NULL,
            'total' => ($score and $score->deleted_at == NULL) ? $score->total : NULL,
            'present' => $score->present,
            'absent' => $score->absent
        ], 200);
    }
    public function store_midtrem(Request $request, $course)
    {
        // echo "$request->student_id --  $request->course_id --$request->subject_id--$request->midterm -- $request->semester";
        // dd($course,$request);

        if ( (auth('user')->check() and ! auth()->user()->can('edit-course')) OR (auth('teacher')->check() and auth('teacher')->user()->id != $course->teacher_id)) {
            return response()->json([
                'success' => false, 
                'id' => $request->id, 
                'total' => " ",
                'message' => trans('general.you_do_not_have_the_right_permissin_for_doing_this_action')
            ], 400);
        }

        $validator = \Validator::make($request->all(), [ 
            'midterm' => 'required|between:0,20.0|min:0|max:20',
            'student_id' => 'required',
            'course_id' => 'required',
            'subject_id' => 'required',
            'semester' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = "";
            foreach ($validator->errors()->messages() as $message) {
                $errors .= $message[0];
            }

            return response()->json([
                'success' => false, 
                'id' => $request->student_id, 
                'total' => " ",
                'message' => $errors
            ], 400);
        }

        $score = Score::where('course_id',$request->course_id)
        ->where('student_id',$request->student_id)->first();
        $data = [
            'student_id' => $request->get('student_id'), 
            'course_id' => $request->get('course_id'),       
            'subject_id' => $request->get('subject_id'),
            'semester' => $request->get('semester'),
            'present' =>   $request->get('present'),             
            'midterm' => $request->get('midterm'),
        ];

        $score = \DB::transaction(function () use ($request, $data, $score) {            
            if ( $score ) {
                $score->update($data);
                                                
            } else {
                $score = Score::create($data);                
            } 
        
            return $score;
        });

        return response()->json([
            'success' => true, 
            'id' => ($score and $score->deleted_at == NULL) ? $score->id : NULL,
            'midterm' => ($score and $score->deleted_at == NULL) ? $score->midterm : NULL,
            
        ], 200);

    }

    public function store_final(Request $request, $course)
    {
        // echo "$request->student_id --  $request->course_id --$request->subject_id--$request->midterm -- $request->semester";
        // dd($course,$request);

        if ( (auth('user')->check() and ! auth()->user()->can('edit-course')) OR (auth('teacher')->check() and auth('teacher')->user()->id != $course->teacher_id)) {
            return response()->json([
                'success' => false, 
                'id' => $request->id, 
                'total' => " ",
                'message' => trans('general.you_do_not_have_the_right_permissin_for_doing_this_action')
            ], 400);
        }

        $validator = \Validator::make($request->all(), [ 
            'student_id' => 'required',
            'course_id' => 'required',
            'subject_id' => 'required',
            'semester' => 'required',
            'present' => 'required',
            'absent' => 'required',
            'homework' => 'required',
            'classwork' => 'required',
            'midterm' => 'required',
            'final' => 'required', 
        ]);

        if ($validator->fails()) {
            $errors = "";
            foreach ($validator->errors()->messages() as $message) {
                $errors .= $message[0];
            }

            return response()->json([
                'success' => false, 
                'id' => $request->student_id, 
                'total' => " ",
                'message' => $errors
            ], 400);
        }

        $score = Score::where('course_id',$request->course_id)
        ->where('student_id',$request->student_id)->first();
        $data = [
            'student_id' => $request->get('student_id'), 
            'course_id' => $request->get('course_id'),       
            'subject_id' => $request->get('subject_id'),
            'semester' => $request->get('semester'), 
            'present' => $request->get('present'),            
            'absent' => $request->get('absent'),            
            'homework' => $request->get('homework'),
            'classwork' => $request->get('classwork'),
            'midterm' => $request->get('midterm') ,
            'final' => $request->get('final'),           
            
        ];

        $score = \DB::transaction(function () use ($request, $data, $score) {            
            if ( $score ) {
                $score->update($data);
                                                
            } else {
                $score = Score::create($data);                
            } 
        
            return $score;
        });

        return response()->json([
            'success' => true, 
            'id' => ($score and $score->deleted_at == NULL) ? $score->id : NULL,
            'final' => ($score and $score->deleted_at == NULL) ? $score->final : NULL,
            
        ], 200);

    }

    public function store_excuse(Request $request, $course)
    {
        if ( (auth('user')->check() and ! auth()->user()->can('edit-course')) OR (auth('teacher')->check() and auth('teacher')->user()->id != $course->teacher_id)) {
            return response()->json([
                'success' => false, 
                'id' => $request->id, 
                'total' => " ",
                'message' => trans('general.you_do_not_have_the_right_permissin_for_doing_this_action')
            ], 400);
        }

        $validator = \Validator::make($request->all(), [ 
            'student_id' => 'required',
            'course_id' => 'required',
            'subject_id' => 'required',
            'semester' => 'required',
            'present' => 'required',
            'absent' => 'required',
            'homework' => 'required',
            'classwork' => 'required',
            'midterm' => 'required',
            'final' => 'required', 
        ]);

        if ($validator->fails()) {
            $errors = "";
            foreach ($validator->errors()->messages() as $message) {
                $errors .= $message[0];
            }

            return response()->json([
                'success' => false, 
                'id' => $request->student_id, 
                'total' => " ",
                'message' => $errors
            ], 400);
        }

        $score = Score::where('course_id',$request->course_id)
        ->where('student_id',$request->student_id)->first();
        $data = [
            'student_id' => $request->get('student_id'), 
            'course_id' => $request->get('course_id'),       
            'subject_id' => $request->get('subject_id'),
            'semester' => $request->get('semester'), 
            'present' => $request->get('present'),            
            'absent' => $request->get('absent'),            
            'homework' => $request->get('homework'),
            'classwork' => $request->get('classwork'),
            'midterm' => $request->get('midterm') ,
            'final' => $request->get('final'),
            'excuse_exam' => 1,           
            
        ];

        $score = \DB::transaction(function () use ($request, $data, $score) {            
            if ( $score ) {
                $score->update($data);
                                                
            } else {
                $score = Score::create($data);                
            } 
        
            return $score;
        });

        return response()->json([
            'success' => true, 
            'id' => ($score and $score->deleted_at == NULL) ? $score->id : NULL,
            'final' => ($score and $score->deleted_at == NULL) ? $score->final : NULL,
            
        ], 200);

    }

    public function store_chance_two(Request $request, $course)
    {
        if ( (auth('user')->check() and ! auth()->user()->can('edit-course')) OR (auth('teacher')->check() and auth('teacher')->user()->id != $course->teacher_id)) {
            return response()->json([
                'success' => false, 
                'id' => $request->id, 
                'total' => " ",
                'message' => trans('general.you_do_not_have_the_right_permissin_for_doing_this_action')
            ], 400);
        }

        $validator = \Validator::make($request->all(), [ 
            'student_id' => 'required',
            'course_id' => 'required',
            'subject_id' => 'required',
            'semester' => 'required',
            'chance_two' => 'required', 
        ]);

        if ($validator->fails()) {
            $errors = "";
            foreach ($validator->errors()->messages() as $message) {
                $errors .= $message[0];
            }

            return response()->json([
                'success' => false, 
                'id' => $request->student_id, 
                'total' => " ",
                'message' => $errors
            ], 400);
        }

        $score = Score::where('course_id',$request->course_id)
        ->where('student_id',$request->student_id)->first();
        $data = [
            'student_id' => $request->get('student_id'), 
            'course_id' => $request->get('course_id'),       
            'subject_id' => $request->get('subject_id'),
            'semester' => $request->get('semester'), 
            'chance_two' => $request->get('chance_two'),           
            
        ];

        $score = \DB::transaction(function () use ($request, $data, $score) {            
            if ( $score ) {
                $score->update($data);
                                                
            } 
        
            return $score;
        });

        return response()->json([
            'success' => true, 
            'id' => ($score and $score->deleted_at == NULL) ? $score->id : NULL,
            'chance_two' => ($score and $score->deleted_at == NULL) ? $score->chance_two : NULL,
            
        ], 200);

    }

    public function store_chance_three(Request $request, $course)
    {
       
        if ( (auth('user')->check() and ! auth()->user()->can('edit-course')) OR (auth('teacher')->check() and auth('teacher')->user()->id != $course->teacher_id)) {
            return response()->json([
                'success' => false, 
                'id' => $request->id, 
                'total' => " ",
                'message' => trans('general.you_do_not_have_the_right_permissin_for_doing_this_action')
            ], 400);
        }

        $validator = \Validator::make($request->all(), [ 
            'student_id' => 'required',
            'course_id' => 'required',
            'subject_id' => 'required',
            'semester' => 'required',
            'chance_three' => 'required', 
        ]);

        if ($validator->fails()) {
            $errors = "";
            foreach ($validator->errors()->messages() as $message) {
                $errors .= $message[0];
            }

            return response()->json([
                'success' => false, 
                'id' => $request->student_id, 
                'total' => " ",
                'message' => $errors
            ], 400);
        }

        $score = Score::where('course_id',$request->course_id)
        ->where('student_id',$request->student_id)->first();
        $data = [
            'student_id' => $request->get('student_id'), 
            'course_id' => $request->get('course_id'),       
            'subject_id' => $request->get('subject_id'),
            'semester' => $request->get('semester'), 
            'chance_three' => $request->get('chance_three'),           
            
        ];

        $score = \DB::transaction(function () use ($request, $data, $score) {            
            if ( $score ) {
                $score->update($data);
                                                
            }
        
            return $score;
        });

        return response()->json([
            'success' => true, 
            'id' => ($score and $score->deleted_at == NULL) ? $score->id : NULL,
            'chance_three' => ($score and $score->deleted_at == NULL) ? $score->chance_three : NULL,
            
        ], 200);

    }

    public function store_chance_four(Request $request, $course)
    {
       
        if ( (auth('user')->check() and ! auth()->user()->can('edit-course')) OR (auth('teacher')->check() and auth('teacher')->user()->id != $course->teacher_id)) {
            return response()->json([
                'success' => false, 
                'id' => $request->id, 
                'total' => " ",
                'message' => trans('general.you_do_not_have_the_right_permissin_for_doing_this_action')
            ], 400);
        }

        $validator = \Validator::make($request->all(), [ 
            'student_id' => 'required',
            'course_id' => 'required',
            'subject_id' => 'required',
            'semester' => 'required',
            'chance_four' => 'required', 
        ]);

        if ($validator->fails()) {
            $errors = "";
            foreach ($validator->errors()->messages() as $message) {
                $errors .= $message[0];
            }

            return response()->json([
                'success' => false, 
                'id' => $request->student_id, 
                'total' => " ",
                'message' => $errors
            ], 400);
        }

        $score = Score::where('course_id',$request->course_id)
        ->where('student_id',$request->student_id)->first();
        $data = [
            'student_id' => $request->get('student_id'), 
            'course_id' => $request->get('course_id'),       
            'subject_id' => $request->get('subject_id'),
            'semester' => $request->get('semester'), 
            'chance_four' => $request->get('chance_four'),           
            
        ];

        $score = \DB::transaction(function () use ($request, $data, $score) {            
            if ( $score ) {
                $score->update($data);
                                                
            }
        
            return $score;
        });

        return response()->json([
            'success' => true, 
            'id' => ($score and $score->deleted_at == NULL) ? $score->id : NULL,
            'chance_four' => ($score and $score->deleted_at == NULL) ? $score->chance_four : NULL,
            
        ], 200);

    }

    public function store_deprived(Request $request, $course)
    {
        // echo "$request->student_id --  $request->course_id --$request->subject_id--$request->midterm -- $request->semester";
        // dd($course,$request);

        if ( (auth('user')->check() and ! auth()->user()->can('edit-course')) OR (auth('teacher')->check() and auth('teacher')->user()->id != $course->teacher_id)) {
            return response()->json([
                'success' => false, 
                'id' => $request->id, 
                'total' => " ",
                'message' => trans('general.you_do_not_have_the_right_permissin_for_doing_this_action')
            ], 400);
        }

        $validator = \Validator::make($request->all(), [ 
            'student_id' => 'required',
            'course_id' => 'required',
            'subject_id' => 'required',
            'semester' => 'required',
            'present' => 'required',
            'absent' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = "";
            foreach ($validator->errors()->messages() as $message) {
                $errors .= $message[0];
            }

            return response()->json([
                'success' => false, 
                'id' => $request->student_id, 
                'total' => " ",
                'message' => $errors
            ], 400);
        }

        $score = Score::where('course_id',$request->course_id)
        ->where('student_id',$request->student_id)->first();
        $data = [
            'student_id' => $request->get('student_id'), 
            'course_id' => $request->get('course_id'),       
            'subject_id' => $request->get('subject_id'),
            'semester' => $request->get('semester'), 
            'present' => $request->get('present'),            
            'absent' => $request->get('absent'),   
            'homework' => null,
            'classwork' => null, 
            'midterm' => null, 
            'final' => null, 
            'total' => null,          
            'deprived' => 1,           
            
        ];

        $score = \DB::transaction(function () use ($request, $data, $score) {            
            if ( $score ) {
                $score->update($data);
                                                
            } else {
                $score = Score::create($data);                
            } 
        
            return $score;
        });

        return response()->json([
            'success' => true, 
            'id' => ($score and $score->deleted_at == NULL) ? $score->id : NULL,
            'final' => ($score and $score->deleted_at == NULL) ? $score->final : NULL,
            
        ], 200);

    }

    public function update_deprived(Request $request, $course)
    {
        if ( (auth('user')->check() and ! auth()->user()->can('edit-course')) OR (auth('teacher')->check() and auth('teacher')->user()->id != $course->teacher_id)) {
            return response()->json([
                'success' => false, 
                'id' => $request->id, 
                'total' => " ",
                'message' => trans('general.you_do_not_have_the_right_permissin_for_doing_this_action')
            ], 400);
        }

        $validator = \Validator::make($request->all(), [ 
            'student_id' => 'required',
            'course_id' => 'required',
            'subject_id' => 'required',
            'semester' => 'required',
            'present' => 'required',
            'absent' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = "";
            foreach ($validator->errors()->messages() as $message) {
                $errors .= $message[0];
            }

            return response()->json([
                'success' => false, 
                'id' => $request->student_id, 
                'total' => " ",
                'message' => $errors
            ], 400);
        }

        $score = Score::where('course_id',$request->course_id)
        ->where('student_id',$request->student_id)->first();
        $data = [
            'student_id' => $request->get('student_id'), 
            'course_id' => $request->get('course_id'),       
            'subject_id' => $request->get('subject_id'),
            'semester' => $request->get('semester'), 
            'present' => $request->get('present'),            
            'absent' => $request->get('absent'),            
            'deprived' => 0,           
            
        ];

        $score = \DB::transaction(function () use ($request, $data, $score) {            
            if ( $score ) {
                $score->update($data);
                                                
            } 
        
            return $score;
        });

        return response()->json([
            'success' => true, 
            'id' => ($score and $score->deleted_at == NULL) ? $score->id : NULL,
            'final' => ($score and $score->deleted_at == NULL) ? $score->final : NULL,
            
        ], 200);

    }

    public function store_absent_exam(Request $request, $course)
    {
        
        if ( (auth('user')->check() and ! auth()->user()->can('edit-course')) OR (auth('teacher')->check() and auth('teacher')->user()->id != $course->teacher_id)) {
            return response()->json([
                'success' => false, 
                'id' => $request->id, 
                'total' => " ",
                'message' => trans('general.you_do_not_have_the_right_permissin_for_doing_this_action')
            ], 400);
        }

        $validator = \Validator::make($request->all(), [ 
            'student_id' => 'required',
            'course_id' => 'required',
            'subject_id' => 'required',
            'semester' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = "";
            foreach ($validator->errors()->messages() as $message) {
                $errors .= $message[0];
            }

            return response()->json([
                'success' => false, 
                'id' => $request->student_id, 
                'total' => " ",
                'message' => $errors
            ], 400);
        }

        $score = Score::where('course_id',$request->course_id)
        ->where('student_id',$request->student_id)->first();
        $data = [
            'student_id' => $request->get('student_id'), 
            'course_id' => $request->get('course_id'),       
            'subject_id' => $request->get('subject_id'),
            'semester' => $request->get('semester'),  
            'present' =>   $request->get('present'), 
            'final' => null, 
            'total' => null,         
            'absent_exam' => 1,           
            
        ];

        $score = \DB::transaction(function () use ($request, $data, $score) {            
            if ( $score ) {
                $score->update($data);
                                                
            } else {
                $score = Score::create($data);                
            } 
        
            return $score;
        });

        return response()->json([
            'success' => true, 
            'id' => ($score and $score->deleted_at == NULL) ? $score->id : NULL,
            'absent_exam' => ($score and $score->deleted_at == NULL) ? $score->absent_exam : NULL,
            
        ], 200);

    }

    public function update_absent_exam(Request $request, $course)
    {
        if ( (auth('user')->check() and ! auth()->user()->can('edit-course')) OR (auth('teacher')->check() and auth('teacher')->user()->id != $course->teacher_id)) {
            return response()->json([
                'success' => false, 
                'id' => $request->id, 
                'total' => " ",
                'message' => trans('general.you_do_not_have_the_right_permissin_for_doing_this_action')
            ], 400);
        }

        $validator = \Validator::make($request->all(), [ 
            'student_id' => 'required',
            'course_id' => 'required',
            'subject_id' => 'required',
            'semester' => 'required',
            
        ]);

        if ($validator->fails()) {
            $errors = "";
            foreach ($validator->errors()->messages() as $message) {
                $errors .= $message[0];
            }

            return response()->json([
                'success' => false, 
                'id' => $request->student_id, 
                'total' => " ",
                'message' => $errors
            ], 400);
        }

        $score = Score::where('course_id',$request->course_id)
        ->where('student_id',$request->student_id)->first();
        $data = [
            'student_id' => $request->get('student_id'), 
            'course_id' => $request->get('course_id'),       
            'subject_id' => $request->get('subject_id'),
            'semester' => $request->get('semester'), 
            'present' =>   $request->get('present'),             
            'absent_exam' => 0,           
            
        ];

        $score = \DB::transaction(function () use ($request, $data, $score) {            
            if ( $score ) {
                $score->update($data);
                                                
            }
        
            return $score;
        });

        return response()->json([
            'success' => true, 
            'id' => ($score and $score->deleted_at == NULL) ? $score->id : NULL,
            'absent_exam' => ($score and $score->deleted_at == NULL) ? $score->absent_exam : NULL,
            
        ], 200);

    }
}