<?php

namespace App\Http\Controllers\System;

use App\DataTables\RoleTypeDataTable;
use App\Http\Controllers\Controller;
use App\Models\RoleType;
use Illuminate\Http\Request;

class RoleTypeController extends Controller
{
    public function __construct()
    {        
        $this->middleware('permission:view-roleType', ['only' => ['index', 'show']]);        
        $this->middleware('permission:create-roleType', ['only' => ['create','store']]);
        $this->middleware('permission:edit-roleType', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-roleType', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RoleTypeDataTable $dataTable)
    {        
      
        return $dataTable->render('role-type.index', [
            'title' => trans('models/roleType.plural'),
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

        return view('role-type.create', [
            'title' => trans('models/roleType.singular'),
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

        $roleType = RoleType::create($input);

        // Flash::success(__('messages.saved', ['model' => __('models/roleType.singular')]));

        return redirect(route('role-type.index'));
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
        $roleType = RoleType::find($id);

        return view('role-type.edit', [
            'title' => trans('models/roleType.singular'),
            'description' => trans('crud.edit'), 
            'roleType' => $roleType

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

        $roleType = RoleType::find($id);

        if (empty($roleType)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('role-type.index'));
        }

        $roleType->update($request->all());

        // Flash::success(__('messages.updated', ['model' => __('models/cars.singular')]));

        return redirect(route('role-type.index'));
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
        $roleType = RoleType::find($id);

        if (empty($roleType)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('role-type.index'));
        }

        $roleType->delete($id);

        // Flash::success(__('messages.deleted', ['model' => __('models/cars.singular')]));

        return redirect(route('role-type.index'));
    }
}
