<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\University;
use Illuminate\Http\Request;
use PDF;

class AttendanceController extends Controller
{
    public function __construct()
    {        
         $this->middleware('permission:view-student', ['only' => ['index', 'show']]);
    }

    public function index()
    {
        return view('attendance.index', [
            'title' => trans('general.attendance'),
            'description' => trans('general.create_attendance'),
            'universities' => University::pluck('name', 'id')
        ]);
    }

    public function show(Request $request)
    {
        $this->validate($request, [
            'department' => 'required'
        ]);

        $department = Department::find($request->department);        

        $pdf = PDF::loadView('attendance.show', compact('department', 'request'), [], [
            'format' => 'A4-L'
          ]);

        return $pdf->stream('attendance.pdf');
    }
}
