<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\Dropout;
use App\Models\Student;
use App\Models\SystemVariable;
use Illuminate\Http\Request;

class CreateAllDropoutController extends Controller
{
    protected $MIN_YEAR_KANKOR;
    protected $MAX_YEAR_KANKOR;
    protected $MIN_SEMESTER;
    protected $MAX_SEMESTER;

    public function __construct()
    {        
        $this->middleware('permission:create-all-dropout', ['only' => ['index', 'create']]);   
        $this->MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first()->user_value;
        $this->MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first()->user_value;
        $this->MIN_SEMESTER=SystemVariable::where('name','MIN_SEMESTER')->first()->user_value;
        $this->MAX_SEMESTER=SystemVariable::where('name','MAX_SEMESTER')->first()->user_value;     
    }
    public function index()
    {
        $date=explode(' ',jdate()); //current date and time
        $currentDate=explode('-',$date[0]); //current date 
        return view('dropouts.create_all.index', [
            'title' => trans('general.dropouts'),
            'description' => trans('general.create_all_dropout'),
            'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR ,
            'MAX_YEAR_KANKOR' => $this->MAX_YEAR_KANKOR ,
            'MIN_SEMESTER' => $this->MIN_SEMESTER ,
            'MAX_SEMESTER' => $this->MAX_SEMESTER ,
            'currentDate' => $currentDate
        ]);
    }
    public function create(Request $request){

        $validatedData = $request->validate([            
            'note' => 'required',
            'kankor_year' => 'required',
        ]);

        $students = Student::where('status_id' , 1)
                    ->where('kankor_year', $request->kankor_year)
                    ->where('department_id', null)
                    ->get();

        foreach($students as $student){
            \DB::transaction(function () use ($request, $student){
                $dropout = Dropout::create([
                    'student_id' => $student->id,
                    'year' => $request->year,
                    'semester' => 1,
                    'note' => $request->note,
                    'university_id' => $student->university_id
                ]);
            });
        }
        return redirect(route('dropouts.index'))->with('message', trans('general.number_of_students_have_been_dropout', ['count' => count($students)]));

    }
}
