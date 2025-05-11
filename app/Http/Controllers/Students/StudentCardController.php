<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use PDF;

class StudentCardController extends Controller
{

 
  public function __construct()
  {
    ini_set("pcre.backtrack_limit", "10000000");       
    $this->middleware('permission:print-studentCard', ['only' => ['index', 'show']]);        
    $this->middleware('permission:create-cardandDiplomaimage', ['only' => ['create','store']]);
    $this->middleware('permission:update-student-cardandDiplomaphoto', ['only' => ['edit','update', 'updateStatus']]);
      //  $this->middleware('permission:delete-student', ['only' => ['destroy']]);
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */




    public function index($student)
    {
       // return view('students.card', compact('student'));

       if($student->department === null){
          $message = " لطفا دیپارتمنت"  . " "  . $student->name . " " . "رامشخص نماید";
          return redirect()->back()->with('message', $message);
        }
        $pdf = PDF::loadView('students.card', compact('student'), [], [
          'format' => [86, 54]
        ]);

        return $pdf->stream($student->form_no.'.pdf');
    }

    public function show_card($student)
    {
       if($student->department === null){
          $message = " لطفا دیپارتمنت"  . " "  . $student->name . " " . "رامشخص نماید";
          return redirect()->back()->with('message', $message);
        }

        return view('students.card1', compact('student'));
    }

    // public function printAllStudentsForm (){

    //     $universities = University::pluck('name', 'id');
    //     return view ('students.students-card.index', [
    //         'title' => trans('general.students-card'),
    //         'description' => trans('general.print-students_card'),
    //         'universities' => $universities,
    //     ]);
    // }

    // public function printAllStudentsCard(Request $request){
    //     //get the department modek
    //     $department = Department::find($request->department_id);
        
    //     //get the all students of the requested year of the department
    //     $students = $department->students->where('kankor_year' , $request->year);

    //     $pdf = PDF::loadView('students.students-card.card', compact('students'), [], [
    //         'format' => [86, 54]
    //       ]);

    //     return $pdf->stream($department->name.'.pdf');

    // }
}
