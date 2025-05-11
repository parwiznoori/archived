<?php

namespace App\Http\Controllers\Students\Groups;

use App\Events\GroupsGenerateEvent;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class GroupsAutomationController extends Controller
{
    //
    public function index(){
        

        return view('students.groups_automation.create', [
            'title' => trans('general.groups'),
            'description' => trans('general.create_groups_automatically'),
        ]);

        
        
    }

    public function store(Request $request) {

        $departments = Department::all();

        $groups =  event(new GroupsGenerateEvent( $departments , $request));

        return redirect(route('groups.index'))->with('message' , 'گروپ ها موفقانه ایجاد گردید !');
    }
}
