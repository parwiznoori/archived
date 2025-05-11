<?php
namespace App\Http\Controllers\Students\Groups;

use App\DataTables\GroupsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Students\Groups\StoreGroupRequest;
use App\Models\Department;
use App\Models\Group;
use App\Models\GroupStudentHistory;
use App\Models\Student;
use App\Models\SystemVariable;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupsController extends Controller
{
    protected $MIN_YEAR_KANKOR;
    protected $MAX_YEAR_KANKOR;
    protected $MIN_SEMESTER;
    protected $MAX_SEMESTER;
    protected $genderoptions;

    public function __construct()
    {        
        $this->middleware('permission:view-group', ['only' => ['index', 'show']]);        
        $this->middleware('permission:create-group', ['only' => ['create','store']]);
        $this->middleware('permission:edit-group', ['only' => ['create','store']]);
        $this->middleware('permission:delete-group', ['only' => ['destroy']]);
        $this->middleware('permission:merge-group', ['only' => ['mergeGroupsForm','mergeGroupsStore']]);

        $this->MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first()->user_value;
        $this->MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first()->user_value;
        $this->MIN_SEMESTER=SystemVariable::where('name','MIN_SEMESTER')->first()->user_value;
        $this->MAX_SEMESTER=SystemVariable::where('name','MAX_SEMESTER')->first()->user_value;
        $this->genderoptions = [
            'm' => trans('general.Male'),
            'f' => trans('general.Female'),
            'b' => trans('general.Male_and_Female')
        ]; 
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GroupsDataTable $dataTable)
    {     
        return $dataTable->render('students.groups.index', [
            'title' => trans('general.groups'),
            'description' => trans('general.groups_list')
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
        $kankorYear = Student::max('kankor_year'); 
       
        return view('students.groups.create', [
            'title' => trans('general.groups'),
            'description' => trans('general.new_group'),
            'universities' => University::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : [] ,
            'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR ,
            'MAX_YEAR_KANKOR' => $kankorYear <= $this->MAX_YEAR_KANKOR ? $kankorYear : $this->MAX_YEAR_KANKOR ,
            'currentDate' => $currentDate,
            'genderoptions' => $this->genderoptions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGroupRequest $request)
    {
        $validatedData = $request->validated();
        $group = Group::create($validatedData);

        return redirect(route('groups.index'));
    }

    public function recover($id)
    {
        $group=Group::where('id',$id)->withTrashed()->first();
        if(isset($group->deleted_at))
        {
            $group->restore();
        }

        return redirect(route('groups.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($group)
    {
        $kankorYear = Student::max('kankor_year'); 
        $date=explode(' ',jdate()); //current date and time
        $currentDate=explode('-',$date[0]); //current date 

        return view('students.groups.edit', [
            'title' => trans('general.groups'),
            'description' => trans('general.edit_group'),
            'group' => $group,
            'universities' => University::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name', 'id') : $group->department()->pluck('name', 'id'),
            'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR ,
            'MAX_YEAR_KANKOR' => $kankorYear <= $this->MAX_YEAR_KANKOR ? $kankorYear : $this->MAX_YEAR_KANKOR ,
            'currentDate' => $currentDate,
            'genderoptions' => $this->genderoptions
        ]);
    }

    /**
     * Update a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(StoreGroupRequest $request, $group)
    {
        $validatedData = $request->validated();
        $group =  $group->update($validatedData);
        return redirect(route('groups.index'));
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($group)
    {
        $group->delete();
        return redirect(route('groups.index'));
    }
    /**
     * merge groups
     * show form to users due to select gorups will have been merged
     * @return \Illuminate\Http\Response
     */
    public function mergeGroupsForm()
    {

        return view('students.groups.merge-groups-form', [
            'title' => trans('general.groups'),
            'description' => trans('general.merge_groups'),
            'universities' => University::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name',
                'id') : [],
            'groups' => old('groups') != '' ? Group::where('id', old('groups'))->pluck('name', 'id') : [],
            'MIN_YEAR_KANKOR' => $this->MIN_YEAR_KANKOR ,
            'MAX_YEAR_KANKOR' => $this->MAX_YEAR_KANKOR ,
            'MIN_SEMESTER' => $this->MIN_SEMESTER,
            'MAX_SEMESTER' => $this->MAX_SEMESTER 
        ]);

    }

    public function mergeGroupsStore(Request $request)
    {
        
        $university = $request->university;
        $department = $request->department;
        $main_group = $request->main_group;
        $merge_group = $request->merge_group;

        if(!$university)
        {
            return response()->json([
                'success' => false,
                'message' => trans('general.you_must_choose_university')
            ], 400);
        }

        if(!$department)
        {
            return response()->json([
                'success' => false,
                'message' => trans('general.you_must_choose_department')
            ], 400);
        }

        if(!$main_group)
        {
            return response()->json([
                'success' => false,
                'message' => trans('general.you_must_choose_main_group')
            ], 400);
        }

        if(!$merge_group)
        {
            return response()->json([
                'success' => false,
                'message' => trans('general.you_must_choose_merge_group')
            ], 400);
        }

        if($merge_group == $main_group)
        {
            return response()->json([
                'success' => false,
                'message' => trans('general.main_and_merge_must_not_be_same')
            ], 400);
        }
        else
        {
            $mainGroup = Group::find($main_group);
            $mergeGroup = Group::find($merge_group);

            if($mainGroup->department_id != $mergeGroup->department_id)
            {
                return response()->json([
                    'success' => false,
                    'message' => trans('general.main_and_merge_must_have_same_department_id')
                ], 400);
            }

            if($mainGroup->kankor_year != $mergeGroup->kankor_year)
            {
                return response()->json([
                    'success' => false,
                    'message' => trans('general.main_and_merge_must_have_same_kankor_year')
                ], 400);
            }

            if($mainGroup->gender != $mergeGroup->gender)
            {
                return response()->json([
                    'success' => false,
                    'message' => trans('general.main_and_merge_must_have_same_gender')
                ], 400);
            }

            ///////////// step 1 : update students with new group id (main group) /////////
            $students = Student::where('group_id',$merge_group)->get();
            $course_groups = DB::table('course_group')->where('group_id',$merge_group)->get();

            $results = DB::transaction(function () use ($students, $main_group,$course_groups,$mergeGroup) { 
                $results = [];   
                $s = 0; 
                $c = 0;       
                foreach($students as $student)
                {
                    $s++;
                    $student->update(['group_id' => $main_group]);
                    
                    GroupStudentHistory::create([
                        'student_id' => $student->id,
                        'group_id' => $main_group,
                    ]);
                }


                foreach($course_groups as $course_group)
                {
                    $c++;
                    $course_group->update(['group_id' => $main_group]);
                }
                $results['student_numbers'] = $s;
                $results['course_numbers'] = $c;

                $mergeGroup->delete();
                return $results;
            });

            return response()->json([
                'success' => true, 
                'message' => trans('general.main_and_merge_group_successfully_merged_togeather').
                            trans('general.student_numbers_chaged_group' , [ 'student_numbers' => $results['student_numbers'] ]).
                            trans('general.course_numbers_chaged_group' , [ 'course_numbers' => $results['course_numbers'] ])
            ], 200);

        }
       
    }
}