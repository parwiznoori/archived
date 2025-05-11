<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Department;
use App\Models\DepartmentType;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Leave;
use App\Models\Student;
use App\Models\Transfer;
use App\Models\University;
use DB;
use Illuminate\Http\Request;

class DeveloperController extends Controller
{
    public function department_type()
    {
        $i=0;
        $departmentTypes=DepartmentType::select('name','id')->get();
        
        foreach($departmentTypes as $departmentType)
        {
            $department_type_name=$departmentType->name;
            if(isset( $department_type_name))
            {
                $departmentsByType=Department::where('department_type',$department_type_name)
                ->where('university_id','>=',1)->get();
            
                foreach($departmentsByType as $departmentByType)
                {
                    $department=Department::find($departmentByType->id);
                    $department->department_type_id=$departmentType->id;
                    $department->save();
                    echo $i++."department with id : ".$departmentByType->id.' updated  '.'<br>';

                }

           }
        }
    }

    public function department_faculty()
    {
        $i=0;
        $faculties=Faculty::select('id','name','university_id')->get();
        
        foreach($faculties as $faculty)
        {
            $faculty_name=$faculty->name;
            if(isset( $faculty_name))
            {
                $departmentsByFaculty=Department::where('faculty',$faculty_name)
                ->where('university_id',$faculty->university_id)->get();
            
                foreach($departmentsByFaculty as $departmentByFaculty)
                {
                    $department=Department::find($departmentByFaculty->id);
                    $department->faculty_id=$faculty->id;
                    $department->save();
                    echo $i++."department with id : ".$departmentByFaculty->id.' updated with faculty id : '.$faculty->id.'<br>';

                }

           }
        }
    }

    public function migration_create_department_faculty()
    {
        $universities=University::get()->pluck('name', 'id');
        
       
        return view('migration.create_department_faculty', [
            'title' => 'migration : ',
            'description' => 'create_department_faculty',
            'universities' => $universities,
            'faculties' => old('faculties') != '' ? Faculty::where('id', old('faculties'))->pluck('name',
                'id') : [],
            
            
        ]);

        
    }

    public function migrate_deprtment_deleted()
    {
        $department_from=903;
        $department_to=27;
        $students=Student::where('department_id',$department_from)->get();
        $i=1;
        foreach ($students as $student)
        {
            echo " $i - student  with department_id : $department_from => updated to department : $department_to <br> ";
            $i++;
            $student->department_id = $department_to;
            $student->save();
            
        }
        //
        $transfers=Transfer::where('to_department_id',$department_from)->get();
        $i=1;
        foreach ($transfers as $transfer)
        {
            echo " $i - transfer  with department_id : $department_from => updated to department : $department_to <br> ";
            $i++;
            $transfer->to_department_id = $department_to;
            $transfer->save();
            
        }
        //
        $groups=Group::where('department_id',$department_from)->get();
        $i=1;
        foreach ($groups as $group)
        {
            echo " $i - group  with department_id : $department_from => updated to department : $department_to <br> ";
            $i++;
            $group->department_id = $department_to;
            $group->save();
            
        }

        $courses=Course::where('department_id',$department_from)->get();
        $i=1;
        foreach ($courses as $course)
        {
            echo " $i - course  with department_id : $department_from => updated to department : $department_to <br> ";
            $i++;
            $course->department_id = $department_to;
            $course->save();
            
        }
        
        
    }

    public function migration_store_department_faculty(Request $request)
    {
        $university_id=$request->university;
        $faculty_id=$request->faculty;
        $faculty_merge_id=$request->faculty_merge;
        // dd($university_id,$faculty_id,$faculty_merge_id);
        $departments=Department::where('faculty_id',$faculty_id)
        ->where('university_id',$university_id)->get();
        $i=1;
        foreach ($departments as $department)
        {
            echo " $i - department with faculty : $faculty_id => merged to faculty : $faculty_merge_id <br> ";
            $i++;
            $department->faculty_id = $faculty_merge_id;
            $department->save();
            
        }
        $faculty = Faculty::find($faculty_id);
        $faculty->delete();
    }

