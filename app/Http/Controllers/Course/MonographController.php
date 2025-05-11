<?php

namespace App\Http\Controllers\Course;

use App\DataTables\MonographDataTable;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Monograph;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\University;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MonographController extends Controller
{
    public function __construct()
    {        
         $this->middleware('permission:view-monograph', ['only' => ['index', 'show']]);        
         $this->middleware('permission:create-monograph', ['only' => ['create','store']]);
         $this->middleware('permission:edit-monograph', ['only' => ['edit','update', 'updateStatus','update_groups']]);
         $this->middleware('permission:delete-monograph', ['only' => ['destroy']]);
         
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(MonographDataTable $dataTable)
    {   
        return $dataTable->render('monograph.index', [
            'title' => trans('general.monographs'),
            'description' => trans('general.monographs_list')            
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function create()
    {    
       $university_id= auth()->user()->university_id;
       
       if($university_id > 0 )
       {
            $teachers=Teacher::select(DB::Raw('concat_ws(" ",name ," ", last_name ," - ولد : ", father_name) as name'), 'id')->pluck('name','id');
            $universities=University::where('id',$university_id)->pluck('name', 'id');
       }
       else{
        $teachers=[];
        $universities=University::pluck('name', 'id');
       }
       
       //changed teacher code 
        return view('monograph.create', [
            'title' => trans('general.monographs'),
            'description' => trans('general.create_monograph'),
            'universities' => $universities,
            'departments' => Department::pluck('name', 'id'),
            'teachers' => $teachers,
            'students' => old('students') != '' ? Student::where('id', old('students'))->pluck('name', 'id') : [],
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [],
           
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
        
        $request->validate([
            'student_id' => [
                'required',
                Rule::unique('monographs')->whereNull('deleted_at')
            ],
           
            'university_id' => 'required',
            'department_id' => 'required',
            'teacher_id' => 'required',
            'title' => 'required',
            'defense_date' => 'required',
            'score' => 'required|max:100|min:0'
        ]);
        
        \DB::transaction(function () use ($request) {
            // $department = Department::find($request->department);

            $monograph = Monograph::create([
                'university_id' => $request->university_id,
                'department_id' => $request->department_id,
                'student_id' => $request->student_id,
                'teacher_id' => $request->teacher_id,
                'title' => $request->title,
                'defense_date' => $request->defense_date,
                'score' =>  number_format($request->score, 2)
            ]);
        });

        return redirect(route('monographs.index'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($monograph_id)
    {
        $monograph=Monograph::findOrFail($monograph_id);
        $university_id= auth()->user()->university_id;
       
        if($university_id > 0 )
        {
             $teachers=Teacher::select(DB::Raw('concat_ws(" ",name ," ", last_name ," - ولد : ", father_name) as name'), 'id')->pluck('name','id');
             $universities=University::where('id',$university_id)->pluck('name', 'id');
        }
        else{
         $teachers= Teacher::select(DB::Raw('concat_ws(" ",name ," ", last_name ," - ولد : ", father_name) as name'), 'id')->where('id', $monograph->teacher_id)->pluck('name','id');
         $universities=University::pluck('name', 'id');
        }
        

        return view('monograph.edit', [
            'title' => trans('general.monographs'),
            'description' => trans('general.edit_monograph'),
            'monograph' => $monograph,
            'universities' => $universities,
            'departments' => Department::where('university_id',$monograph->university_id)->pluck('name', 'id'),
            'teachers' => $teachers,
            'students' =>  Student::select('id', \DB::raw('CONCAT(form_no, " ", name, " ", last_name ,"-",father_name) as name'))->where('id',$monograph->student_id )->pluck('name', 'id'),
            'defense_date_persian' => $monograph->defense_date
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $monograph_id)
    {
        $monograph=Monograph::findOrFail($monograph_id);
       
        $request->validate([
            'student_id' => [
                'required',
                Rule::unique('monographs')->whereNull('deleted_at')->ignore($monograph_id),
            ],
           
            'university_id' => 'required',
            'department_id' => 'required',
            'teacher_id' => 'required',
            'title' => 'required',
            'defense_date' => 'required',
            'score' => 'required|max:100|min:0'
        ]);

        $score = number_format($request->score, 2);
       
        $monograph->update([
            'university_id' => $request->university_id,
            'department_id' => $request->department_id,
            'student_id' => $request->student_id,
            'teacher_id' => $request->teacher_id,
            'title' => $request->title,
            'defense_date' => $request->defense_date,
            'score' => $score
        ]);
        // $monograph->students()->sync(Student::whereIn('group_id', $monograph->groups)->pluck('id'));

        return redirect(route('monographs.index'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($monograph_id)
    {
        $monograph=Monograph::findOrFail($monograph_id);

            \DB::transaction(function () use ($monograph) {

                $monograph->delete();
            });
    
            return redirect(route('monographs.index'));

       
        
    }

    
}
