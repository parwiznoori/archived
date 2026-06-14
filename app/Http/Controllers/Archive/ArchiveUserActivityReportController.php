<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Archivedata;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian; // اضافه کردن این خط

class ArchiveUserActivityReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-archive');
    }

    /**
     * تبدیل تاریخ میلادی به شمسی با استفاده از پکیج morilog/jalali
     */
    private function toShamsi($date, bool $withTime = true): string
    {
        if (empty($date)) {
            return '—';
        }
        
        try {
            // تبدیل به Carbon
            $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
            
            // بررسی اعتبار تاریخ
            if (!$carbon->isValid()) {
                return '—';
            }
            
            // تبدیل به تاریخ شمسی با استفاده از پکیج
            $jalali = Jalalian::fromCarbon($carbon);
            
            if ($withTime) {
                return $jalali->format('Y/m/d H:i');
            } else {
                return $jalali->format('Y/m/d');
            }
            
        } catch (\Exception $e) {
            \Log::error('Date conversion error: ' . $e->getMessage());
            return '—';
        }
    }
    
    /**
     * گزارش درج‌کنندگان (de_user_id)
     */
    private function getDeUserReport(array $dateRange, ?string $specificUserId, int $authUniversityId): array
    {
        $query = Archive::whereNull('deleted_at')
            ->whereNotNull('de_user_id')
            ->with(['archivedatas' => function($q) use ($dateRange) {
                if ($dateRange['start'] && $dateRange['end']) {
                    $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                }
            }]);
        
        // اعمال فیلتر تاریخ
        if ($dateRange['start'] && $dateRange['end']) {
            $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        }
        
        if ($authUniversityId > 0) {
            $query->where('university_id', $authUniversityId);
        }
        
        if ($specificUserId) {
            $query->where('de_user_id', $specificUserId);
        }
        
        $archives = $query->get();
        
        $userIds = $archives->pluck('de_user_id')->unique()->filter();
        $usersMap = User::whereIn('id', $userIds)->get()->keyBy('id');
        
        $report = [];
        foreach ($archives as $archive) {
            $uid = $archive->de_user_id;
            if (!$uid) continue;
            
            if (!isset($report[$uid])) {
                $u = $usersMap->get($uid);
                $report[$uid] = [
                    'user_id'        => $uid,
                    'user_name'      => $u?->name ?? 'نامشخص',
                    'user_email'     => $u?->email ?? '',
                    'total_books'    => 0,
                    'total_students' => 0,
                    'archives'       => [],
                ];
            }
            
            $cnt = $archive->archivedatas->count();
            $report[$uid]['total_books']++;
            $report[$uid]['total_students'] += $cnt;
            
            // تبدیل تاریخ به شمسی
            $shamsiDate = $this->toShamsi($archive->created_at, true);
            
            $report[$uid]['archives'][] = [
                'archive_id'        => $archive->id,
                'book_name'         => $archive->book_name ?? '—',
                'book_pagenumber'   => $archive->book_pagenumber ?? '—',
                'students_count'    => $cnt,
                'created_at_shamsi' => $shamsiDate,
            ];
        }
        
        // مرتب‌سازی بر اساس تعداد کتاب
        $reportArray = array_values($report);
        usort($reportArray, function($a, $b) {
            return $b['total_books'] - $a['total_books'];
        });
        
        return [
            'users'          => $reportArray,
            'total_books'    => array_sum(array_column($reportArray, 'total_books')),
            'total_students' => array_sum(array_column($reportArray, 'total_students')),
            'total_users'    => count($reportArray),
        ];
    }
    
    /**
     * گزارش کنترول‌کنندگان (qc_user_id)
     */
    private function getQcUserReport(array $dateRange, ?string $specificUserId, int $authUniversityId): array
    {
        $query = Archive::whereNull('deleted_at')
            ->whereNotNull('qc_user_id')
            ->whereNotNull('qc_status_id')
            ->with('archivedatas');
        
        if ($dateRange['start'] && $dateRange['end']) {
            $query->whereBetween('updated_at', [$dateRange['start'], $dateRange['end']]);
        }
        
        if ($authUniversityId > 0) {
            $query->where('university_id', $authUniversityId);
        }
        
        if ($specificUserId) {
            $query->where('qc_user_id', $specificUserId);
        }
        
        $archives = $query->get();
        
        $userIds = $archives->pluck('qc_user_id')->unique()->filter();
        $usersMap = User::whereIn('id', $userIds)->get()->keyBy('id');
        
        $report = [];
        foreach ($archives as $archive) {
            $uid = $archive->qc_user_id;
            if (!$uid) continue;
            
            if (!isset($report[$uid])) {
                $u = $usersMap->get($uid);
                $report[$uid] = [
                    'user_id'                   => $uid,
                    'user_name'                 => $u?->name ?? 'نامشخص',
                    'user_email'                => $u?->email ?? '',
                    'total_books_controlled'    => 0,
                    'total_students_controlled' => 0,
                    'archives'                  => [],
                ];
            }
            
            $cnt = $archive->archivedatas->count();
            $report[$uid]['total_books_controlled']++;
            $report[$uid]['total_students_controlled'] += $cnt;
            
            // تبدیل تاریخ به شمسی
            $shamsiDate = $this->toShamsi($archive->updated_at, true);
            
            $report[$uid]['archives'][] = [
                'archive_id'          => $archive->id,
                'book_name'           => $archive->book_name ?? '—',
                'book_pagenumber'     => $archive->book_pagenumber ?? '—',
                'students_count'      => $cnt,
                'controlled_at_shamsi'=> $shamsiDate,
                'qc_status'           => $archive->qc_status_id,
            ];
        }
        
        // مرتب‌سازی بر اساس تعداد کتاب
        $reportArray = array_values($report);
        usort($reportArray, function($a, $b) {
            return $b['total_books_controlled'] - $a['total_books_controlled'];
        });
        
        return [
            'users'          => $reportArray,
            'total_books'    => array_sum(array_column($reportArray, 'total_books_controlled')),
            'total_students' => array_sum(array_column($reportArray, 'total_students_controlled')),
            'total_users'    => count($reportArray),
        ];
    }
    
    /**
     * تعیین محدوده تاریخ
     */
    private function getDateRange(string $dateFilter, ?string $startDate, ?string $endDate): array
    {
        $now = Carbon::now();
        
        switch ($dateFilter) {
            case 'today':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'label' => 'امروز'
                ];
            case 'week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek(),
                    'label' => 'این هفته'
                ];
            case 'month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'label' => 'این ماه'
                ];
            case 'year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear(),
                    'label' => 'امسال'
                ];
            case 'custom':
                return [
                    'start' => $startDate ? Carbon::parse($startDate)->startOfDay() : null,
                    'end' => $endDate ? Carbon::parse($endDate)->endOfDay() : null,
                    'label' => 'دوره سفارشی'
                ];
            default:
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'label' => 'امروز'
                ];
        }
    }
    
    /**
     * دریافت لیست کاربران
     */
    private function getUsersList(int $authUniversityId, string $userType = 'all')
    {
        if ($userType === 'de') {
            $allIds = Archive::whereNotNull('de_user_id')->distinct()->pluck('de_user_id');
        } elseif ($userType === 'qc') {
            $allIds = Archive::whereNotNull('qc_user_id')->distinct()->pluck('qc_user_id');
        } else {
            $deIds = Archive::whereNotNull('de_user_id')->distinct()->pluck('de_user_id');
            $qcIds = Archive::whereNotNull('qc_user_id')->distinct()->pluck('qc_user_id');
            $allIds = $deIds->merge($qcIds)->unique();
        }
        
        $q = User::whereIn('id', $allIds)->orderBy('name');
        if ($authUniversityId > 0) {
            $q->where('university_id', $authUniversityId);
        }
        
        return $q->get(['id', 'name', 'email']);
    }
    
    /**
     * متد اصلی
     */
    public function index(Request $request)
    {
        $authUniversityId = auth()->user()->university_id ?? 0;
        
        $dateFilter = $request->get('date_filter', 'today');
        $startDate  = $request->get('start_date');
        $endDate    = $request->get('end_date');
        $userId     = $request->get('user_id');
        $userType   = $request->get('user_type', 'all');
        
        $dateRange = $this->getDateRange($dateFilter, $startDate, $endDate);
        
        $deUserReport = $this->getDeUserReport($dateRange, $userId, $authUniversityId);
        $qcUserReport = $this->getQcUserReport($dateRange, $userId, $authUniversityId);
        $users = $this->getUsersList($authUniversityId, $userType);
        
        return view('archivereport.user_activity_report', compact(
            'deUserReport', 'qcUserReport', 'users',
            'dateFilter', 'startDate', 'endDate', 'userId', 'userType'
        ));
    }
    
    /**
     * متد عمومی برای استفاده در ویو (تبدیل تاریخ)
     */
    public function toShamsiPublic($date, bool $withTime = false): string
    {
        return $this->toShamsi($date, $withTime);
    }
}