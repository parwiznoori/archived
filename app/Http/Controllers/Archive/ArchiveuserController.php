<?php

namespace App\Http\Controllers\Archive;


use App\DataTables\ArchiveuserDataTable;
use App\Http\Controllers\Controller;
use App\Models\University;
use App\Models\Role;
use App\User;
use DB;
use Illuminate\Http\Request;

class ArchiveuserController extends Controller
{
     public function __construct()
     {
          $this->middleware('permission:view-archive', ['only' => ['index', 'show']]);
          $this->middleware('permission:create-archive', ['only' => ['create','store']]);
          $this->middleware('permission:edit-archive', ['only' => ['edit','update', 'updateStatus','update_groups']]);
          $this->middleware('permission:delete-archive', ['only' => ['destroy']]);
         
     }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */




    public function index(ArchiveuserDataTable $dataTable)
    {
        return $dataTable->render('archiveuser.index', [
            'title' => trans('general.users'),
            'description' => trans('general.users_list'),

        ]);
    }

//
//    public function create(){
//
//        $roles = array();
//        if (auth()->user()->hasRole('system-developer')) {
//            $roles = Role::where('archive_type', 2)->get();
//
//        }
//        else
//        {
//            $roles = Role::where('name','!=','super-admin')->where('name','!=','system-developer')
//                ->get();
//        }
//
//        return view('archiveuser.create', [
//            'title' => trans('general.users'),
//            'description' => trans('general.create_account'),
//            'roles' => $roles,
//
//        ]);
//    }
//
//
//
//
//    public function store(Request $request)
//    {
//        $request->university_id='-1';
//        $request->type='2';
//        $controller = new UsersController();
//        $response = $controller->store($request);
//        return redirect(route('archiveuser.index'));
//    }

    public function create()
    {
         $university_id= auth()->user()->university_id;


        $roles = [];
        if (auth()->user()->hasRole('system-developer') || auth()->user()->hasRole('super-admin')) {
            $roles = Role::where('archive_type', 2)->get();
        } else {
            $roles = Role::where('name', '!=', 'super-admin')
                ->where('name', '!=', 'system-developer')
                ->get();
        }


        return view('archiveuser.create', [
            'title' => trans('general.users'),
            'description' => trans('general.create_account'),
            'roles' => $roles,
            'universities' => ['-1' => trans('general.all_options')] + University::pluck('name', 'id')->toArray(),


        ]);
    }

    public function store(Request $request)
    {
        // Setting the university_id to -1
       // $request->merge(['university_id' => '-1']);


        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'type' => 'required|in:2',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'active' => 'nullable|boolean',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
           // 'university_id' => 'required|integer|min:-1' // Adding validation for university_id
        ]);

        $user = User::create([
            'name' => $request->name,
            'position' => $request->position,
            'phone' => $request->phone,
            'type' => $request->type,
            'email' => $request->email,
            'password' => ($request->password),
            'active' => $request->active ? 1 : 0,
            //'university_id' => $request->university_id // Adding university_id to user creation
            'university_id' => $request->university_id ?? null,
        ]);

        $user->roles()->sync($request->roles);

        return redirect()->route('archiveuser.index')->with('success', 'User created successfully!');
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */



    public function edit($id)
    {
        $user = User::findOrFail($id);
        $university_id= auth()->user()->university_id;

        $roles = [];
        if (auth()->user()->hasRole('system-developer')) {
            $roles = Role::where('archive_type', 2)->get();
        } else {
            $roles = Role::where('name', '!=', 'super-admin')
                ->where('name', '!=', 'system-developer')
                ->get();
        }

        return view('archiveuser.edit', [
            'title' => trans('general.users'),
            'description' => trans('general.edit_account'),
            'user' => $user,
            'roles' => $roles,
            'universities' => ['-1' => trans('general.all_options')] + University::pluck('name', 'id')->toArray(),
        ]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'type' => 'required|in:2',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'active' => 'nullable|boolean',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->name,
            'position' => $request->position,
            'phone' => $request->phone,
            'university_id' => $request->university_id ?? null,
            'type' => $request->type,
            'email' => $request->email,
            'password' => $request->password ? ($request->password) : null,
            'active' => $request->active ? 1 : 0,
        ]);

        $user->roles()->sync($request->roles);

        return redirect()->route('archiveuser.index')->with('success', 'User updated successfully!');
    }





    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Find the user by ID or fail
        $user = User::findOrFail($id);

        // Delete the user
        $user->delete();

        // Redirect back to the index route with a success message
        return redirect()->route('archiveuser.index')->with('success', 'User deleted successfully!');
    }





}


  

    

