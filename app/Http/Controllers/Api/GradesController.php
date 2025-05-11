<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradesController extends Controller
{
    public function __invoke(Request $request)
    {
        $grades =  Grade::select('id', 'name as text');
        
        if ($request->q != '') {
            $grades->where('name', 'like', '%'.$request->q.'%');
        }
                
        return $grades->get();
    }
}
