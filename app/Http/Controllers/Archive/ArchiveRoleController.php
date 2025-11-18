<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\University;
use App\Models\ArchiveRole;
use App\Models\ModalHasRole;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;

class ArchiveRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-archive', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-archive', ['only' => ['create','store']]);
        $this->middleware('permission:edit-archive', ['only' => ['edit','update', 'updateStatus','update_groups']]);
        $this->middleware('permission:delete-archive', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        \Carbon\Carbon::setLocale('fa');
        $query = ArchiveRole::with(['user', 'archive', 'archivedatastatus', 'archiveqcstatus', 'role']);

        // Check if there's a search query
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;

            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })
                ->orWhereHas('archive', function ($q) use ($search) {
                    $q->where('book_name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('archive', function ($q) use ($search) {
                    $q->whereHas('university', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
                })
                ->orWhereHas('role', function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%');
                });
                
        }

        $archiveRoles = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('archiverole.index', compact('archiveRoles'));
    }



    public function create()
    {
        
        

        $universities = University::pluck('name', 'id');

        // Fetch users and archives
        $archiveUsers = User::where('type', 2)->select('id', 'name')->get();
        $archives = Archive::select('id', 'book_name')->get();
      
        // Fetch roles where archive_type is 2
        $archiveRoles = Role::where('archive_type', 2)
            ->select('id', 'title')
            ->get();

        // Return the view with the fetched data
        return view('archiverole.create', compact('archiveUsers', 'archives', 'archiveRoles','universities'));
    }



    public function archiveUserRoleLoad($roleId = null)
    {
        // Fetch user IDs associated with the given role ID
        $userIdList = ModalHasRole::where('role_id', $roleId)->pluck('model_id')->all();

        // Fetch users based on the retrieved user IDs
        return User::whereIn('id', $userIdList)->select('id', 'name as text')->get();
    }


    public function archiveBookRoleLoad($university_id,$role_id)
    {

        $role = Role::where('id', $role_id)->first();
        if ($role->name == 'quality_Control') {

            $archives = Archive::where('status_id', 4)
            ->where('qc_user_id', null)
            ->where('university_id',$university_id)
                ->select('id', 'book_name as text')
                ->get();

        } elseif ($role->name == 'Data_Entry') {
            $archives = Archive::where('status_id', 1)
            ->where('university_id',$university_id)
            ->where('de_user_id', null)
                ->select('id', 'book_name as text')
                ->get();
        }

        return $archives;
    }


    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'user_id' => 'required|exists:users,id',
            'archive_id' => 'required|exists:archives,id',
        ]);

//        dd($request);
        //insert tin to archive role
        $archiveRole = ArchiveRole::create([
            'archive_id' => $request->archive_id,
            'role_id' => $request->role_id,
            'user_id' => $request->user_id
        ]);

        // update to archive table
        $archive = Archive::find($request->archive_id);
        $role = Role::where('id', $request->role_id)->first();
        if ($role->name == 'Data_Entry') {
            $archive->update([
                'de_user_id' => $request->user_id
            ]);
            $archive->save();

        } elseif ($role->name == 'quality_Control') {
            $archive->update([
                'qc_user_id' => $request->user_id
            ]);
            $archive->save();
        }
        
        return redirect()->route('archiverole.index')->with('success', 'وظایف موفقانه سپرده شد!');
    }

    public function edit($id)
    {
        $archiveRole = ArchiveRole::with(['user', 'archive.university', 'role'])->findOrFail($id);
        $universities = University::pluck('name', 'id');
        // Fetch users and archives
        $archiveUsers = User::where('type', 2)->select('id', 'name')->get();
        // $archives = Archive::select('id', 'book_name')->get();
        $archives = Archive::pluck('book_name', 'id');
        // Fetch roles where archive_type is 2
        $archiveRoles = Role::where('archive_type', 2)
            ->select('id', 'title')
            ->get();

        // Return the edit view with the fetched data
        return view('archiverole.edit', compact('archiveRole', 'archiveUsers', 'archives', 'archiveRoles','universities'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'user_id' => 'required|exists:users,id',
            'archive_id' => 'required|exists:archives,id',
        ]);

        // Fetch the archive role by ID
        $archiveRole = ArchiveRole::findOrFail($id);

        // Update the archive role
        $archiveRole->update([
            'archive_id' => $request->archive_id,
            'role_id' => $request->role_id,
            'user_id' => $request->user_id
        ]);

        // Update the archive table
        $archive = Archive::find($request->archive_id);
        $role = Role::where('id', $request->role_id)->first();
        if ($role->name == 'Data_Entry') {
            $archive->update([
                'de_user_id' => $request->user_id
            ]);
        } elseif ($role->name == 'quality_Control') {
            $archive->update([
                'qc_user_id' => $request->user_id
            ]);
        }

        // Redirect back with success message
        return redirect()->route('archiverole.index')->with('success', 'وظایف موفقانه سپرده شد');
    }


    public function show($name, $id)
    {
        // Fetch the ArchiveRole model by ID
        $role = ArchiveRole::with(['user', 'archive', 'archivedatastatus', 'archiveqcstatus', 'role'])
            ->find($id);

        // Check if the role exists
        if (!$role) {
            return abort(404, 'وظایف پیدا نشد');
        }

        // Pass the $role variable to the view
        return view('archiverole.show', compact('role'));
    }


    public function destroy($id)
    {
        $archiveRole = ArchiveRole::findOrFail($id);
        $archiveRole->delete();
        return redirect()->route('archiverole.index')->with('success', 'وظایف یوزر آرشیف موفقانه حذف گردید.');
    }
}