    public function migration_create_gender_students()
    {
       
        $genders = Student::select('gender')->distinct()->get()->pluck('gender','gender');
        
       
        return view('migration.create_gender_students', [
            'title' => 'migration : ',
            'description' => 'create_gender_students',
            'genders' => $genders,
            
        ]);

        
    }
    public function migration_store_gender_students(Request $request)
    {
        
        $genders=$request->genders;
        $genders_merge=$request->genders_merge;
        // dd($university_id,$faculty_id,$faculty_merge_id);
        $students=Student::where('gender',$genders)->get();
        $i=1;
        foreach ($students as $student)
        {
            
            $i++;
            $student->gender = $genders_merge;
            $student->save();
            
        }
        echo "$i students => gender updated successfully";
    }
    public function courses_group_id($min_value,$max_value)
    {
        // dd($min_value,$max_value);
        $i=0;
        $min=$min_value;
        $max=$max_value;
        $j=0;
        $courses=Course::select('id','groups')
        ->whereBetween('id', [$min, $max])
        ->withTrashed()->get();
       
        foreach($courses as $course)
        {
            echo $j++ ." :  ".$course->id.' - ';
            $array=$course->groups;
            if(is_array($array))
            {
                echo " array - ";
                $data=array();
                $i=0;
                foreach($array as $key=>$value)
                {
                    $data[$i++]=$value;
                    
                }
                print_r($data);
                $group=Group::whereIn('id',$data)->withTrashed()->get();
                if($group->count() > 0 )
                {
                    echo " - course -group synced succeefully";
                    $course->groups()->withTrashed()->sync($data);
                }
                    
            }
            elseif(is_integer($array))
            {
                echo " integer - ";
                $data=array();
                $i=0;
                $data[$i]=$array;
                print_r($data);
                $group=Group::whereIn('id',$data)->withTrashed()->get();
                if($group->count())
                {   echo " - course -group synced succeefully";
                    $course->groups()->withTrashed()->sync($data);
                }
                    
                
            }
           
            echo "<br>";
        }
       
        exit;
        
        // foreach($courses as $course)
        // {
           
        //     if(isset($course->groups))
        //     {
        //         echo $i++." course id : $course->id "." - group : ". (int)$course->groups ." <br>";
        //         $course->group_id=(int)$course->groups;
        //         $course->update();

        //    }
        // }
    }
    public function update_university_in_courses()
    {
        $courses=Course::where('university_id',-1)->withTrashed()->get();
        echo "number of courses with university = -1 : ".$courses->count();
        foreach($courses as $course)
        {
            $department=Department::where('id',$course->department_id)->withTrashed()->first();
            if($department->count()>0)
            {
                echo "course ". $course->id."- department id :".$department->id.'- universit : '.$department->university_id.'<br>';
                $course->university_id=$department->university_id;
                $course->save();
            } 
        }
        echo "finish";
    }
    public function leaves_set_department($min_value,$max_value)
    {
        // Disable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $leaves = Leave::select('leaves.id','leaves.semester','student_id','form_no','students.department_id')
        ->join('students', 'students.id', '=', 'student_id')
        ->whereBetween('leaves.id', [$min_value, $max_value])
        ->get();

        $i=1;
        foreach($leaves as $leave)
        {
            $department_id = $leave->department_id;
            $semester = $leave->semester;
            $semester_type =  ($semester % 2) ? 1 : 2;
            $leave_instance = Leave::where('id',$leave->id)->first();
            $leave_instance->department_id = $department_id;
            $leave_instance->semester_type_id = $semester_type;
            $leave_instance->save();
            echo $i++." st ".$leave->form_no." updated with department : ".$department_id." <br> ";

        }

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
