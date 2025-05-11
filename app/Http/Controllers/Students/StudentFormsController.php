<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class StudentFormsController extends Controller
{
    
    public function index ($student){

        if($student->department === null){
            $message = " لطفا دیپارتمنت"  . " "  . $student->name . " " . "رامشخص نماید";
            return redirect()->back()->with('message', $message);
        }

        $files = [];

        if (file_exists( resource_path ("views/pdf/students/downloads") )) {
            $files = File::allFiles( resource_path ("views/pdf/students/downloads"));
        }   
       // dd($files);

    	return view('students.student-forms', compact('student', 'files'));
    }

    public function generateForm(Request $request , $student, $model = null){
        if(auth('user')->check() or auth('student')->check() ){

            if($student->department === null){
                $message = " لطفا دیپارتمنت"  . " "  . $student->name . " " . "رامشخص نماید";
                return redirect()->back()->with('message', $message);
            }
            // $student =  $student->graduatedStudents();
            $student = Student::with('graduatedStudents')->where( 'id',$student->id)->first();
          
            // dd($student);
            $student->download($student , $request->file , $request, $model);
        }
    }
}
