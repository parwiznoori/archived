<?php

namespace App\Http\Controllers\System;

use App\DataTables\ShiftTypeDataTable;
use App\Http\Controllers\Controller;
use App\Models\ShiftType;
use App\Models\Student;
use Illuminate\Http\Request;


class ShiftTypeController extends Controller
{
    public function __construct()
    {        
        $this->middleware('permission:view-shiftType', ['only' => ['index', 'show']]);        
        $this->middleware('permission:create-shiftType', ['only' => ['create','store']]);
        $this->middleware('permission:edit-shiftType', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-shiftType', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ShiftTypeDataTable $dataTable)
    {        
        return $dataTable->render('shift_type.index', [
            'title' => trans('models/shiftType.plural'),
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
        $shiftTypes=ShiftType::all()->count();
        $i=0;
       
        if(!$shiftTypes)
        {
            $facultiesFromDatabase=Student::where('deleted_at',Null)
            ->select('student_sheft')
            ->distinct()
            ->get();

            foreach($facultiesFromDatabase as $faculty)
            {
                if($faculty->student_sheft)
                {
                   
                    $newFaculty=new ShiftType();
                    $newFaculty->name=$faculty->student_sheft;
                    $newFaculty->save();
                   // echo "$i++- faculty ".$faculty->faculty." is inserted to faculty table <br>";

                }
               
            }
        }

        return view('shift_type.create', [
            'title' => trans('models/shiftType.singular'),
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

        $shift_type = ShiftType::create($input);

        // Flash::success(__('messages.saved', ['model' => __('models/shiftType.singular')]));

        return redirect(route('shiftType.index'));
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
        $shiftType = ShiftType::find($id);

        return view('shift_type.edit', [
            'title' => trans('models/shiftType.singular'),
            'description' => trans('crud.edit'), 
            'shiftType' => $shiftType

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

        $shiftType = ShiftType::find($id);

        if (empty($shiftType)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('shiftType.index'));
        }

        $shiftType->update($request->all());

        // Flash::success(__('messages.updated', ['model' => __('models/cars.singular')]));

        return redirect(route('shiftType.index'));
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
        $shift_type = ShiftType::find($id);

        if (empty($shift_type)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('shiftType.index'));
        }

        $shift_type->delete($id);

        // Flash::success(__('messages.deleted', ['model' => __('models/cars.singular')]));

        return redirect(route('shiftType.index'));
    }
}
