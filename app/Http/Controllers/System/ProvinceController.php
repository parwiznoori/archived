<?php
namespace App\Http\Controllers\System;

use App\DataTables\ProvinceDataTable;
use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function __construct()
    {        
        $this->middleware('permission:view-province', ['only' => ['index', 'show']]);        
        $this->middleware('permission:create-province', ['only' => ['create','store']]);
        $this->middleware('permission:edit-province', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-province', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProvinceDataTable $dataTable)
    {        
      
        return $dataTable->render('province.index', [
            'title' => trans('models/province.plural'),
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
        $province =Province::all()->count();
        
        return view('province.create', [
            'title' => trans('models/province.singular'),
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
            'name_en' => 'required'    
        ]);

        $input = $request->all();

        $province = Province::create($input);

        return redirect(route('province.index'));
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
        $province = Province::find($id);

        return view('province.edit', [
            'title' => trans('models/province.singular'),
            'description' => trans('crud.edit'), 
            'province' => $province

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
            'name_en' => 'required',
        ]);

        $province = Province::find($id);

        if (empty($province)) {
            return redirect(route('province.index'));
        }

        $province->update($request->all());
        return redirect(route('province.index'));
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
        $province = Province::find($id);

        if (empty($province)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('province.index'));
        }

        $province->delete($id);

        // Flash::success(__('messages.deleted', ['model' => __('models/cars.singular')]));

        return redirect(route('province.index'));
    }
}
