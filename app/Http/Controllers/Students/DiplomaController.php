<?php

namespace App\Http\Controllers\Students;
use App\Http\Controllers\Controller;
use App\Models\GraduatedStudent;
use App\Models\Scores;
use App\Models\Student;
use PDF;

class DiplomaController extends Controller
{
    public function __construct()
    {        
        $this->middleware('permission:print-diploma', ['only' => ['index']]);        
       
    }
    /**
     * print pdf diploma for graduated student
     *
     * @return pdf file
     */
    public function index($id)
    {
        $graduatedStudent = GraduatedStudent::findOrFail($id);
        $student= Student::findOrFail($graduatedStudent->student_id);

        $pdf = PDF::loadView('students.diploma-en', compact('student'), [], [
            'format' => 'A4-L'
          ]);

        return $pdf->stream('Diploma-'.$student->form_no.'.pdf');
    }
}
