<?php

namespace App\Http\Controllers\System;

use App\DataTables\DayDataTable;
use App\Http\Controllers\Controller;
use App\Models\Day;
use App\Models\Student;
use Illuminate\Http\Request;


class DayController extends Controller
{
    public function __construct()
    {        
        $this->middleware('permission:view-day', ['only' => ['index', 'show']]);        
        $this->middleware('permission:create-day', ['only' => ['create','store']]);
        $this->middleware('permission:edit-day', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-day', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DayDataTable $dataTable)
    {        
      
        return $dataTable->render('day.index', [
            'title' => trans('models/day.plural'),
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
        $day =Day::all()->count();
        $i=0;
       
        if(!$day)
        {
            $facultiesFromDatabase=Student::where('deleted_at',Null)
            ->select('student_sheft')
            ->distinct()
            ->get();

            foreach($facultiesFromDatabase as $faculty)
            {
                if($faculty->student_sheft)
                {
                   
                    $newFaculty=new Day();
                    $newFaculty->name=$faculty->student_sheft;
                    $newFaculty->save();
                   // echo "$i++- faculty ".$faculty->faculty." is inserted to faculty table <br>";

                }
               
            }
        }

        return view('day.create', [
            'title' => trans('models/day.singular'),
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
            'day' => 'required'     
        ]);

        $input = $request->all();

        $day = Day::create($input);

        // Flash::success(__('messages.saved', ['model' => __('models/day.singular')]));

        return redirect(route('day.index'));
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
        $day = Day::find($id);

        return view('day.edit', [
            'title' => trans('models/day.singular'),
            'description' => trans('crud.edit'), 
            'day' => $day

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
            'day' => 'required'       
        ]);

        $day = Day::find($id);

        if (empty($day)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('day.index'));
        }

        $day->update($request->all());

        // Flash::success(__('messages.updated', ['model' => __('models/cars.singular')]));

        return redirect(route('day.index'));
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
        $day = Day::find($id);

        if (empty($day)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('day.index'));
        }

        $day->delete($id);

        // Flash::success(__('messages.deleted', ['model' => __('models/cars.singular')]));

        return redirect(route('day.index'));
    }
}
