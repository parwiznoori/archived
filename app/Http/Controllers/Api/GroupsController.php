<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupsController extends Controller
{
    public function __invoke(Request $request,$department = null)
    {        
        $groups =  Group::select('id', DB::Raw('concat_ws(" ",name ," [ سال کانکور : ", kankor_year,"]" ) as text'))->orderBy('id', 'DESC');

        if ($department) {
            $groups->where('department_id', $department->id);
        }
        if ($request->q != '') {
            $groups->where('name', 'like', '%'.$request->q.'%');
        }
                
        return $groups->take(20)->get();
    }
}
