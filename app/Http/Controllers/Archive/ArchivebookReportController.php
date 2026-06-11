<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\ArchiveDepartment;
use App\Models\ArchiveYear;
use App\Models\University;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Grade;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArchivebookReportController extends Controller
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
        
        // Get archive years for filter
        $archiveYears = ArchiveYear::orderBy('year')->pluck('year', 'id');
        
        // Get faculties, departments, grades, provinces for filters
        $faculties = Faculty::pluck('name', 'id');
        $departments = Department::pluck('name', 'id');
        $grades = Grade::pluck('name', 'id');
        $provinces = Province::pluck('name', 'id');

        // Base query
        $query = Archive::query()
            ->whereNull('deleted_at')
            ->whereIn('status_id', [1, 2, 3, 4])
            ->with([
                'university.province',
                'archiveYears'
            ]);

        // Apply filters
        if ($user_university_id > 0) {
            $query->where('university_id', $user_university_id);
        }
        
        if ($request->filled('university_id')) {
            $query->where('university_id', $request->university_id);
        }

        if ($request->filled('province_id')) {
            $query->whereHas('university', function($q) use ($request) {
                $q->where('province_id', $request->province_id);
            });
        }

        if ($request->filled('archive_year_id')) {
            $query->whereHas('archiveYears', 
                fn($q) => $q->where('archiveyears.id', $request->archive_year_id));
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'complete'    => $query->where('status_id', '=', 4),
                'registered'  => $query->where('status_id', '=', 1),
                'in_progress' => $query->whereIn('status_id', [2, 3]),
                default       => null,
            };
        }

        // Faculty filter
        if ($request->filled('faculty_id')) {
            $query->whereHas('archiveDepartments', function($q) use ($request) {
                $q->where('faculty_id', $request->faculty_id);
            });
        }

        // Department filter
        if ($request->filled('department_id')) {
            $query->whereHas('archiveDepartments', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Grade filter
        if ($request->filled('grade_id')) {
            $query->whereHas('archiveDepartments.department', function($q) use ($request) {
                $q->where('grade_id', $request->grade_id);
            });
        }

        // Get archives
        $archives = $query->get();

        // Get archive IDs
        $archiveIds = $archives->pluck('id')->toArray();
        
        // Get archive departments
        $archiveDepartments = ArchiveDepartment::with([
            'faculty',
            'department.grade'
        ])
        ->whereIn('archive_id', $archiveIds)
        ->get()
        ->groupBy('archive_id');

        // Calculate totals by unique ID
        $uniqueArchiveIds = [];
        $totalComplete = 0;
        
        foreach ($archives as $archive) {
            if (!in_array($archive->id, $uniqueArchiveIds)) {
                $uniqueArchiveIds[] = $archive->id;
                if ($archive->status_id == 4) {
                    $totalComplete++;
                }
            }
        }
        
        $totalBooks = count($uniqueArchiveIds);
        $totalRemaining = $totalBooks - $totalComplete;

        // Build report rows
        $reportRows = [];
        
        foreach ($archives as $archive) {
            $univName = $archive->university->name ?? 'نامشخص';
            $univProvince = $archive->university->province->name ?? 'نامشخص';
            
            $depts = $archiveDepartments->get($archive->id);
            $isComplete = $archive->status_id == 4;
            
            if ($depts && $depts->isNotEmpty()) {
                foreach ($depts as $dept) {
                    $facultyName = $dept->faculty->name ?? '—';
                    $departmentName = $dept->department->name ?? '—';
                    $gradeName = $dept->department->grade->name ?? '—';
                    
                    $groupKey = $univName . '_' . $univProvince . '_' . $facultyName . '_' . $departmentName . '_' . $gradeName;
                    
                    if (!isset($reportRows[$groupKey])) {
                        $reportRows[$groupKey] = [
                            'university_id' => $archive->university_id,
                            'university_name' => $univName,
                            'province' => $univProvince,
                            'faculty' => $facultyName,
                            'department' => $departmentName,
                            'grade' => $gradeName,
                            'total_books' => 0,
                            'complete' => 0,
                            'archive_ids' => [],
                        ];
                    }
                    
                    if (!in_array($archive->id, $reportRows[$groupKey]['archive_ids'])) {
                        $reportRows[$groupKey]['archive_ids'][] = $archive->id;
                        $reportRows[$groupKey]['total_books']++;
                        if ($isComplete) {
                            $reportRows[$groupKey]['complete']++;
                        }
                    }
                }
            } else {
                if (!$request->filled('faculty_id') && !$request->filled('department_id') && !$request->filled('grade_id')) {
                    $facultyName = 'بدون پوهنځی';
                    $departmentName = 'بدون دیپارتمنت';
                    $gradeName = '—';
                    
                    $groupKey = $univName . '_' . $univProvince . '_NO_DEPT';
                    
                    if (!isset($reportRows[$groupKey])) {
                        $reportRows[$groupKey] = [
                            'university_id' => $archive->university_id,
                            'university_name' => $univName,
                            'province' => $univProvince,
                            'faculty' => $facultyName,
                            'department' => $departmentName,
                            'grade' => $gradeName,
                            'total_books' => 0,
                            'complete' => 0,
                            'archive_ids' => [],
                        ];
                    }
                    
                    if (!in_array($archive->id, $reportRows[$groupKey]['archive_ids'])) {
                        $reportRows[$groupKey]['archive_ids'][] = $archive->id;
                        $reportRows[$groupKey]['total_books']++;
                        if ($isComplete) {
                            $reportRows[$groupKey]['complete']++;
                        }
                    }
                }
            }
        }

        // Final processing
        $reportRows = collect($reportRows)
            ->sortBy(['university_name', 'province', 'faculty', 'department', 'grade'])
            ->values()
            ->map(function ($row) {
                unset($row['archive_ids']);
                $row['remaining'] = $row['total_books'] - $row['complete'];
                return $row;
            });

        $totals = [
            'total_books' => $reportRows->sum('total_books'),
            'complete' => $reportRows->sum('complete'),
            'remaining' => $reportRows->sum('remaining'),
        ];

        $cardTotals = [
            'total_books' => $totalBooks,
            'complete' => $totalComplete,
            'remaining' => $totalRemaining,
        ];

        // ==============================================
        // داده‌های وابسته برای فیلترهای آبشاری
        // ==============================================
        $facultiesWithUniversity = [];
        $facultiesList = DB::table('faculties')->select('id', 'university_id')->get();
        foreach ($facultiesList as $faculty) {
            $facultiesWithUniversity[$faculty->id] = $faculty->university_id;
        }

        $departmentsWithFaculty = [];
        $departmentsList = DB::table('departments')->select('id', 'faculty_id')->get();
        foreach ($departmentsList as $department) {
            $departmentsWithFaculty[$department->id] = $department->faculty_id;
        }

        // تغییر مسیر ویو به archivereport.bookreport
        return view('archivereport.bookreport', compact(
            'reportRows',
            'totals',
            'cardTotals',
            'universitiesList',
            'archiveYears',
            'faculties',
            'departments',
            'grades',
            'provinces',
            'facultiesWithUniversity',
            'departmentsWithFaculty'
        ));
    }
}