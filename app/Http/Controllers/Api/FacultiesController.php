<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Faculty;

class FacultiesController extends Controller
{
    public function __invoke(Request $request, $university = null)
    {
        
        $faculties =  Faculty::leftJoin('universities', 'universities.id', '=', 'faculties.university_id')
        ->select('faculties.id',\DB::raw('CONCAT(faculties.name) as text'));
        
        if ($university) {            
            $faculties->allUniversities()                
                ->where('faculties.university_id', $university);
        }
        
        if ($request->q != '') {
            $faculties->where('faculties.name', 'like', '%'.$request->q.'%');
        }
                
        return $faculties->get();
    }
}
