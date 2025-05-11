<?php
namespace App\Http\Controllers\Students;

use App\DataTables\TransfersDataTable;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\GroupStudentHistory;
use App\Models\Student;
use App\Models\SystemVariable;
use App\Models\Transfer;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;
use PDF;

class TransfersController extends Controller
{
    protected $MIN_YEAR_KANKOR;
    protected $MAX_YEAR_KANKOR;
    protected $MIN_SEMESTER;
    protected $MAX_SEMESTER;

    public function __construct()
    {        
        $this->middleware('permission:view-transfer', ['only' => ['index', 'show']]);        
        $this->middleware('permission:create-transfer', ['only' => ['create','store']]);
        $this->middleware('permission:edit-transfer', ['only' => ['edit','update']]);
        $this->middleware('permission:exception-transfer', ['only' => ['create_exception','store_exception']]);
        $this->middleware('permission:delete-transfer', ['only' => ['destroy']]);
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
    public function index(TransfersDataTable $dataTable)
    {        
        return $dataTable->render('transfers.index', [
            'title' => trans('general.transfers'),
            'description' => trans('general.transfers_list')            
        ]);
    }

    public function recover($id)
    {
        $transfer=Transfer::where('id',$id)->withTrashed()->first();
        if(isset($transfer->deleted_at))
        {
            $transfer->restore();
        }
        return redirect(route('transfers.index'));
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function report()
    {
        return view('transfers.report', [
            'title' => trans('general.transfers'),
            'description' => trans('general.report'),
            'universities' => University::pluck('name', 'id')
        ]);
    }

    public function report_search(Request $request)
    {
        $kankorYear=$request->kankor_year;
        if(isset($kankorYear))
        {          
            $transfers=Transfer::join('students', 'students.id', '=', 'student_id')
            ->join('departments as from_department', 'from_department.id', '=', 'from_department_id')
            ->leftJoin('universities as from_university', 'from_university.id', '=', 'from_department.university_id')
            ->join('departments as to_department', 'to_department.id', '=', 'to_department_id')        
            ->leftJoin('universities as to_university', 'to_university.id', '=', 'to_department.university_id')
            ->where('students.kankor_year',$kankorYear)
            ->select(
                'transfers.id',
                'transfers.approved',
                'transfers.education_year',
                'transfers.semester',
                'students.form_no',
                'students.name',
                'transfers.deleted_at',
                'students.kankor_year',
                'students.father_name as father_name',
                \DB::raw('CONCAT(from_department.name, " ", from_university.name) as from_department'),
                \DB::raw('CONCAT(to_department.name, " ", to_university.name) as to_department'),
                'note'
                )
            ->get();
            return view('transfers.report_search', [
                'title' => trans('general.transfers'),
                'description' => trans('general.report'),
                'universities' => University::pluck('name', 'id'),
                'transfers' => $transfers
            ]);
        }
        else
        {
            return view('transfers.report', [
                'title' => trans('general.transfers'),
                'description' => trans('general.report'),
                'universities' => University::pluck('name', 'id')
            ]);
        } 
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
       
        return view('transfers.create', [
            'title' => trans('general.transfers'),
            'description' => trans('general.new_transfer'),
            'universities' => University::pluck('name', 'id'),
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
        // dd($request);    
        // dd($request->department_id);
        $validatedData = $request->validate([            
            'student_id' => [
                'required',
                Rule::unique('transfers')->where('student_id', $request->student_id)->whereNull('deleted_at'),
            ],
            'university_id' => 'required',
            'education_year' => 'required',
            'semester' => 'required',
            'department_id' => 'required|valid_destination_department',   // in AppServiceProvider   
            'note' =>  'required',       
        ]);
        //CHECK WEATHER STUDENT HAS THE DEPARTMENT
        $student = Student::find($request->student_id);
        if($student->department === null){
            return redirect()->back()->with('message', trans('general.student_must_specify_department', ['student' => $student->name]));
        }

        //CHECK WEATHER STUDENT HAS THE ENROLLED STATUS
        if($student->status->id !== 2){
            return redirect()->back()->with('message',  trans('general.student_must_has_enrolled_status', ['student' => $student->name]));
        }

        //CHECK WEATHER STUDENT SHOULD HAVE VALID SCORE FOR TRANSFER
        $department  = $student->department;
        $kankorYear = $student->kankor_year;
        $toDepartment = Department::allUniversities()->where('id', $request->department_id)->first();
        $allowedScore = $department->students->where('kankor_year', $student->kankor_year)->min('kankor_score');

        // CHECK DEPARTMENT_TYPE GENDER AND KANKOR YEAR OF STUDENT FOR A SPECIFIC PUPROSE
        if($toDepartment->department_type_id == 1 and $student->gender === "Male" and $student->kankor_year >= 1398){

            if($student->kankor_score >= $allowedScore) {
                return redirect()->back()->with('message',  trans('general.student_must_have_greater_or_equal_valid_score_for_transfer', ['student' => $student->name]));
            }
        }
        $student = Student::find($request->student_id);
            
        $newTransfer = Transfer::create([
            'student_id' => $request->student_id,
            'education_year' => $request->education_year,
            'semester' => $request->semester,
            'from_department_id' => $student->department_id, //from studetn existing department
            'to_department_id' => $request->department_id, //to requested department  
            'note' => $request->note
        ]);

        return redirect(route('transfers.index'));
    }
    /**
     * Show the form for creating a new exception transfer
     *
     * @return \Illuminate\Http\Response
     */
    public function create_exception()
    {
        $date=explode(' ',jdate()); //current date and time
        $currentDate=explode('-',$date[0]); //current date 

        return view('transfers.create-exception', [
            'title' => trans('general.transfers'),
            'description' => trans('general.new_exception_transfer'),
            'universities' => University::pluck('name', 'id'),
            'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR ,
            'MAX_YEAR_KANKOR' => $this->MAX_YEAR_KANKOR ,
            'MIN_SEMESTER' => $this->MIN_SEMESTER ,
            'MAX_SEMESTER' => $this->MAX_SEMESTER ,
            'currentDate' => $currentDate
        ]);
    }

    /**
     * Store a newly created exception transfer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_exception(Request $request)
    {
        // dd("store exception transfer");
        $validatedData = $request->validate([            
            'student_id' => 'required',
            'university_id' => 'required',
            'department_id' => 'required', 
            'education_year' => 'required',
            'semester' => 'required',  
            'note' =>  'required',         
        ]);

        //CHECK WEATHER STUDENT HAS THE DEPARTMENT
        $student = Student::find($request->student_id);
        if($student->department === null){
            return redirect()->back()->with('message', trans('general.student_must_specify_department', ['student' => $student->name]));
        }

        //CHECK WEATHER STUDENT HAS THE ENROLLED STATUS
        if($student->status->id !== 2){
            return redirect()->back()->with('message',  trans('general.student_must_has_enrolled_status', ['student' => $student->name]));
        }
  
        $student = Student::find($request->student_id);
            
        $newTransfer = Transfer::create([
            'student_id' => $request->student_id,
            'education_year' => $request->education_year,
            'semester' => $request->semester,
            'from_department_id' => $student->department_id, //from studetn existing department
            'to_department_id' => $request->department_id, //to requested department  
            'note' => $request->note,
            'exception' => 1 
        ]);

        return redirect(route('transfers.index'));
    }
    /**
     * Display the specified resource.
     *
    
     * @return \Illuminate\Http\Response
     */
    public function download_pdf($transfer)
    {
        $student=Student::withoutGlobalScopes()->find($transfer->student_id);
        $transferData=array();
        $transferData['semester']=$transfer->semester;

        $transferData['fromUniversity']=$transfer->fromDepartment->university->name;

        $from_faculty_id=$transfer->fromDepartment->faculty_id;
        $fromFaculty=Faculty::withoutGlobalScopes()->where('id',$from_faculty_id)->first();
        $transferData['fromFaculty']=$fromFaculty->name;
       
        $faculty_id=$transfer->toDepartment->faculty_id;
        $toFaculty=Faculty::withoutGlobalScopes()->where('id',$faculty_id)->first();
        $transferData['toFaculty']=$toFaculty->name;

        $transferData['toUniversity']=$transfer->toDepartment->university->name;
       
        if($student)
        {
            $fileName="transfer-request";
            $pdf = PDF::loadView('pdf.students.forms.transfer-request', compact('student','transfer','transferData'), [], [            
                'title' => $fileName
            ]);
    
            return $pdf->stream('transfer-request.pdf');

        }
        else{
            abort(404);
        }
 
    }


    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($transfer)
    {
        $student=Student::withoutGlobalScopes()->find($transfer->student_id);
        $department=Department::withoutGlobalScopes()->where('id',$transfer->to_department_id)->first();
        $university_id=$department->university->id;
        $departments=Department::withoutGlobalScopes()->where('university_id',$university_id)->pluck('name','id');

        return view('transfers.show', [
            'title' => trans('general.transfers'),
            'description' => trans('general.show'),
            'universities' => University::pluck('name', 'id'),
            'student' => $student,
            'transfer' => $transfer,
            'university_id' => $university_id,
            'departments' => $departments,
            'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR ,
            'MAX_YEAR_KANKOR' => $this->MAX_YEAR_KANKOR ,
            'MIN_SEMESTER' => $this->MIN_SEMESTER ,
            'MAX_SEMESTER' => $this->MAX_SEMESTER 
        ]);

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($transfer)
    {
        // dd($student->activities);
        if (( $transfer->approved ==1 ) and (! auth()->user()->hasRole('super-admin'))) {
            abort(403);
        }

        if (! auth()->user()->allUniversities())  
        {
           $from_university_id = auth()->user()->university_id;
           if($transfer->fromDepartment->university->id != $from_university_id)
           {
            abort(403);
           }  
        }

        $student=Student::withoutGlobalScopes()->find($transfer->student_id);
        $department=Department::withoutGlobalScopes()->where('id',$transfer->to_department_id)->first();
        // $student=Student::where('id',$transfer->student_id)->first();
        // $department=Department::where('id',$transfer->to_department_id)->first();
        $university_id=$department->university->id;
        $departments=Department::withoutGlobalScopes()->where('university_id',$university_id)->pluck('name','id');

        return view('transfers.edit', [
            'title' => trans('general.transfers'),
            'description' => trans('general.edit'),
            'universities' => University::pluck('name', 'id'),
            'student' => $student,
            'transfer' => $transfer,
            'university_id' => $university_id,
            'departments' => $departments,
            'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR ,
            'MAX_YEAR_KANKOR' => $this->MAX_YEAR_KANKOR ,
            'MIN_SEMESTER' => $this->MIN_SEMESTER ,
            'MAX_SEMESTER' => $this->MAX_SEMESTER 
        ]);

    }

    public function update(Request $request, $transfer)
    {        
        $validatedData = $request->validate([            
            'university_id' => 'required',
            'department_id' => 'required', 
            'education_year'  => 'required', 
            'semester'  => 'required',  
            'note' =>  'required',       
        ]);

        //CHECK WEATHER STUDENT HAS THE DEPARTMENT
        $student = Student::find($transfer->student_id);
        if($student->department === null){
            return redirect()->back()->with('message', trans('general.student_must_specify_department', ['student' => $student->name]));
        }

        //CHECK WEATHER STUDENT HAS THE ENROLLED STATUS
        if($student->status->id !== 2){
            return redirect()->back()->with('message',  trans('general.student_must_has_enrolled_status', ['student' => $student->name]));
        }

        //CHECK WEATHER STUDENT SHOULD HAVE VALID SCORE FOR TRANSFER
        $department  = $student->department;
        $kankorYear = $student->kankor_year;
        $toDepartment = Department::allUniversities()->where('id', $request->department_id)->first();
        $allowedScore = $department->students->where('kankor_year', $student->kankor_year)->min('kankor_score');

        // CHECK DEPARTMENT_TYPE GENDER AND KANKOR YEAR OF STUDENT FOR A SPECIFIC PUPROSE
        if($toDepartment->department_type === 'روزانه' and $student->gender === "Male" and $student->kankor_year >= 1398){

            if($student->kankor_score >= $allowedScore) {
                return redirect()->back()->with('message',  trans('general.student_must_have_greater_or_equal_valid_score_for_transfer', ['student' => $student->name]));
            }
        }
           
        $transfer->update([
            'to_department_id' => $request->department_id, //to requested department 
            'education_year' => $request->education_year,
            'semester' => $request->semester,
            'note' => $request->note
        ]);
        $message = __('general.transfer_succssessfully_edited',[ 'form_no' => $transfer->student->form_no ]);

        return redirect(route('transfers.index'))->with('message', $message);
    }


    public function approved($transfer)
    {
        if (( $transfer->approved ==1 ) and (! auth()->user()->hasRole('super-admin'))) {
            abort(403);
        }
  
        // if user is super admin then he can approved tansfer even if transfers already approved

        $department = Department::find($transfer->to_department_id);
        $university_id = $department->university_id;

        \DB::transaction(function () use ($transfer,$university_id){

            $transfer->update([
                'approved' => true
            ]);

            $student = Student::find($transfer->student_id);

            if($student->group_id){
                GroupStudentHistory::create([
                    'student_id' => $transfer->student_id,
                    'group_id' => $student->group_id,
                ]);
            }
            
            $student->update([
                'university_id' => $university_id,
                'department_id' => $transfer->to_department_id,
                'group_id' => null
            ]);
           
        });
        
        return redirect(route('transfers.index'))->with('message',  trans('general.transfer_student_successfully_approved', ['student' => $transfer->student->form_no]));
 
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($transfer)
    {
        \DB::transaction(function () use ($transfer){
            $transfer->student->update([
                'university_id' => $transfer->fromDepartment->university_id,
                'department_id' => $transfer->from_department_id
            ]);
            $transfer->delete();
        });

        return redirect(route('transfers.index'));
    }
}