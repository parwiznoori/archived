<?php

namespace App\Http\Controllers\System;

use App\DataTables\StudentStatusDataTable;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentStatus;
use Illuminate\Http\Request;


class StudentStatusController extends Controller
{
    public function __construct()
    {        
        $this->middleware('permission:view-studentStatus', ['only' => ['index', 'show']]);        
        $this->middleware('permission:create-studentStatus', ['only' => ['create','store']]);
        $this->middleware('permission:edit-studentStatus', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-studentStatus', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(StudentStatusDataTable $dataTable)
    {        
      
        return $dataTable->render('studentstatus.index', [
            'title' => trans('models/studentstatus.plural'),
            'description' => trans('general.list'),         
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $studentstatus =StudentStatus::all()->count();
        $i=0;
       
        if(!$studentstatus)
        {
            $facultiesFromDatabase=Student::where('deleted_at',Null)
            ->select('student_sheft')
            ->distinct()
            ->get();

            foreach($facultiesFromDatabase as $faculty)
            {
                if($faculty->student_sheft)
                {
                   
                    $newFaculty=new StudentStatus();
                    $newFaculty->name=$faculty->student_sheft;
                    $newFaculty->save();
                   // echo "$i++- faculty ".$faculty->faculty." is inserted to faculty table <br>";

                }
               
            }
        }
/*
        return view('studentstatus.create', [
            'title' => trans('models/studentstatus.singular'),
            'description' => trans('crud.add_new'),    
        ]);
        */
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required', 
            'tag_color' => 'required', 
            'editable' => 'required'     
        ]);

        $input = $request->all();

        $studenstatus = StudentStatus::create($input);

        // Flash::success(__('messages.saved', ['model' => __('models/studentstatus.singular')]));

        return redirect(route('studentstatus.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $studenstatus = StudentStatus::find($id);

        // return view('studentstatus.edit', [
        //     'title' => trans('models/studentstatus.singular'),
        //     'description' => trans('crud.edit'), 
        //     'studentstatus' => $studenstatus

        // ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required', 
            'tag_color' => 'required', 
            'editable' => 'required'       
        ]);

        $studenstatus = StudentStatus::find($id);

        if (empty($studenstatus)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('studentstatus.index'));
        }

        $studenstatus->update($request->all());

        // Flash::success(__('messages.updated', ['model' => __('models/cars.singular')]));

        return redirect(route('studentstatus.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $studentstatus = StudentStatus::find($id);

        if (empty($studenstatus)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('studentstatus.index'));
        }

        $studenstatus->delete($id);

        // Flash::success(__('messages.deleted', ['model' => __('models/cars.singular')]));

        return redirect(route('studentstatus.index'));
    }
}
