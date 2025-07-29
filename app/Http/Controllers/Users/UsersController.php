<?php

namespace App\Http\Controllers\Users;

use App\User;
use App\Models\Role;
use App\Models\Grade;
use App\Models\University;
use App\Models\Department;
use App\Models\ArchiveDepartment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\DataTables\UsersDataTable;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function __construct()
    {        
        $this->middleware('permission:view-user', ['only' => ['index', 'show']]);        
        $this->middleware('permission:create-user', ['only' => ['create','store']]);
        $this->middleware('permission:edit-user', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-user', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UsersDataTable $dataTable)
    {         
        return $dataTable->render('users.index', [
            'title' => trans('general.users'),
            'description' => trans('general.users_list') 
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $roles = array();
        $departments = [];
        $user_types = array( 1  => 'سطح وزارت - همه پوهنتون ها',
        2 => 'سطح وزارت - چند پوهنتون',
        3 => 'سطح پوهنتون');

     
        if (auth()->user()->hasRole('system-developer')) {
            $roles = Role::all();
        }
        elseif(auth()->user()->hasRole('super-admin')) {
            // For super-admin with user_type = 1 (ministry level)
            if (auth()->user()->user_type == 1) {
                $roles = Role::where('archive_type', '<>', 2)
                 ->whereNotIn('name', ['system-developer'])
                 ->get();
            }
            // For regular super-admin
            else {
                $roles = Role::where('archive_type', '<>', 2)
                        ->whereNotIn('name', ['system-developer'])
                        ->get();
            }
        }
        else {
            $roles = Role::where('name', '!=', 'super-admin')
                    ->where('name', '!=', 'system-developer')
                    ->get();
        }  


        $grades = Grade::pluck('name', 'id');
        // dd($user_types);

        return view('users.create', [
            'title' => trans('general.users'),
            'description' => trans('general.create_account'),
            'roles' => $roles,            
            'universities' => ['-1' => trans('general.all_options')] + University::pluck('name', 'id')->toArray(),
            'departments' => $departments,
            'grades' => $grades,
            'user_types' => $user_types
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
            'position' => 'required',            
            'email' => 'required|email|unique:users',
            'phone' => 'nullable',
            'user_type' => 'required|in:1,2,3',
            'university_id' => 'nullable',
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:8',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ]            
        ]);
        
        \DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'position' => $request->position,
                'email' => $request->email,
                'phone' => $request->phone,
                'university_id' =>  null,
                'password' => $request->password ?? null,
                'active' => $request->has('active'),
                'user_type' => $request->user_type,
            ]);

            $user->universities()->sync($request->university_id ?? []);

            $user->roles()->sync($request->roles ?? []);

            $user->departments()->sync($request->departments ?? []); 
            
            $user->grades()->sync($request->grades ?? []); 
        });

        return redirect(route('users.index'));
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
    public function edit($user)
    {    
        $roles = array();
        $user_types = array( 1  => 'سطح وزارت - همه پوهنتون ها',
        2 => 'سطح وزارت - چند پوهنتون',
        3 => 'سطح پوهنتون');

        $universityIds = $user->universities()->pluck('universities.id');
        
        $departments = Department::leftJoin('department_types', 'department_types.id', '=', 'departments.department_type_id')
                            ->leftJoin('faculties', 'faculties.id', '=', 'departments.faculty_id')
                            ->select('departments.id',\DB::raw('CONCAT(departments.name, " [ پوهنځی : ", faculties.name , "] (", department_types.name,")") as text'))
                            ->whereIn('departments.university_id',$universityIds)
                            ->pluck('text', 'id')
                            ;
        
        if (auth()->user()->hasRole('system-developer')) {
            $roles = Role::all();
        }
        else
        {
            $roles = Role::where('name','!=','super-admin')->where('name','!=','system-developer')
            ->get();
        }  

        return view('users.edit', [
            'title' => trans('general.users'),
            'description' => trans('general.edit_account'),
            'user' => $user,
            'roles' => $roles,
            'universities' => ['-1' => trans('general.all_options')] + University::pluck('name', 'id')->toArray(),
            'departments' => $departments,
            'grades' => Grade::pluck('name', 'id'),
            'user_types' => $user_types,
            'universityIds' => $universityIds,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user)
    {        
        $validatedData = $request->validate([
            'name' => 'required',
            'position' => 'required',  
            'user_type' => 'required|in:1,2,3',          
            'email' => [
                'required', 
                Rule::unique('users')->ignore($user->id, 'id')->whereNull('deleted_at')
            ],
            'phone' => 'nullable',
            'university_id' => 'nullable',
            'password' => [
                'nullable',
                'confirmed',
                'string',
                'min:8',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ]           
        ]);
        
        \DB::transaction(function () use ($user, $request) {
            $user->update([
                'name' => $request->name,
                'position' => $request->position,
                'email' => $request->email,
                'phone' => $request->phone,
                'university_id' =>  null,
                'password' => $request->password ?? null,
                'active' => $request->has('active'),
                'user_type' => $request->user_type,
            ]);

            $user->universities()->sync($request->university_id ?? []);
            
            $user->roles()->sync($request->roles ?? []);
            
            $user->departments()->sync($request->departments ?? []);

            $user->grades()->sync($request->grades ?? []);
        });

        return redirect(route('users.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($user)
    {
        $user->delete();

        return redirect(route('users.index'));
    }

    public function recover($id)
    {
        $user=User::where('id',$id)->withTrashed()->first();
        $user->restore();
        return redirect(route('users.index'))->with('message', 'یوزر '.$user->name.' موفقانه ریکاور شد.');
    }

    public function editStatus($id)
    {   
        $user=User::where('id',$id)->first(); 
        if (auth()->user()->hasRole('super-admin')) {
            return view('users.status-form', [
                'title' => trans('general.users'),
                'description' => trans('general.edit_status'),
                'user' => $user,
            ]);
        }
        
    }

    public function updateStatus(Request $request, $id)
    {   
        $user=User::where('id',$id)->first();     
        \DB::transaction(function () use ($user, $request) {
            $user->update([
                'active' => $request->has('active')
            ]);
        });

        return redirect(route('users.index'))->with('message', 'یوزر '.$user->name.' موفقانه تصحیح شد.');
    }
}
