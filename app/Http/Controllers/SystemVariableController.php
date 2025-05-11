<?php

namespace App\Http\Controllers;

use App\DataTables\SystemVariableDataTable;
use App\Models\SystemVariable;
use Illuminate\Http\Request;

class SystemVariableController extends Controller
{
    public function __construct()
    {        
        $this->middleware('permission:view-systemVariable', ['only' => ['index', 'show']]);        
        $this->middleware('permission:create-systemVariable', ['only' => ['create','store']]);
        $this->middleware('permission:edit-systemVariable', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-systemVariable', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SystemVariableDataTable $dataTable)
    {        
        return $dataTable->render('system_variable.index', [
            'title' => trans('models/systemVariable.plural'),
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
        return view('system_variable.create', [
            'title' => trans('models/systemVariable.singular'),
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
            'name' => 'required',
            'default_value' => 'required',
            'user_value' => '',           
            'description' => ''         
        ]);

        $input = $request->all();

        $system_variable = SystemVariable::create($input);

        // Flash::success(__('messages.saved', ['model' => __('models/systemVariable.singular')]));

        return redirect(route('systemVariable.index'));
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
        $systemVariable = SystemVariable::find($id);

        return view('system_variable.edit', [
            'title' => trans('models/systemVariable.singular'),
            'description' => trans('crud.edit'), 
            'systemVariable' => $systemVariable

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
            'name' => 'required',
            'default_value' => 'required',
            'user_value' => '',           
            'description' => ''         
        ]);

        $systemVariable = SystemVariable::find($id);

        if (empty($systemVariable)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('systemVariable.index'));
        }

        $systemVariable->update($request->all());

        // Flash::success(__('messages.updated', ['model' => __('models/cars.singular')]));

        return redirect(route('systemVariable.index'));
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
        $system_variable = SystemVariable::find($id);

        if (empty($system_variable)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('systemVariable.index'));
        }

        $system_variable->delete($id);

        // Flash::success(__('messages.deleted', ['model' => __('models/cars.singular')]));

        return redirect(route('systemVariable.index'));
    }
}
