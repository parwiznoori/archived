<?php

namespace App\Http\Controllers\Users;

use App\DataTables\PermissionDataTable;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct()
    {        
         $this->middleware('permission:view-permission', ['only' => ['index', 'show']]);        
         $this->middleware('permission:create-permission', ['only' => ['create','store']]);
         $this->middleware('permission:edit-permission', ['only' => ['edit','update']]);
         $this->middleware('permission:delete-permission', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PermissionDataTable $dataTable)
    {        
        return $dataTable->render('permissions.index', [
            'title' => trans('models/permissions.plural'),
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
        return view('permissions.create', [
            'title' => trans('models/permissions.singular'),
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
            'title' => 'required',
            'guard_name' => ''    
        ]);

        $input = $request->all();

        $permission = Permission::create($input);

        return redirect(route('permissions.index'))->with('message', 'مجوز  '.$permission->name.' موفقانه ایجاد شد.');
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
        $permissions = Permission::find($id);

        return view('permissions.edit', [
            'title' => trans('models/permissions.singular'),
            'description' => trans('crud.edit'), 
            'permissions' => $permissions

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
            'title' => 'required',        
        ]);

        $permission = Permission::find($id);

        if (empty($permission)) {
            
            return redirect(route('permissions.index'));
        }

        $permission->update([
            'title' => $request->title,
            'name' => $request->name,
            'guard_name' => $request->guard_name,
            'is_restricted' => $request->is_restricted,
        ]);


        // Flash::success(__('messages.updated', ['model' => __('models/cars.singular')]));

        return redirect(route('permissions.index'))->with('message', 'مجوز  '.$permission->title.' موفقانه آپدیت شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
        $permission = Permission::find($id);
        $permission_name = $permission->title;

        if (empty($permission)) {
            
            return redirect(route('permissions.index'))->with('message', 'مجوز '.$permission_name.' موفقانه حذف شد.');
        }

        $permission->delete($id);

        // Flash::success(__('messages.deleted', ['model' => __('models/cars.singular')]));

        return redirect(route('permissions.index'));
    }

    public function recover($id)
    {
        $permission=Permission::where('id',$id)->withTrashed()->first();
        $permission->restore();
        return redirect(route('permissions.index'))->with('message', 'مجوز  '.$permission->name.' موفقانه ریکاور شد.');
    }
}
