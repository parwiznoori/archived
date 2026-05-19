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
        // $this->middleware('permission:view-archive', ['only' => ['index', 'show']]);
        // $this->middleware('permission:create-archive', ['only' => ['create','store']]);
        // $this->middleware('permission:edit-archive', ['only' => ['edit','update', 'updateStatus','update_groups']]);
        // $this->middleware('permission:delete-archive', ['only' => ['destroy']]);
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



    // public function archiveUserRoleLoad($roleId = null)
    // {
    //     // Fetch user IDs associated with the given role ID
    //     $userIdList = ModalHasRole::where('role_id', $roleId)->pluck('model_id')->all();

    //     // Fetch users based on the retrieved user IDs
    //     return User::whereIn('id', $userIdList)->select('id', 'name as text')->get();
    // }



        public function archiveUserRoleLoad(Request $request, $roleId = null){
            $searchTerm = $request->input('q');
            
            if (!$roleId) {
                return response()->json([]);
            }

            // Fetch user IDs associated with the given role ID
            $userIdList = ModalHasRole::where('role_id', $roleId)->pluck('model_id')->all();

            // Fetch users based on the retrieved user IDs with search filter
            $query = User::whereIn('id', $userIdList);
            
            if ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%');
            }

            return $query->select('id', 'name as text')->get();
        }

    // public function archiveBookRoleLoad($university_id,$role_id)
    // {

    //     $role = Role::where('id', $role_id)->first();
    //     if ($role->name == 'quality_Control') {

    //         $archives = Archive::where('status_id', 4)
    //         ->where('qc_user_id', null)
    //         ->where('university_id',$university_id)
    //             ->select('id', 'book_name as text')
    //             ->get();

    //     } elseif ($role->name == 'Data_Entry') {
    //         $archives = Archive::where('status_id', 1)
    //         ->where('university_id',$university_id)
    //         ->where('de_user_id', null)
    //             ->select('id', 'book_name as text')
    //             ->get();
    //     }

    //     return $archives;
    // }


    
public function archiveBookRoleLoad(Request $request, $university_id = null, $role_id = null)
{
    $q = $request->get('q'); // متن جستجو (اگر فرستاده شد)
    $archives = collect();

    if ($role_id && $university_id) {
        $role = Role::find($role_id);

        if ($role && $role->name === 'quality_Control') {
            $archives = Archive::where('status_id', 4)
                ->whereNull('qc_user_id')
                ->where('university_id', $university_id)
                ->when($q, function ($query) use ($q) {
                    $query->where('book_name', 'like', "%{$q}%");
                })
                ->select('id', 'book_name as text')
                ->orderBy('book_name')
                ->get();

        } elseif ($role && $role->name === 'Data_Entry') {
            $archives = Archive::where('status_id', 1)
                ->where('university_id', $university_id)
                ->whereNull('de_user_id')
                ->when($q, function ($query) use ($q) {
                    $query->where('book_name', 'like', "%{$q}%");
                })
                ->select('id', 'book_name as text')
                ->orderBy('book_name')
                ->get();
        }
    }

    return response()->json($archives);
}


   
/**
 * متد جدید برای بارگذاری چندتایی کتاب‌ها
 */
public function archiveBookRoleLoadMultiple(Request $request, $university_id = null, $role_id = null)
{
    $q = $request->get('q'); // متن جستجو
    $page = $request->get('page', 1); // شماره صفحه برای صفحه‌بندی
    $archives = collect();

    if ($role_id && $university_id) {
        $role = Role::find($role_id);

        if ($role && $role->name === 'quality_Control') {
            $query = Archive::where('status_id', 4)
                ->whereNull('qc_user_id')
                ->where('university_id', $university_id)
                ->when($q, function ($query) use ($q) {
                    $query->where('book_name', 'like', "%{$q}%");
                })
                ->orderBy('book_name');

            // برای صفحه‌بندی در select2
            $total = $query->count();
            $archives = $query->select('id', 'book_name as text')
                ->skip(($page - 1) * 10)
                ->take(10)
                ->get();

        } elseif ($role && $role->name === 'Data_Entry') {
            $query = Archive::where('status_id', 1)
                ->where('university_id', $university_id)
                ->whereNull('de_user_id')
                ->when($q, function ($query) use ($q) {
                    $query->where('book_name', 'like', "%{$q}%");
                })
                ->orderBy('book_name');

            // برای صفحه‌بندی در select2
            $total = $query->count();
            $archives = $query->select('id', 'book_name as text')
                ->skip(($page - 1) * 10)
                ->take(10)
                ->get();
        }
    }

    // فرمت خروجی برای select2 با صفحه‌بندی
    return response()->json([
        'results' => $archives,
        'pagination' => [
            'more' => ($page * 10) < ($total ?? 0)
        ]
    ]);
}

