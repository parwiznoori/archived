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
        $this->middleware(function ($request, $next) {
            if (!auth()->check()) {
                abort(403);
            }
            // کاربران عادی (درج‌کننده/کنترول‌کننده) می‌توانند فقط گزارش خودشان را ببینند
            if (auth()->user()->type == 2 || auth()->user()->can('archiveuser_activity_report') || auth()->user()->can('view-archive')) {
                return $next($request);
            }
            abort(403);
        });
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
        
        // اعمال فیلتر تاریخ: کتابی که در همین دوره ساخته شده باشد
        // یا محصلانش در همین دوره درج شده باشند (فعالیت واقعی درج‌کننده)
        if ($dateRange['start'] && $dateRange['end']) {
            $query->where(function ($q) use ($dateRange) {
                $q->whereBetween('archives.created_at', [$dateRange['start'], $dateRange['end']])
                    ->orWhereHas('archivedatas', function ($qq) use ($dateRange) {
                        $qq->whereBetween('archivedatas.created_at', [$dateRange['start'], $dateRange['end']]);
                    });
            });
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
                    'user_id'          => $uid,
                    'user_name'        => $u?->name ?? 'نامشخص',
                    'user_email'       => $u?->email ?? '',
                    'total_books'      => 0,
                    'total_students'   => 0,
                    'books_complete'   => 0,
                    'books_process'    => 0,
                    'books_incomplete' => 0,
                    'books_normal'     => 0,
                    'archives'         => [],
                ];
            }

            $cnt = $archive->archivedatas->count();
            $report[$uid]['total_books']++;
            $report[$uid]['total_students'] += $cnt;

            // شمارش وضعیت کتاب
            $st = (int) $archive->status_id;
            if ($st === 4) {
                $report[$uid]['books_complete']++;
            } elseif ($st === 2) {
                $report[$uid]['books_process']++;
            } elseif ($st === 3) {
                $report[$uid]['books_incomplete']++;
            } else {
                $report[$uid]['books_normal']++;
            }

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
     * شرط «محصل در همین دوره کنترول شده است»:
     * یا وضعیت QC خودِ محصل در همین دوره تغییر کرده،
     * یا وضعیت QC صفحه (عکس)ِ همان محصل در همین دوره تغییر کرده است.
     */
    private function studentControlledInPeriod(array $dateRange)
    {
        $range = [$dateRange['start'], $dateRange['end']];

        return function ($q) use ($range) {
            $q->where(function ($qq) use ($range) {
                $qq->whereBetween('archivedatas.updated_at', $range)
                    ->whereIn('archivedatas.qc_status_id', [2, 3, 4]);
            })->orWhere(function ($qq) use ($range) {
                $qq->whereHas('archiveimage', function ($qi) use ($range) {
                    $qi->whereBetween('archiveimages.updated_at', $range)
                        ->whereIn('archiveimages.qc_status_id', [3, 4]);
                });
            });
        };
    }

    /**
     * گزارش کنترول‌کنندگان (qc_user_id)
     */
    private function getQcUserReport(array $dateRange, ?string $specificUserId, int $authUniversityId): array
    {
        $hasRange = $dateRange['start'] && $dateRange['end'];

        $query = Archive::whereNull('deleted_at')
            ->whereNotNull('qc_user_id')
            ->whereNotNull('qc_status_id')
            ->with(['images', 'archivedatas' => function ($q) use ($dateRange, $hasRange) {
                if ($hasRange) {
                    $q->where($this->studentControlledInPeriod($dateRange));
                }
            }]);
        
        // اعمال فیلتر تاریخ: کتابی که در همین دوره آپدیت شده باشد
        // یا محصلی از آن در همین دوره کنترول (تایید/رد) شده باشد
        if ($hasRange) {
            $query->where(function ($q) use ($dateRange) {
                $q->whereBetween('archives.updated_at', [$dateRange['start'], $dateRange['end']])
                    ->orWhereHas('archivedatas', $this->studentControlledInPeriod($dateRange));
            });
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
                    'user_id'                 => $uid,
                    'user_name'               => $u?->name ?? 'نامشخص',
                    'user_email'              => $u?->email ?? '',
                    'total_books_controlled'  => 0,
                    'total_students_controlled' => 0,
                    'books_rejected'          => 0,
                    'books_approved'          => 0,
                    'books_not_checked'       => 0,
                    'students_rejected'       => 0,
                    'students_approved'       => 0,
                    'archives'                => [],
                ];
            }

            $cnt = $archive->archivedatas->count();
            $report[$uid]['total_books_controlled']++;
            $report[$uid]['total_students_controlled'] += $cnt;

            // شمارش وضعیت محصلان کنترول‌شده (رد شده / تایید شده)
            // وضعیت صفحه (عکس) محصل هم لحاظ می‌شود، چون کنترول گاهی روی صفحه انجام می‌شود
            $images = $archive->images->keyBy('id');
            foreach ($archive->archivedatas as $student) {
                $imageQc   = (int) ($images[$student->archiveimage_id]->qc_status_id ?? 0);
                $studentQc = (int) $student->qc_status_id;

                if ($studentQc === 4 || $imageQc === 4) {
                    $report[$uid]['students_rejected']++;
                } elseif ($studentQc === 3 || $imageQc === 3) {
                    $report[$uid]['students_approved']++;
                }
            }

            // شمارش وضعیت QC کتاب
            $qc = (int) $archive->qc_status_id;
            if ($qc === 4) {
                $report[$uid]['books_rejected']++;
            } elseif ($qc === 3) {
                $report[$uid]['books_approved']++;
            } else {
                $report[$uid]['books_not_checked']++;
            }

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
                // هفته کاری از شنبه شروع می‌شود (آخر هفته در افغانستان جمعه است)
                return [
                    'start' => $now->copy()->startOfWeek(Carbon::SATURDAY),
                    'end' => $now->copy()->endOfWeek(Carbon::SATURDAY),
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
        // کاربران عادی فقط خودشان را در لیست می‌بینند
        if (auth()->user()->type == 2 || !(auth()->user()->can('archiveuser_activity_report') || auth()->user()->can('view-archive'))) {
            return User::where('id', auth()->id())->get(['id', 'name', 'email']);
        }

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

        // کاربران درج‌کننده و کنترول‌کننده فقط دیتای مربوط به خودشان را می‌بینند
        if (auth()->user()->type == 2 || !(auth()->user()->can('archiveuser_activity_report') || auth()->user()->can('view-archive'))) {
            $userId   = auth()->id();
            $userType = 'all';
        }

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