<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Group;
use App\Models\Score;
use App\Models\Student;
use App\Models\StudentResult;
use App\Models\SystemVariable;
use App\Models\University;
use App\Services\SemesterBasedResultsService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class StudentsResultController extends Controller
{
    protected $semesterBasedResultsService;

    public function __construct(SemesterBasedResultsService $semesterBasedResultsService)
    {
        ini_set("pcre.backtrack_limit", "10000000");
        $this->semesterBasedResultsService = $semesterBasedResultsService;
    }

    public function index()
    {
        $MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first();
        $MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first();
        $MIN_SEMESTER=SystemVariable::where('name','MIN_SEMESTER')->first();
        $MAX_SEMESTER=SystemVariable::where('name','MAX_SEMESTER')->first();

        return view('students.results.show-form', [
            'title' => trans('general.students_result'),
            'description' => trans('general.create_students_result'),
            'universities' => University::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name',
                'id') : [],
            'groups' => old('groups') != '' ? Group::where('id', old('groups'))->pluck('name', 'id') : [],
            'MIN_YEAR_KANKOR' => $MIN_YEAR_KANKOR->user_value ,
            'MAX_YEAR_KANKOR' => $MAX_YEAR_KANKOR->user_value ,
            'MIN_SEMESTER' => $MIN_SEMESTER->user_value ,
            'MAX_SEMESTER' => $MAX_SEMESTER->user_value 
        ]);

    }

    public function create_results(Request $request)
    {
        $department = Department::find($request->department);
        $university = University::find($request->university);
        // $year = $request->year;
        $semester = $request->semester;
        $MIN_SCORE_FOR_PASSED_EXAM=$this->semesterBasedResultsService->MIN_SCORE_FOR_PASSED_EXAM();
        $MIN_SCORE_FOR_PASSED_SEMESTER=$this->semesterBasedResultsService->MIN_SCORE_FOR_PASSED_SEMESTER();
        ///////////////////////////////// semester based results /////////////////////////////////////
        /*
        step1 : find the group
        */
        $group=$this->semesterBasedResultsService->find_group($request);
        if (!$group) {
            Session::flash('message', trans('general.group_not_found_for_department_exception'));
            return redirect()->back()->withInput($request->input());
        }
        $year= $group->kankor_year;
        /*
        step2 : fetch all cousrses and subjects within this semester and group 
       */
        $courseSpecification=$this->semesterBasedResultsService->course_specification($semester,$group->id);
        $courseSubjects = $this->semesterBasedResultsService->course_subjects($semester,$group->id);

        if(!$courseSubjects->count())
        {
            Session::flash('message', trans('general.courses_were_not_existed_for_this_specification'));
            return redirect()->back()->withInput($request->input());
        }
        $courseArray= $this->semesterBasedResultsService->get_course_array($courseSubjects);
        $coursesIdArray=$this->semesterBasedResultsService->get_courses_id_array($courseSubjects);

        $courseNotApprovedArray= $this->semesterBasedResultsService->check_all_courses_were_final_approved($courseSubjects);
        if(count($courseNotApprovedArray)  > 0 )
        {
            if(count($courseNotApprovedArray['course_id']) > 0 )
            {
                $message='';
                for($i=0;$i < count($courseNotApprovedArray['course_id']);$i++)
                {
                    $message.= $i+1 .' - ' . trans('general.course_must_be_final_approved',
                    [
                        'subject' => $courseNotApprovedArray['subject_name'][$i], 
                        'year' => $courseNotApprovedArray['education_year'][$i],
                        'semester' => $courseNotApprovedArray['semester'][$i],
                        'code' => $courseNotApprovedArray['code'][$i]
                    ]);
    
                }
                Session::flash('message', $message);
                return redirect()->back()->withInput($request->input());
            }

        }
       
        $courseHasNotScoreArray= $this->semesterBasedResultsService->check_all_courses_were_have_scores($courseSubjects);
        if(count($courseHasNotScoreArray) > 0 )
        {
            $message='';
            for($i=0;$i < count($courseHasNotScoreArray['course_id']);$i++)
            {
                $message.= trans('general.course_must_be_has_score',
                [
                    'subject' => $courseHasNotScoreArray['subject_name'][$i], 
                    'year' => $courseHasNotScoreArray['education_year'][$i],
                    'semester' => $courseHasNotScoreArray['semester'][$i],
                    'code' => $courseHasNotScoreArray['code'][$i]
                ]);

            }
            Session::flash('message', $message);
            return redirect()->back()->withInput($request->input());
        }

        /*
         step3 :
        fetch all students from the groupe that given with university and department id from the request form
        */
        $students =$this->semesterBasedResultsService->find_students_in_group_with_at_least_enrollment_in_one_course($request,$group,$coursesIdArray);
        if (count($students) <= 0 ) {
            Session::flash('message',trans("general.There_are_no_students_in_this_group"));
            return redirect()->back()->withInput($request->input());
        }    
        $studentsArray=$this->semesterBasedResultsService->get_students_array($students);
        $studentsIdArray=$this->semesterBasedResultsService->get_students_id_array($students);
        //////////////////////////////////////////////////////////////////////////////////////////////
        /*
         step4 : find all scores of students were enrolment in courses
        */
        $scoresArray=array();
        $studentsScoresArray=Score::whereIn('student_id',$studentsIdArray)
        ->whereIn('course_id',$coursesIdArray)->get();

       $studentsData= $this->semesterBasedResultsService->get_students_results($students,$courseArray,$studentsScoresArray,$MIN_SCORE_FOR_PASSED_EXAM,$MIN_SCORE_FOR_PASSED_SEMESTER);
    
    //    dd($studentsData['scoresArray']['1177877']);
       $studentsResults=$studentsData['studentsResults'];
       $scoresArray=$studentsData['scoresArray'];
       $failedStudentNumbers=$studentsData['failedStudentNumbers'];
       $failedAverageStudentNumbers=$studentsData['failedAverageStudentNumbers'];
       $passedStudentNumbers=$studentsData['passedStudentNumbers'];
       $deprivedStudentNumbers=$studentsData['deprivedStudentNumbers'];
       $absentStudentNumbers=$studentsData['absentStudentNumbers'];
        
        $subjectsCount = $courseSubjects->count();

        $pdf = \PDF::loadView('students.results.semester_results',
        compact('university', 'department', 'semester', 'year', 'students','studentsArray','scoresArray','courseArray', 'subjectsCount', 'courseSubjects','MIN_SCORE_FOR_PASSED_EXAM','studentsResults','group','courseSpecification','MIN_SCORE_FOR_PASSED_SEMESTER','failedStudentNumbers','failedAverageStudentNumbers','passedStudentNumbers','deprivedStudentNumbers','absentStudentNumbers'), [],
        [
            'format' => 'A4-L',
        ]);
       

        return $pdf->stream('semester_results.pdf');
         
        // return view('students.results.semester_results',
        // compact('university', 'department', 'semester', 'year', 'students','studentsArray','scoresArray','courseArray', 'subjectsCount', 'courseSubjects','MIN_SCORE_FOR_PASSED_EXAM','studentsResults','group','courseSpecification','MIN_SCORE_FOR_PASSED_SEMESTER','failedStudentNumbers','failedAverageStudentNumbers','passedStudentNumbers','deprivedStudentNumbers'));
       
        
    }

    public function create(Request $request)
    {

        // dd($request->all());
        //use for test puprose
        // $department = Department::find($request->department);
        // $group = $department->group->where('kankor_year', $request->year)->first();
        // $students = $group->students;
        // $courseSubjects = $students[0]->courses->where('semester', $request->semester)->where('year', $request->year);
        // $subjectsCount = $courseSubjects->count();
        // return view ('students.results.test', [
        //     'title' => trans('general.students_result'),
        //     'description' => trans('general.create_students_result'),
        //     'university' => University::find($request->university),
        //     'department' => Department::find($request->department),
        //     'semester' => $request->semester,
        //     'year' => $request->year,
        //     'students' => $students,
        //     'subjectsCount' => $subjectsCount,
        //     'courseSubjects' => $courseSubjects,
        // ]);

        // use for production purpose
        // dd($request->all());
        $department = Department::find($request->department);
        $university = University::find($request->university);
        $year = $request->year;
        $semester = $request->semester;
        $systemVariable=SystemVariable::where('name','MIN_SCORE_FOR_PASSED_EXAM')->first();
        $MIN_SCORE_FOR_PASSED_EXAM=$systemVariable->user_value;
        //////////////////////////////////////////////////////////////////////
        
        try {
            $group = $department->group->where('kankor_year', $request->year)->first();
            if (!$group) {
                throw (new ModelNotFoundException(trans('general.group_not_found_for_department_exception')));
            }
        } catch (ModelNotFoundException $exception) {
            Session::flash('message', $exception->getMessage());
            return redirect()->back()->withInput($request->input());
        }
        /*
        fetch all students from the groupe that given with university and department id from the request form
        */
        try {
            $students = $group ? $group->students : null;
            if (count($students) <= 0 ) {
                throw new ModelNotFoundException("There are no students in this group");
            }
        } catch (ModelNotFoundException $exception) {
            Session::flash('message', $exception->getMessage());
            return redirect()->back()->withInput($request->input());
        }

        try {
            $courseSubjects = $students[0]->courses->where('semester', $request->semester);
            if (!$courseSubjects) {
                throw new ModelNotFoundException("No subjects for this specification");
            }
        } catch (ModelNotFoundException $exception) {
            
            Session::flash('message', $exception->getMessage());
            return redirect()->back()->withInput($request->input());
        }
        
        // foreach($students as $student)
        // {
        //     echo $student->id.'<br>';

        // }
        // dd($group,$students);
        // exit;
        $courseArray= array();
        $scoresArray=array();
        $studentsArray=array();
        $j=0;
        $i=0;
        $studentsIdArray=array();
        $coursesIdArray=array();
        
        foreach($students as $student)
        {
            $studentsIdArray[$j]=$student->id;
            $j++;
        }
        // $student_string=implode(',',$studentsIdArray);
       
        $j=0;
        foreach($courseSubjects as $course)
        {
            $coursesIdArray[$j++]=$course->id;

        }
        // $course_string=implode(',',$coursesIdArray);
        $j=0;
        foreach($students as $student)
        {
            $studentsArray['id'][$j]=$student->id;
            $studentsArray['form_no'][$j]=$student->form_no;
            $studentsArray['name'][$j]=$student->name;
            $studentsArray['last_name'][$j]=$student->last_name;
            $studentsArray['father_name'][$j]=$student->father_name;
            $studentsArray['grandfather_name'][$j]=$student->grandfather_name;
            $studentsArray['kankor_year'][$j]=$student->kankor_year;
            $j++;
        }
    
        $studentsScoresArray=Score::whereIn('student_id',$studentsIdArray)
        ->whereIn('course_id',$coursesIdArray)->get();
        $j=0;
        $i=0;
        foreach($courseSubjects as $course)
        {
            $courseArray['course_id'][$i]=$course->id;
            $courseArray['semester'][$i]=$course->semester;
            $courseArray['subject_id'][$i]=$course->subject_id;
            $courseArray['subject_name'][$i]=$course->subject->title;
            $courseArray['credit'][$i]=$course->subject->credits;
          
            ///////////////////////////////////
            foreach($students as $student)
            {
                $studentScore=$studentsScoresArray->where('student_id', $student->id)
                ->where('course_id',$course->id)->first();
               
                $scoresArray[$course->id][$student->id][1]=
                (isset($studentScore->total) ? $studentScore->total : ''); //chance 1
               
                $scoresArray[$course->id][$student->id][2]=
                (isset($studentScore->chance_two) ? $studentScore->chance_two : '');//chance 2
                $scoresArray[$course->id][$student->id][3]=
                (isset($studentScore->chance_three) ? $studentScore->chance_three : '');//chance 3
                $scoresArray[$course->id][$student->id][4]=
                (isset($studentScore->chance_four) ? $studentScore->chance_four : '');//chance 4

                $scoresArray[$course->id][$student->id]['passed']=
                (isset($studentScore->passed) ? $studentScore->passed : 0);//chance 4

                
            }
            $i++;


        }
      
        // echo "<pre>";
        // print_r($scoresArray);
        // exit;
        ///////////////////////////////////////////////////////////////////////


        $subjectsCount = $courseSubjects->count();
        

        return view('students.results.print1',
        compact('university', 'department', 'semester', 'year', 'students','studentsArray','scoresArray','courseArray', 'subjectsCount', 'courseSubjects','MIN_SCORE_FOR_PASSED_EXAM'));
        exit;
        //print1
        
        // dd($department,$university,$group,$students,$courseSubjects,$subjectsCount,$year,$semester);
        // $pdf = \PDF::loadView('students.results.print1',
        //  compact('university', 'department', 'semester', 'year', 'students','studentsArray','scoresArray','courseArray', 'subjectsCount', 'courseSubjects','MIN_SCORE_FOR_PASSED_EXAM'), [],
        //     [
        //         'format' => 'A4-L',
        //     ]);

        // return $pdf->stream('document.pdf');
    }

    public function show()
    {

        // return view('students.results.show', [
        //     'title' => trans('general.students_result'),
        //     'description' => trans('general.show_students_result'),
        //     'universities' => University::pluck('name', 'id'),
        //     'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name',
        //         'id') : [],
        // ]);

        $MIN_YEAR_KANKOR=SystemVariable::where('name','MIN_YEAR_KANKOR')->first();
        $MAX_YEAR_KANKOR=SystemVariable::where('name','MAX_YEAR_KANKOR')->first();
        $MIN_SEMESTER=SystemVariable::where('name','MIN_SEMESTER')->first();
        $MAX_SEMESTER=SystemVariable::where('name','MAX_SEMESTER')->first();

        return view('students.results.show', [
            'title' => trans('general.students_result'),
            'description' => trans('general.show_students_result'),
            'universities' => University::pluck('name', 'id'),
            'department' => old('department') != '' ? Department::where('id', old('department'))->pluck('name',
                'id') : [],
            'groups' => old('groups') != '' ? Group::where('id', old('groups'))->pluck('name', 'id') : [],
            'MIN_YEAR_KANKOR' => $MIN_YEAR_KANKOR->user_value ,
            'MAX_YEAR_KANKOR' => $MAX_YEAR_KANKOR->user_value ,
            'MIN_SEMESTER' => $MIN_SEMESTER->user_value ,
            'MAX_SEMESTER' => $MAX_SEMESTER->user_value 

        ]);
    }

    public function showResult(Request $request)
    {
        $department = Department::find($request->department);
        $university = University::find($request->university);
        $semester = $request->semester;

        $MIN_SCORE_FOR_PASSED_SEMESTER=$this->semesterBasedResultsService->MIN_SCORE_FOR_PASSED_SEMESTER();
        ///////////////////////////////// semester based results /////////////////////////////////////
        /*
        step1 : find the group
        */
        $group=$this->semesterBasedResultsService->find_group($request);
        if (!$group) {
            Session::flash('message', trans('general.group_not_found_for_department_exception'));
            return redirect()->back()->withInput($request->input());
        }
        $year= $group->kankor_year;
        /*
        step2 : fetch all cousrses and subjects within this semester and group 
       */
        $courseSpecification=$this->semesterBasedResultsService->course_specification($semester,$group->id);
        $courseSubjects = $this->semesterBasedResultsService->course_subjects($semester,$group->id);
        
        if(!$courseSubjects->count())
        {
            Session::flash('message', trans('general.courses_were_not_existed_for_this_specification'));
            return redirect()->back()->withInput($request->input());
        }
        $courseArray= $this->semesterBasedResultsService->get_course_array($courseSubjects);
        $coursesIdArray=$this->semesterBasedResultsService->get_courses_id_array($courseSubjects);

        $courseNotApprovedArray= $this->semesterBasedResultsService->check_all_courses_were_final_approved($courseSubjects);
        if(count($courseNotApprovedArray)  > 0 )
        {
            if(count($courseNotApprovedArray['course_id']) > 0 )
            {
                $message='';
                for($i=0;$i < count($courseNotApprovedArray['course_id']);$i++)
                {
                    $message.= $i+1 .' - ' . trans('general.course_must_be_final_approved',
                    [
                        'subject' => $courseNotApprovedArray['subject_name'][$i], 
                        'year' => $courseNotApprovedArray['education_year'][$i],
                        'semester' => $courseNotApprovedArray['semester'][$i],
                        'code' => $courseNotApprovedArray['code'][$i]
                    ]);
    
                }
                Session::flash('message', $message);
                return redirect()->back()->withInput($request->input());
            }

        }
       

        $courseHasNotScoreArray= $this->semesterBasedResultsService->check_all_courses_were_have_scores($courseSubjects);
        if(count($courseHasNotScoreArray) > 0 )
        {
            $message='';
            for($i=0;$i < count($courseHasNotScoreArray['course_id']);$i++)
            {
                $message.= trans('general.course_must_be_has_score',
                [
                    'subject' => $courseHasNotScoreArray['subject_name'][$i], 
                    'year' => $courseHasNotScoreArray['education_year'][$i],
                    'semester' => $courseHasNotScoreArray['semester'][$i],
                    'code' => $courseHasNotScoreArray['code'][$i]
                ]);

            }
            Session::flash('message', $message);
            return redirect()->back()->withInput($request->input());
        }

        $education_year=$courseSpecification->year;
        // dd($education_year);
        $studentsResult = StudentResult::where('department_id', $request->department)
            ->where('education_year', $education_year)
            ->where('semester', $request->semester)->get();

        // dd($studentsResult);

        return view('students.results.show_result', [
            'title' => trans('general.show_result'),
            'description' => trans('general.show_students_result'),
            'studentsResult' => $studentsResult,
            'department' => $request->department,
            'university' => $request->university,
            'year' => $education_year,
            'semester' => $request->semester,
            'MIN_SCORE_FOR_PASSED_SEMESTER' => $MIN_SCORE_FOR_PASSED_SEMESTER
        ]);
    }

    public function movedToNextSemester(Request $request)
    {
        // dd($request->results);
        // exit;
        $current_semester=$request->semester;

        $students = Student::whereIn('id', $request->results)->get();
        $studentIncreaseSmester=0;
        // $semester = $students[0]->semester;
        foreach ($students as $student) {
            $student_semester=$student->semester;

            $studentResult = StudentResult::where('student_id', $student->id)
                ->where('education_year', $request->year)
                ->where('semester', $request->semester)
                ->first();

            if ($studentResult and $studentResult->increase_semester == 1) {
                if($current_semester > $student_semester)
                {
                    $studentIncreaseSmester++;
                   // $studentResult->student->incrementSemester();
                    $studentResult->student->update(['semester' => $current_semester ]);
                } 
            }
        }

        return redirect(route('students.semester-base.show'))->with('message',
            trans('general.students_have_moved_to_next_semester', ['students' => $studentIncreaseSmester]));
    }


}
