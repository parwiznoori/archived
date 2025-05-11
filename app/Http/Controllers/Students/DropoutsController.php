<?php

namespace App\Http\Controllers\Students;

use App\DataTables\DropoutsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Dropout;
use App\Models\Student;
use App\Models\SystemVariable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use PDF;

class DropoutsController extends Controller
{
    protected $MIN_YEAR_KANKOR;
    protected $MAX_YEAR_KANKOR;
    protected $MIN_SEMESTER;
    protected $MAX_SEMESTER;

    public function __construct()
    {        
        $this->middleware('permission:view-dropout', ['only' => ['index', 'show']]);        
        $this->middleware('permission:create-dropout', ['only' => ['create','store']]);
        $this->middleware('permission:delete-dropout', ['only' => ['destroy']]);
        $this->middleware('permission:removal-dropout', ['only' => ['removal','removal_store']]);
        $this->middleware('permission:approve-dropout', ['only' => ['approved']]);

        $system_variables = SystemVariable::select('name','default_value','user_value')->get();

        $this->MIN_YEAR_KANKOR=$system_variables->where('name','MIN_YEAR_KANKOR')->first();
        $this->MAX_YEAR_KANKOR=$system_variables->where('name','MAX_YEAR_KANKOR')->first();
        $this->MIN_SEMESTER=$system_variables->where('name','MIN_SEMESTER')->first();
        $this->MAX_SEMESTER=$system_variables->where('name','MAX_SEMESTER')->first();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DropoutsDataTable $dataTable)
    {        
        return $dataTable->render('dropouts.index', [
            'title' => trans('general.dropouts'),
            'description' => trans('general.dropouts_list')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $date1 = Date('Y-m-d');
        $jalali_date=explode('-',$date1);
        $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);
        $currentYear = $jDate[0];

        return view('dropouts.create', [
            'title' => trans('general.dropouts'),
            'description' => trans('general.new_dropouts'),
            'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR->user_value ,
            'MAX_YEAR_KANKOR' => $this->MAX_YEAR_KANKOR->user_value ,
            'MIN_SEMESTER' => $this->MIN_SEMESTER->user_value ,
            'MAX_SEMESTER' => $this->MAX_SEMESTER->user_value ,
            'currentYear' => $currentYear,
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
            'year' => 'required',
            'student_id' =>  [
                'required',
                Rule::unique('dropouts')
                ->where('student_id', $request->student_id)
                ->whereNull('deleted_at'),
            ],
        ]);
        
        $student = Student::find($request->student_id);
        if($student->department == null){
            return redirect()->back()->with('message', trans('general.student_must_specify_department', ['student' => $student->name]));
        }

        //THIS IS NOT APPLICABLE FOR DROPOUTS
        if($student->status->id != 2){
            return redirect()->back()->with('message',  trans('general.student_must_has_enrolled_status', ['student' => $student->name]));
        }
        
        \DB::transaction(function () use ($request, $student){
            
            $dropout = Dropout::create([
                'student_id' => $request->student_id,
                'year' => $request->year,
                'semester' => $request->semester,
                'note' => $request->note,
                'university_id' => $student->university_id,
                'permanent' => $request->input('permanent') ? true : false, 
            ]);

            //$dropout->download($student , 'درخواست-منفکی', $request, $dropout);
            
        });

        return redirect(route('dropouts.index'));
    }

     /**
     * Display the specified resource.
     *
    
     * @return \Illuminate\Http\Response
     */
    public function show($dropout)
    {
        $student=Student::find($dropout->student_id);
        $fileName="dropout-request";
        // dd($dropout);
      //return view('pdf.students.downloads.dropout-request', compact('student','dropout'));

        $pdf = PDF::loadView('pdf.students.forms.dropout-request', compact('student','dropout'), [], [            
            'title' => $fileName
        ]);

        return $pdf->stream('dropout-request.pdf');

 
    }

    public function edit($dropout)
   {
        if (( $dropout->approved ==1 ) and (! auth()->user()->hasRole('super-admin'))) {
            abort(403);
        }
        // dd($dropout);
        $student=Student::where('id',$dropout->student_id)->first();
       
        return view('dropouts.edit', [
            'title' => trans('general.dropouts'),
            'description' => trans('general.edit'),
            'dropout' => $dropout,
            'student' => $student,
            'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR->user_value ,
            'MAX_YEAR_KANKOR' => $this->MAX_YEAR_KANKOR->user_value ,
            'MIN_SEMESTER' => $this->MIN_SEMESTER->user_value ,
            'MAX_SEMESTER' => $this->MAX_SEMESTER->user_value ,
        ]);

   }

   
   public function update(Request $request, $dropout)
   {
       
        $validatedData = $request->validate([            
            'year' =>  [
                'required',
                Rule::unique('dropouts')->where('student_id', $dropout->student_id)->where('dropouts.year', $request->year)->whereNull('deleted_at')->ignore($dropout->id),
            ],
        ]);

        $student = Student::find($dropout->student_id);
       
        $dropout->update([
                'year' => $request->year, //to requested department 
                'semester' => $request->semester,
                'note' => $request->note,
                'permanent' => $request->input('permanent') ? true : false, 
        ]);         
        

        return redirect(route('dropouts.index'));

   }

    public function approved($dropout)
    {
        if( $dropout->approved == false )
        {
             $dropout->update([
                 'approved' => true
             ]);

             $dropout->student->update([
                'status_id' => 3,
                'group_id' => null,
            ]);
        }
        return redirect(route('dropouts.index'));
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($dropout)
    {
        \DB::transaction(function () use ($dropout){
            $dropout->student->update([
                'status_id' => 1
            ]);
            $dropout->delete();
        });

        return redirect(route('dropouts.index'));
    }

    public function removal($dropout)
    {
        $student=Student::where('id',$dropout->student_id)->first();
       
        return view('dropouts.removal', [
            'title' => trans('general.dropouts'),
            'description' => trans('general.removal_dropout'),
            'dropout' => $dropout,
            'student' => $student,
            'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR->user_value ,
            'MAX_YEAR_KANKOR' => $this->MAX_YEAR_KANKOR->user_value ,
            'MIN_SEMESTER' => $this->MIN_SEMESTER->user_value ,
            'MAX_SEMESTER' => $this->MAX_SEMESTER->user_value ,
        ]);
    }

    public function removal_store(Request $request, $dropout)
    {
        $form_no = $dropout->student->form_no;
        $validatedData = $request->validate([            
            'removal_dropout_description' =>  [
                'required',
            ],
        ]);

        if( $dropout->approved == true )
        {
            $student=Student::where('id',$dropout->student_id)->first();
            \DB::transaction(function () use ($dropout,$request,$student,$form_no){

                $student->update([
                    'status_id' => 2
                ]);

                $dropout->update([
                    'removal_dropout' => 1,
                    'removal_dropout_description' => $request->removal_dropout_description 
                ]);  
            
                Session::flash('message', trans('general.this_student_succseefully_removal_dropouted',[ 'form_no' => $form_no ]));
            });
        }
        return redirect(route('dropouts.index'));
    }
}
