<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use DB;
use Illuminate\Http\Request;


class TeachersController extends Controller
{
    public function __invoke(Request $request,$university = null)
    {
        $teachers =  Teacher::select('id', DB::Raw('concat_ws(" ",name ," (تخلص : ", last_name, ")" ," (نام پدر : ", father_name, ")" ) as text'))
        ->orderBy('name');

        if ($university) {
            $teachers->where('university_id', $university->id);
        }
        if ($request->q != '') {
            $teachers->where('name', 'like', '%'.$request->q.'%');
        }

        return $teachers->take(50)->get();
    }

  
}