/**
 * به‌روزرسانی متد store برای پشتیبانی از چند کتاب
 */
public function store(Request $request)
{
    // Validate the request
    $request->validate([
        'role_id' => 'required|exists:roles,id',
        'user_id' => 'required|exists:users,id',
        'archive_ids' => 'required|array|min:1',
        'archive_ids.*' => 'exists:archives,id',
    ]);

    $role = Role::where('id', $request->role_id)->first();
    $successCount = 0;
    $failedArchives = [];
    $newAssignments = []; // برای ذخیره اطلاعات تخصیص‌های جدید

    foreach ($request->archive_ids as $archiveId) {
        
        $archive = Archive::find($archiveId);
        
        if ($role->name == 'Data_Entry') {
            // برای وروددهی: اگر de_user_id خالی باشد
            if (is_null($archive->de_user_id)) {
                
                // ایجاد رکورد جدید در archive_entry_users (تاریخچه جدید)
                $archiveRole = ArchiveRole::create([
                    'archive_id' => $archiveId,
                    'role_id' => $request->role_id,
                    'user_id' => $request->user_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // به‌روزرسانی archive - فقط de_user_id را تغییر بده، status_id را تغییر نده
                $archive->update([
                    'de_user_id' => $request->user_id,
                ]);
                
                $successCount++;
                
                // ذخیره اطلاعات برای نمایش
                $newAssignments[] = [
                    'book' => $archive->book_name,
                    'user' => $archiveRole->user->name ?? 'کاربر',
                    'current_status' => $archive->status_id // وضعیت فعلی کتاب
                ];
                
            } else {
                // اگر کتاب به کاربر دیگری تخصیص داده شده بود
                $previousUser = User::find($archive->de_user_id);
                $failedArchives[] = $archive->book_name . ' (به کاربر ' . ($previousUser->name ?? 'نامشخص') . ' تخصیص داده شده)';
            }
        } 
        elseif ($role->name == 'quality_Control') {
            // برای کنترل کیفیت: اگر qc_user_id خالی باشد
            if (is_null($archive->qc_user_id)) {
                
                // ایجاد رکورد جدید در archive_entry_users
                $archiveRole = ArchiveRole::create([
                    'archive_id' => $archiveId,
                    'role_id' => $request->role_id,
                    'user_id' => $request->user_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // به‌روزرسانی archive - فقط qc_user_id را تغییر بده
                $archive->update([
                    'qc_user_id' => $request->user_id,
                ]);
                
                $successCount++;
                
                $newAssignments[] = [
                    'book' => $archive->book_name,
                    'user' => $archiveRole->user->name ?? 'کاربر',
                    'current_status' => $archive->status_id,
                    'current_qc_status' => $archive->qc_status_id
                ];
                
            } else {
                $previousUser = User::find($archive->qc_user_id);
                $failedArchives[] = $archive->book_name . ' (به کاربر ' . ($previousUser->name ?? 'نامشخص') . ' تخصیص داده شده)';
            }
        }
    }

    // نمایش پیام مناسب
    if ($successCount > 0) {
        $message = "✅ تعداد {$successCount} کتاب با موفقیت تخصیص داده شد:";
        foreach ($newAssignments as $item) {
            $message .= "📚 {$item['book']} 👤 {$item['user']}";
        }
        
        if (!empty($failedArchives)) {
            $message .= "⚠️ موارد زیر تخصیص داده نشد:" . implode("", $failedArchives);
        }
        
        return redirect()->route('archiverole.index')
            ->with('success', nl2br($message))
            ->with('new_assignments', $newAssignments)
            ->with('failed', $failedArchives);
            
    } else {
        return redirect()->back()
            ->with('error', '❌ هیچ کتابی تخصیص داده نشد.' . implode("", $failedArchives));
    }
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
        'archive_ids' => 'required|array|min:1',
        'archive_ids.*' => 'exists:archives,id',
    ]);

    // دریافت رکورد فعلی
    $currentArchiveRole = ArchiveRole::findOrFail($id);
    
    $role = Role::where('id', $request->role_id)->first();
    $successCount = 0;
    $failedArchives = [];
    $newAssignments = [];

    foreach ($request->archive_ids as $archiveId) {
        
        $archive = Archive::find($archiveId);
        
        if ($role->name == 'Data_Entry') {
            // بررسی می‌کنیم که آیا این کتاب قبلاً به این کاربر تخصیص داده شده یا نه
            $existingAssignment = ArchiveRole::where([
                'archive_id' => $archiveId,
                'role_id' => $request->role_id,
                'user_id' => $request->user_id
            ])->exists();
            
            // اگر قبلاً تخصیص داده نشده و de_user_id خالی است
            if (!$existingAssignment && is_null($archive->de_user_id)) {
                
                // ایجاد رکورد جدید
                $archiveRole = ArchiveRole::create([
                    'archive_id' => $archiveId,
                    'role_id' => $request->role_id,
                    'user_id' => $request->user_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // فقط de_user_id را به‌روزرسانی کن
                $archive->update([
                    'de_user_id' => $request->user_id
                ]);
                
                $successCount++;
                
                $newAssignments[] = [
                    'book' => $archive->book_name,
                    'user' => $archiveRole->user->name ?? 'کاربر'
                ];
                
            } else {
                if (!is_null($archive->de_user_id)) {
                    $previousUser = User::find($archive->de_user_id);
                    $failedArchives[] = $archive->book_name . ' (به کاربر ' . ($previousUser->name ?? 'نامشخص') . ' تخصیص داده شده)';
                } else {
                    $failedArchives[] = $archive->book_name . ' (قبلاً تخصیص داده شده بود)';
                }
            }
        } 
        elseif ($role->name == 'quality_Control') {
            // بررسی تخصیص قبلی
            $existingAssignment = ArchiveRole::where([
                'archive_id' => $archiveId,
                'role_id' => $request->role_id,
                'user_id' => $request->user_id
            ])->exists();
            
            if (!$existingAssignment && is_null($archive->qc_user_id)) {
                
                $archiveRole = ArchiveRole::create([
                    'archive_id' => $archiveId,
                    'role_id' => $request->role_id,
                    'user_id' => $request->user_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $archive->update([
                    'qc_user_id' => $request->user_id
                ]);
                
                $successCount++;
                
                $newAssignments[] = [
                    'book' => $archive->book_name,
                    'user' => $archiveRole->user->name ?? 'کاربر'
                ];
                
            } else {
                if (!is_null($archive->qc_user_id)) {
                    $previousUser = User::find($archive->qc_user_id);
                    $failedArchives[] = $archive->book_name . ' (به کاربر ' . ($previousUser->name ?? 'نامشخص') . ' تخصیص داده شده)';
                } else {
                    $failedArchives[] = $archive->book_name . ' (قبلاً تخصیص داده شده بود)';
                }
            }
        }
    }

    // اگر تخصیص جدیدی انجام شده، رکورد قبلی را حذف نمی‌کنیم (تاریخچه نگه داشته می‌شود)
    // فقط اگر تخصیص جدیدی انجام نشده بود، پیام خطا نشان می‌دهیم

    if ($successCount > 0) {
        $message = "✅ تعداد {$successCount} کتاب با موفقیت تخصیص داده شد:";
        foreach ($newAssignments as $item) {
            $message .= "📚 {$item['book']} 👤 {$item['user']}";
        }
        
        if (!empty($failedArchives)) {
            $message .= "⚠️ موارد زیر تخصیص داده نشد:" . implode("", $failedArchives);
        }
        
        return redirect()->route('archiverole.index')->with('success', nl2br($message));
        
    } else {
        return redirect()->back()->with('error', '❌ هیچ کتابی تخصیص داده نشد.' . implode("", $failedArchives));
    }
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
