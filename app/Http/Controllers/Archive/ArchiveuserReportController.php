<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\ArchiveYear;
use App\Models\University;
use App\Models\Province;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArchiveuserReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-archive');
    }

    public function index(Request $request)
    {
        $user_university_id = auth()->user()->university_id;

        // Get all universities with their type and province
        $universitiesQuery = University::query()->with('province');
        if ($user_university_id > 0) {
            $universitiesQuery->where('id', $user_university_id);
        }
        $universities = $universitiesQuery->get();
        $universitiesList = $universities->pluck('name', 'id');
        
        // Get provinces for filters
        $provinces = Province::pluck('name', 'id');
        
        // Get list of years for dropdown
        $allYears = ArchiveYear::orderBy('year')->pluck('year', 'year');

        // ==============================================
        // گرفتن کتاب‌های فیلتر شده بر اساس سال
        // ==============================================
        $archiveQuery = Archive::query()
            ->whereNull('deleted_at')
            ->whereIn('status_id', [1, 2, 3, 4]);
        
        // Apply university filter
        if ($user_university_id > 0) {
            $archiveQuery->where('university_id', $user_university_id);
        }
        
        if ($request->filled('university_id')) {
            $archiveQuery->where('university_id', $request->university_id);
        }
        
        // Apply province filter
        if ($request->filled('province_id')) {
            $archiveQuery->whereHas('university', function($q) use ($request) {
                $q->where('province_id', $request->province_id);
            });
        }
        
        // Apply year filters - استفاده از whereHas با relationship درست
        if ($request->filled('from_year')) {
            $archiveQuery->whereHas('archiveYears', function($q) use ($request) {
                $q->where('year', '>=', $request->from_year);
            });
        }
        
        if ($request->filled('to_year')) {
            $archiveQuery->whereHas('archiveYears', function($q) use ($request) {
                $q->where('year', '<=', $request->to_year);
            });
        }
        
        $archives = $archiveQuery->get();
        
        // گرفتن لیست archive_id های فیلتر شده
        $archiveIds = $archives->pluck('id')->toArray();
        
        // ==============================================
        // محاسبه آمار برای هر کاربر
        // ==============================================
        $allUsers = User::where('type', 2)->get();
        
        $usersData = [];
        foreach ($allUsers as $user) {
            // تعداد کتاب‌هایی که این کاربر به عنوان درج کننده ثبت کرده است
            $booksAsInserter = $archives->where('de_user_id', $user->id)->count();
            
            // تعداد کتاب‌هایی که این کاربر به عنوان کنترول کننده بررسی کرده است
            $booksAsController = $archives->where('qc_user_id', $user->id)->count();
            
            $role = '';
            $booksCount = 0;
            $studentsCount = 0;
            
            // شمارش محصلان برای درج کننده - استفاده از query ساده
            $studentsCountInserter = 0;
            if ($booksAsInserter > 0 && !empty($archiveIds)) {
                $studentsCountInserter = DB::table('archivedatas')
                    ->join('archives', 'archivedatas.archive_id', '=', 'archives.id')
                    ->where('archives.de_user_id', $user->id)
                    ->whereIn('archives.id', $archiveIds)
                    ->count();
            }
            
            // شمارش محصلان برای کنترول کننده
            $studentsCountController = 0;
            if ($booksAsController > 0 && !empty($archiveIds)) {
                $studentsCountController = DB::table('archivedatas')
                    ->join('archives', 'archivedatas.archive_id', '=', 'archives.id')
                    ->where('archives.qc_user_id', $user->id)
                    ->whereIn('archives.id', $archiveIds)
                    ->count();
            }
            
            if ($booksAsInserter > 0 && $booksAsController > 0) {
                // اگر کاربر هم درج کننده و هم کنترول کننده است
                $role = 'درج کننده / کنترول کننده';
                $booksCount = $booksAsInserter + $booksAsController;
                $studentsCount = $studentsCountInserter + $studentsCountController;
                
            } elseif ($booksAsInserter > 0) {
                // فقط درج کننده
                $role = 'درج کننده';
                $booksCount = $booksAsInserter;
                $studentsCount = $studentsCountInserter;
                
            } elseif ($booksAsController > 0) {
                // فقط کنترول کننده
                $role = 'کنترول کننده';
                $booksCount = $booksAsController;
                $studentsCount = $studentsCountController;
                
            } else {
                // بدون فعالیت
                $role = 'بدون فعالیت';
                $booksCount = 0;
                $studentsCount = 0;
            }
            
            $usersData[$user->id] = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'role' => $role,
                'books_count' => $booksCount,
                'students_count' => $studentsCount,
            ];
        }
        
        // ==============================================
        // فیلتر بر اساس نقش و یوزر
        // ==============================================
        $filterRole = $request->input('filter_role');
        $filterUserId = $request->input('filter_user_id');
        
        $reportRows = collect($usersData);
        
        // فیلتر بر اساس نقش
        if ($filterRole && $filterRole != 'all') {
            $reportRows = $reportRows->filter(function($user) use ($filterRole) {
                if ($filterRole == 'inserter') {
                    return $user['role'] == 'درج کننده' || $user['role'] == 'درج کننده / کنترول کننده';
                } elseif ($filterRole == 'controller') {
                    return $user['role'] == 'کنترول کننده' || $user['role'] == 'درج کننده / کنترول کننده';
                }
                return true;
            });
        }
        
        // فیلتر بر اساس یوزر خاص
        if ($filterUserId && $filterUserId != 'all') {
            $reportRows = $reportRows->filter(function($user) use ($filterUserId) {
                return $user['user_id'] == $filterUserId;
            });
        }
        
        // مرتب‌سازی بر اساس نام یوزر
        $reportRows = $reportRows->sortBy('user_name')->values();
        
        // ==============================================
        // تهیه لیست یوزرها برای فیلتر دوم
        // ==============================================
        $userListForFilter = collect($usersData);
        
        if ($filterRole && $filterRole != 'all') {
            $userListForFilter = $userListForFilter->filter(function($user) use ($filterRole) {
                if ($filterRole == 'inserter') {
                    return $user['role'] == 'درج کننده' || $user['role'] == 'درج کننده / کنترول کننده';
                } elseif ($filterRole == 'controller') {
                    return $user['role'] == 'کنترول کننده' || $user['role'] == 'درج کننده / کنترول کننده';
                }
                return true;
            });
        }
        
        $userListForFilter = $userListForFilter->sortBy('user_name')->pluck('user_name', 'user_id');
        
        // ==============================================
        // محاسبه مجموع کل
        // ==============================================
        $totals = [
            'total_users' => $reportRows->count(),
            'total_books' => $reportRows->sum('books_count'),
            'total_inserters' => $reportRows->filter(function($user) {
                return $user['role'] == 'درج کننده' || $user['role'] == 'درج کننده / کنترول کننده';
            })->count(),
            'total_controllers' => $reportRows->filter(function($user) {
                return $user['role'] == 'کنترول کننده' || $user['role'] == 'درج کننده / کنترول کننده';
            })->count(),
        ];
        
        // ==============================================
        // گزارش بر اساس پوهنتون
        // ==============================================
        $universityReport = [];
        $universityGroups = $archives->groupBy('university_id');
        
        foreach ($universityGroups as $universityId => $universityArchives) {
            $university = $universityArchives->first()->university;
            if ($university) {
                $universityReport[] = [
                    'university_name' => $university->name,
                    'province' => $university->province->name ?? 'نامشخص',
                    'total_books' => $universityArchives->count(),
                    'inserters_count' => $universityArchives->whereNotNull('de_user_id')->count(),
                    'controllers_count' => $universityArchives->whereNotNull('qc_user_id')->count(),
                ];
            }
        }
        
        $universityTotals = [
            'total_books' => collect($universityReport)->sum('total_books'),
            'total_inserters' => collect($universityReport)->sum('inserters_count'),
            'total_controllers' => collect($universityReport)->sum('controllers_count'),
        ];
        
        return view('archivereport.userreport', compact(
            'reportRows',
            'totals',
            'universityReport',
            'universityTotals',
            'universitiesList',
            'provinces',
            'allYears',
            'userListForFilter',
            'filterRole',
            'filterUserId',
            'request'
        ));
    }
    
    public function exportExcel(Request $request)
    {
        // دریافت داده‌ها با همان فیلترها
        $user_university_id = auth()->user()->university_id;
        
        $archiveQuery = Archive::query()
            ->whereNull('deleted_at')
            ->whereIn('status_id', [1, 2, 3, 4]);
        
        if ($user_university_id > 0) {
            $archiveQuery->where('university_id', $user_university_id);
        }
        
        if ($request->filled('university_id')) {
            $archiveQuery->where('university_id', $request->university_id);
        }
        
        if ($request->filled('province_id')) {
            $archiveQuery->whereHas('university', fn($q) => $q->where('province_id', $request->province_id));
        }
        
        if ($request->filled('from_year')) {
            $archiveQuery->whereHas('archiveYears', fn($q) => $q->where('year', '>=', $request->from_year));
        }
        
        if ($request->filled('to_year')) {
            $archiveQuery->whereHas('archiveYears', fn($q) => $q->where('year', '<=', $request->to_year));
        }
        
        $archives = $archiveQuery->get();
        $archiveIds = $archives->pluck('id')->toArray();
        
        $allUsers = User::where('type', 2)->get();
        $usersData = [];
        
        foreach ($allUsers as $user) {
            $booksAsInserter = $archives->where('de_user_id', $user->id)->count();
            $booksAsController = $archives->where('qc_user_id', $user->id)->count();
            
            $role = '';
            $booksCount = 0;
            
            if ($booksAsInserter > 0 && $booksAsController > 0) {
                $role = 'درج کننده / کنترول کننده';
                $booksCount = $booksAsInserter + $booksAsController;
            } elseif ($booksAsInserter > 0) {
                $role = 'درج کننده';
                $booksCount = $booksAsInserter;
            } elseif ($booksAsController > 0) {
                $role = 'کنترول کننده';
                $booksCount = $booksAsController;
            } else {
                $role = 'بدون فعالیت';
                $booksCount = 0;
            }
            
            $usersData[$user->id] = [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role,
                'books_count' => $booksCount,
            ];
        }
        
        // اعمال فیلترها
        $filterRole = $request->input('filter_role');
        $filterUserId = $request->input('filter_user_id');
        
        $filteredData = collect($usersData);
        
        if ($filterRole && $filterRole != 'all') {
            $filteredData = $filteredData->filter(function($user) use ($filterRole) {
                if ($filterRole == 'inserter') {
                    return $user['role'] == 'درج کننده' || $user['role'] == 'درج کننده / کنترول کننده';
                } elseif ($filterRole == 'controller') {
                    return $user['role'] == 'کنترول کننده' || $user['role'] == 'درج کننده / کنترول کننده';
                }
                return true;
            });
        }
        
        if ($filterUserId && $filterUserId != 'all') {
            $filteredData = $filteredData->filter(function($user, $id) use ($filterUserId) {
                return $id == $filterUserId;
            });
        }
        
        // تولید CSV
        $csvFileName = 'archive_user_report_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');
        fputs($handle, "\xEF\xBB\xBF");
        
        fputcsv($handle, ['شماره', 'نام یوزر', 'ایمیل', 'نقش', 'تعداد کتاب‌ها']);
        
        $rowNumber = 1;
        foreach ($filteredData as $data) {
            fputcsv($handle, [
                $rowNumber++,
                $data['name'],
                $data['email'],
                $data['role'],
                $data['books_count'],
            ]);
        }
        
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);
        
        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$csvFileName\"",
        ]);
    }
    
    public function exportPdf(Request $request)
    {
        return $this->index($request);
    }
    
    public function getUsersByRole(Request $request)
    {
        $role = $request->input('role');
        $universityId = $request->input('university_id');
        $provinceId = $request->input('province_id');
        $fromYear = $request->input('from_year');
        $toYear = $request->input('to_year');
        
        // دریافت کتاب‌های فیلتر شده
        $archiveQuery = Archive::query()
            ->whereNull('deleted_at')
            ->whereIn('status_id', [1, 2, 3, 4]);
        
        if ($universityId) {
            $archiveQuery->where('university_id', $universityId);
        }
        
        if ($provinceId) {
            $archiveQuery->whereHas('university', fn($q) => $q->where('province_id', $provinceId));
        }
        
        if ($fromYear) {
            $archiveQuery->whereHas('archiveYears', fn($q) => $q->where('year', '>=', $fromYear));
        }
        
        if ($toYear) {
            $archiveQuery->whereHas('archiveYears', fn($q) => $q->where('year', '<=', $toYear));
        }
        
        $archives = $archiveQuery->get();
        $allUsers = User::where('type', 2)->get();
        
        $usersData = [];
        foreach ($allUsers as $user) {
            $booksAsInserter = $archives->where('de_user_id', $user->id)->count();
            $booksAsController = $archives->where('qc_user_id', $user->id)->count();
            
            $userRole = '';
            if ($booksAsInserter > 0 && $booksAsController > 0) {
                $userRole = 'both';
            } elseif ($booksAsInserter > 0) {
                $userRole = 'inserter';
            } elseif ($booksAsController > 0) {
                $userRole = 'controller';
            } else {
                $userRole = 'none';
            }
            
            // فیلتر بر اساس نقش انتخاب شده
            if ($role == 'all') {
                $usersData[$user->id] = $user->name;
            } elseif ($role == 'inserter' && ($userRole == 'inserter' || $userRole == 'both')) {
                $usersData[$user->id] = $user->name;
            } elseif ($role == 'controller' && ($userRole == 'controller' || $userRole == 'both')) {
                $usersData[$user->id] = $user->name;
            }
        }
        
        return response()->json(['users' => $usersData]);
    }
}