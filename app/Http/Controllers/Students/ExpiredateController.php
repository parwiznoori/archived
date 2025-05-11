<?php
namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;


class ExpiredateController extends Controller
{
    public function __construct()
    {        
        //  $this->middleware('permission:view-student', ['only' => ['index', 'show']]);        
        //  $this->middleware('permission:create-student', ['only' => ['create','store']]);
        //  $this->middleware('permission:edit-student', ['only' => ['edit','update', 'updateStatus']]);
        //  $this->middleware('permission:delete-student', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($student)
    {
        //  dd($student); 
        // $student = Student::all();
        // return view('students.expiredate',compact(' student'));

        return view('students.expiredate', [
             'student' => $student,
            'title' => trans('general.create-cardExpiredate')
        ]);
        
    
    }

 

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
 
   
    public function update(Request $request, $student)
    {
    //    dd($student);
        
        $validatedData = $request->validate([
            'expire_date' => 'required',
        //     'issue_date' => 'required',
        ]);

        $student->update([
            
            'expire_date' => $request->expire_date,
            // 'issue_date' => $request->issue_date,
           
        ]);
        // Student::whereId($id)->update($validatedData);

        return redirect(route('students.expiredate', $student))->with('success', 'Expire Date is successfully updated');
    }


  
   
   

   

    

    

}
