<?php

namespace App\Http\Controllers\Students;

use App\DataTables\LeavesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Student;
use App\Models\SystemVariable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PDF;

class LeavesController extends Controller
{
    protected $MIN_YEAR_KANKOR;
    protected $MAX_YEAR_KANKOR;
    protected $MIN_SEMESTER;
    protected $MAX_SEMESTER;
    public function __construct()
    {        
        $this->middleware('permission:view-leave', ['only' => ['index', 'show']]);        
        $this->middleware('permission:create-leave', ['only' => ['create','store']]);
        $this->middleware('permission:delete-leave', ['only' => ['destroy']]);
        $this->MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first()->user_value;
        $this->MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first()->user_value;
        $this->MIN_SEMESTER=SystemVariable::where('name','MIN_SEMESTER')->first()->user_value;
        $this->MAX_SEMESTER=SystemVariable::where('name','MAX_SEMESTER')->first()->user_value;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LeavesDataTable $dataTable)
    {     
        return $dataTable->render('leaves.index', [
            'title' => trans('general.leaves'),
            'description' => trans('general.leaves_list')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $date=explode(' ',jdate()); //current date and time
        $currentDate=explode('-',$date[0]); //current date 

        return view('leaves.create', [
            'title' => trans('general.leaves'),
            'description' => trans('general.new_leaves'),
            'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR ,
            'MAX_YEAR_KANKOR' => $this->MAX_YEAR_KANKOR ,
            'MIN_SEMESTER' => $this->MIN_SEMESTER ,
            'MAX_SEMESTER' => $this->MAX_SEMESTER ,
            'currentDate' => $currentDate
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([            
            'student_id' => 'required',
            'semester' => 'required',
            'leave_year' =>  [
                'required',
                Rule::unique('leaves')->where('student_id', $request->student_id)->where('leave_year', $request->leave_year)->whereNull('deleted_at'),
            ],
        ]);

        $student = Student::find($request->student_id);
        if($student->department === null){
            return redirect()->back()->with('message', trans('general.student_must_specify_department', ['student' => $student->name]));
        }

        if($student->status->id !== 2){
            return redirect()->back()->with('message',  trans('general.student_must_has_enrolled_status', ['student' => $student->name]));
        }
        
        \DB::transaction(function () use ($request, $student){
            
            $leave = Leave::create([
                'student_id' => $request->student_id,
                'leave_year' => $request->leave_year,
                'semester' => $request->semester,
                'note' => $request->note,
                'university_id' => $student->university_id
            ]);

           // $leave->download($student , 'درخواست-تاجیلی', $request, $leave);            
        });

        return redirect(route('leaves.index'));
    }

    /**
     * Display the specified resource.
     *
    
     * @return \Illuminate\Http\Response
     */
    public function show($leave)
    {
        $student=Student::find($leave->student_id);
        $fileName="leave-request";
        // dd($leave);
      //return view('pdf.students.downloads.leave-request', compact('student','leave'));

        $pdf = PDF::loadView('pdf.students.forms.leave-request', compact('student','leave'), [], [            
            'title' => $fileName
        ]);

        return $pdf->stream('leave-request.pdf');

 
    }

   public function edit($leave)
   {
        if (( $leave->approved ==1 ) and (! auth()->user()->hasRole('super-admin'))) {
            abort(403);
        }
       
        // dd($leave);
        $student=Student::where('id',$leave->student_id)->first();
        return view('leaves.edit', [
            'title' => trans('general.leaves'),
            'description' => trans('general.edit'),
            'leave' => $leave,
            'student' => $student,
            'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR ,
            'MAX_YEAR_KANKOR' => $this->MAX_YEAR_KANKOR ,
            'MIN_SEMESTER' => $this->MIN_SEMESTER ,
            'MAX_SEMESTER' => $this->MAX_SEMESTER 
        ]);

   }
   public function update(Request $request, $leave)
   {
       
        $validatedData = $request->validate([            
            'leave_year' =>  [
                'required',
                Rule::unique('leaves')->where('student_id', $leave->student_id)->where('leave_year', $request->leave_year)->whereNull('deleted_at')->ignore($leave->id),
            ],
        ]);

        $student = Student::find($leave->student_id);
        if($student->department === null){
            return redirect()->back()->with('message', trans('general.student_must_specify_department', ['student' => $student->name]));
        }

        $leave->update([
                'leave_year' => $request->leave_year, //to requested department 
                'semester' => $request->semester,
                'note' => $request->note
        ]);         
        

        return redirect(route('leaves.index'));

   }
   public function approved($leave)
   {

        if( $leave->approved == false )
        {
            $leave->update([
                'approved' => true
            ]);

            $leave->student->update([
                'status_id' => 4,
                'group_id' => null
            ]);
        }

        return redirect(route('leaves.index'));

   }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($leave)
    {
        \DB::transaction(function () use ($leave){
            
            $leave->student->update([
                'status_id' => 2
            ]);

            $leave->delete();

        });

        return redirect(route('leaves.index'));
    }
}