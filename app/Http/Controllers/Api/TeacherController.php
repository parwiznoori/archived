<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Teacher;
use App\Models\TeacherAcademicRank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use stdClass;

class TeacherController  extends Controller
{  
    public function checkteacherauthapi(Request $request){
       
         
        $teacher = Teacher::select('*')
        ->where('email',$request->email)
        ->first();
        if($teacher!=null){
            if(Hash::check($request->password,$teacher->password )){            
            // query
            $gradeList = Grade::select('name')->get();
            $teacher_academic_ranks = TeacherAcademicRank :: select('title')->get();
            //
            
                $object=new stdClass();
                $object->teacher=$teacher;
                $object->gradeList=$gradeList;
                $object->teacher_academic_ranks=$teacher_academic_ranks;
                $object->token_name=$teacher->createToken('token')->accessToken;
                return (array)$object;
    
                return null;    
            }
        
        }
        return null;
    }

}
