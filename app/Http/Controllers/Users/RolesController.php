<?php

namespace App\Http\Controllers\Users;

use App\DataTables\RolesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoleType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{

    public function __construct()
    {        
         $this->middleware('permission:view-role', ['only' => ['index', 'show']]);        
         $this->middleware('permission:create-role', ['only' => ['create','store']]);
         $this->middleware('permission:edit-role', ['only' => ['edit','update']]);
         $this->middleware('permission:delete-role', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RolesDataTable $dataTable)
    {        
        return $dataTable->render('roles.index', [
            'title' => trans('general.users'),
            'description' => trans('general.roles_list'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('roles.create', [
            'title' => trans('general.users'),
            'description' => trans('general.create_role'),
            // 'abilities' => Permission::all(),
            'roleTypes' => RoleType::pluck('name','id')
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
            'name' => 'required|unique:roles',
            'title' => 'required'                         
        ]);
        
        $role = Role::create([
            'name' => $request->name,
            'title' => $request->title,
            'type_id' => $request->roleType,
            'admin' => $request->has('admin'),
            'priority' => $request->priority,
        ]);

        // if ($request->abilities) {
        //     $role->permissions()->sync($request->abilities);
        // }
        
        return redirect(route('roles.index'))->with('message', 'وظیفه '.$request->title.' موفقانه ایجاد شد.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($user)
    {
        exit();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($role)
    {
        $role = Role::find($role->id);
        $abilities = [];
        if(auth()->user()->hasRole('system-developer'))
        {
            $abilities =  Permission::all();
        }
        else if(auth()->user()->hasRole('super-admin'))
        {
            $abilities =  Permission::where('is_restricted' , '<=' ,1)->get();
        }
        return view('roles.edit', [
            'title' => trans('general.users'),
            'description' => trans('general.edit_role'),
            'role' => $role,
            'abilities' => $abilities,
            'roleTypes' => RoleType::pluck('name','id')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $role)
    {
        $role = Role::find($role->id);       
        $validatedData = $request->validate([            
            'name' => [
                'required', 
                Rule::unique('roles')->ignore($role->id, 'id')
            ],
            'title' => 'required'              
        ]);
        
        $role->update([
            'name' => $request->name,
            'title' => $request->title,
            'type_id' => $request->roleType,
            'admin' => $request->has('admin'),
            'priority' => $request->priority,
        ]);

        if ($request->abilities) {
            $role->permissions()->sync($request->abilities);
        }
        
        return redirect(route('roles.index'))->with('message', 'وظیفه '.$request->title.' موفقانه آپدیت شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($role)
    {
        $role_name = $role->title;
        $role = Role::find($role->id); 
        $role->syncPermissions([]);
        $role->delete();

        return redirect(route('roles.index'))->with('message', 'وظیفه '.$role_name.' موفقانه حذف شد.');
    }

    public function recover($id)
    {
        $role=Role::where('id',$id)->withTrashed()->first();
        $role->restore();
        return redirect(route('roles.index'))->with('message', 'وظیفه '.$role->title.' موفقانه ریکاور شد.');
    }

}
