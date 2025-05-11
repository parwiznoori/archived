<?php

namespace App\Http\Controllers\System;

use App\DataTables\GradeDataTable;
use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;


class GradesController extends Controller
{
    public function __construct()
    {        
        $this->middleware('permission:view-grades', ['only' => ['index', 'show']]);        
        $this->middleware('permission:create-grades', ['only' => ['create','store']]);
        $this->middleware('permission:edit-grades', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-grades', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Responsegrades
     */
    public function index(GradeDataTable $dataTable)
    {        
        return $dataTable->render('grades.index', [
            'title' => trans('models/grades.plural'),
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
        $grades=Grade::all()->count();
        $i=0;
       
        if(!$grades)
        {
            $facultiesFromDatabase=Student::where('deleted_at',Null)
            ->select('student_sheft')
            ->distinct()
            ->get();

            foreach($facultiesFromDatabase as $faculty)
            {
                if($faculty->student_sheft)
                {
                   
                    $newFaculty=new Grade();
                    $newFaculty->name=$faculty->student_sheft;
                    $newFaculty->save();
                   // echo "$i++- faculty ".$faculty->faculty." is inserted to faculty table <br>";

                }
               
            }
        }

        return view('grades.create', [
            'title' => trans('models/grades.singular'),
            'description' => trans('crud.add_new'),    
        ]);
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
            'name' => 'required'     
        ]);

        $input = $request->all();

        $grades = Grade::create($input);

        // Flash::success(__('messages.saved', ['model' => __('models/grades.singular')]));

        return redirect(route('grades.index'));
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
        $grades = Grade::find($id);

        return view('grades.edit', [
            'title' => trans('models/grades.singular'),
            'description' => trans('crud.edit'), 
            'grades' => $grades

        ]);
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
            'name' => 'required'       
        ]);

        $grades = Grade::find($id);

        if (empty($grades)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('grades.index'));
        }

        $grades->update($request->all());

        // Flash::success(__('messages.updated', ['model' => __('models/cars.singular')]));

        return redirect(route('grades.index'));
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
        $grades = Grade::find($id);

        if (empty($grades)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('grades.index'));
        }

        $grades->delete($id);

        // Flash::success(__('messages.deleted', ['model' => __('models/cars.singular')]));

        return redirect(route('grades.index'));
    }
}
