<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{
    public function __invoke(Request $request,$department = null)
    {        
        if(!$department)
        {
            return '';
        }
        else{
            $subjects =  Subject::select('id', \DB::raw('concat_ws("", title, "[ کد : ",code, "]"," ( کردیت : ", credits,")" ) as text'))
            ->where('department_id', $department->id)
            ->where('active',1)
            ;
            if ($request->q != '') {
                $subjects->where(function($query) use($request)  
                {
                    $query->where('title', 'like', '%'.$request->q.'%')
                    ->orWhere('code', 'like', '%'.$request->q.'%');   
                }); 
            }
            return $subjects->take(100)->get();

        }         
        
    }
}
