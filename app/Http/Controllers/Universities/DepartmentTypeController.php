<?php

namespace App\Http\Controllers\Universities;

use App\DataTables\DepartmentTypeDataTable;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DepartmentType;
use Illuminate\Http\Request;

class DepartmentTypeController extends Controller
{
    public function __construct()
    {        
        $this->middleware('permission:view-departmentType', ['only' => ['index', 'show']]);        
        $this->middleware('permission:create-departmentType', ['only' => ['create','store']]);
        $this->middleware('permission:edit-departmentType', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-departmentType', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DepartmentTypeDataTable $dataTable)
    {        
        return $dataTable->render('department_type.index', [
            'title' => trans('models/departmentType.plural'),
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
        $departmentTypes=DepartmentType::all()->count();
        $i=0;
       
        if(!$departmentTypes)
        {
            $facultiesFromDatabase=Department::where('deleted_at',Null)
            ->select('department_type')
            ->distinct()
            ->get();

            foreach($facultiesFromDatabase as $faculty)
            {
                if($faculty->department_type)
                {
                   
                    $newFaculty=new DepartmentType();
                    $newFaculty->name=$faculty->department_type;
                    $newFaculty->save();
                   // echo "$i++- faculty ".$faculty->faculty." is inserted to faculty table <br>";

                }
               
            }
        }

        return view('department_type.create', [
            'title' => trans('models/departmentType.singular'),
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

        $department_type = DepartmentType::create($input);

        // Flash::success(__('messages.saved', ['model' => __('models/departmentType.singular')]));

        return redirect(route('departmentType.index'));
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
        $departmentType = DepartmentType::find($id);

        return view('department_type.edit', [
            'title' => trans('models/departmentType.singular'),
            'description' => trans('crud.edit'), 
            'departmentType' => $departmentType

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

        $departmentType = DepartmentType::find($id);

        if (empty($departmentType)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('departmentType.index'));
        }

        $departmentType->update($request->all());

        // Flash::success(__('messages.updated', ['model' => __('models/cars.singular')]));

        return redirect(route('departmentType.index'));
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
        $department_type = DepartmentType::find($id);

        if (empty($department_type)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('departmentType.index'));
        }

        $department_type->delete($id);

        // Flash::success(__('messages.deleted', ['model' => __('models/cars.singular')]));

        return redirect(route('departmentType.index'));
    }
}
