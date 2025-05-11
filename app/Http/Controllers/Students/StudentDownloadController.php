<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use PDF;

class StudentDownloadController extends Controller
{
    
    // public function index($student)
    // {    
    //     $files = [];
    //     if (file_exists( resource_path ("views/pdf/students/downloads") )) {
    //         $files = File::allFiles( resource_path ("views/pdf/students/downloads"));
    //     }       
    // 	return view('students.downloads.index', compact('student', 'files'));
    // }

    
    public function show($student, $fileName)
    {                        
        //  dd($student, $fileName);
     

        $pdf = PDF::loadView('pdf.students.downloads.'.$fileName, compact('student'), [], [            
            'title' => implode(explode('-', $fileName), ' ')
        ]);

        return $pdf->stream('document.pdf');
    }
}
