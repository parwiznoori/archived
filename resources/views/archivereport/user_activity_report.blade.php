@extends('layouts.app')

@section('title', 'گزارش فعالیت کاربران')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; }
body { font-family: 'Noto Naskh Arabic', sans-serif; direction: rtl; background: #f0f4f8; }

.rpt-wrap { max-width: 1400px; margin: 24px auto; padding: 0 16px; }

/* ══ Top bar ══ */
.top-bar { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
.top-bar h1 { font-size:20px; font-weight:700; color:#1a2942; margin:0; }
.top-bar .period-tag { background:#1a2942; color:#fff; font-size:12px; padding:4px 14px; border-radius:20px; }

/* ══ Filter card ══ */
.filter-card {
    background:#fff; border-radius:12px; padding:18px 22px;
    margin-bottom:22px; box-shadow:0 1px 6px rgba(0,0,0,.07);
    display:flex; flex-wrap:wrap; gap:14px; align-items:flex-end;
}
.fg { display:flex; flex-direction:column; gap:5px; }
.fg label { font-size:11px; color:#667; font-weight:600; }
.fg select,
.fg input[type="text"],
.fg input[type="date"] {
    font-family:'Noto Naskh Arabic',sans-serif; font-size:13px;
    padding:7px 11px; border:1px solid #d0d7e2; border-radius:8px;
    background:#f8fafc; min-width:160px; color:#1a2942;
}
.fg select:focus, .fg input:focus { outline:none; border-color:#3b82f6; background:#fff; }

.btn-filter {
    font-family:'Noto Naskh Arabic',sans-serif; font-size:13px;
    padding:8px 20px; border-radius:8px; border:none;
    cursor:pointer; font-weight:600; transition:opacity .15s; text-decoration:none;
    display:inline-flex; align-items:center; gap:5px;
}
.btn-filter:hover { opacity:.85; }
.btn-blue  { background:#3b82f6; color:#fff; }
.btn-gray  { background:#e5e7eb; color:#374151; }
.btn-green { background:#10b981; color:#fff; }

/* ══ Stat cards ══ */
.stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:22px; }
.stat-card {
    background:#fff; border-radius:12px; padding:18px 20px;
    display:flex; align-items:center; gap:14px;
    box-shadow:0 1px 6px rgba(0,0,0,.07); border-right:4px solid transparent;
}
.stat-card.blue   { border-color:#3b82f6; }
.stat-card.green  { border-color:#10b981; }
.stat-card.orange { border-color:#f59e0b; }
.stat-card.purple { border-color:#8b5cf6; }
.stat-icon { width:46px;height:46px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0; }
.blue   .stat-icon { background:#eff6ff; }
.green  .stat-icon { background:#ecfdf5; }
.orange .stat-icon { background:#fffbeb; }
.purple .stat-icon { background:#f5f3ff; }
.stat-info .val { font-size:26px; font-weight:800; color:#1a2942; line-height:1; }
.stat-info .lbl { font-size:11px; color:#6b7280; margin-top:4px; }

/* ══ Two panels ══ */
.panels-row { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.panel { background:#fff; border-radius:12px; box-shadow:0 1px 6px rgba(0,0,0,.07); overflow:hidden; }

.panel-header { padding:14px 18px; display:flex; align-items:center; justify-content:space-between; }
.panel-header.blue-h  { background:#eff6ff; border-bottom:2px solid #3b82f6; }
.panel-header.amber-h { background:#fffbeb; border-bottom:2px solid #f59e0b; }
.panel-header h2 { font-size:14px; font-weight:700; color:#1a2942; margin:0; }

.badge { font-size:11px; padding:3px 10px; border-radius:20px; font-weight:700; }
.badge-blue  { background:#3b82f6; color:#fff; }
.badge-amber { background:#f59e0b; color:#fff; }

/* ══ User tables (بدون جزئیات و بدون قابلیت باز شدن) ══ */
.user-list { padding:10px 14px; }

.user-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.user-table th {
    background: #f8fafc;
    padding: 12px 10px;
    text-align: center;
    font-size: 13px;
    font-weight: 700;
    color: #1a2942;
    border-bottom: 2px solid #e5e7eb;
}

.user-table td {
    padding: 10px;
    text-align: center;
    font-size: 13px;
    color: #374151;
    border-bottom: 1px solid #f3f4f6;
}

.user-table tr:hover td {
    background: #f9fafb;
}

.empty-state { text-align:center; padding:40px 20px; color:#9ca3af; font-size:14px; }
.empty-state .icon { font-size:36px; margin-bottom:10px; }

/* ══ Responsive ══ */
@media (max-width:900px) { .panels-row { grid-template-columns:1fr; } .stats-row { grid-template-columns:repeat(2,1fr); } }
@media (max-width:560px) { .stats-row { grid-template-columns:1fr 1fr; } .filter-card { flex-direction:column; } }
</style>
@endpush

@section('content')
@php
    $pLabels = ['today'=>'امروز','week'=>'این هفته','month'=>'این ماه','year'=>'امسال','custom'=>'دوره سفارشی'];
@endphp

<div class="rpt-wrap">

    {{-- ══ Top bar ══ --}}
    <div class="top-bar">
        <h1>📊 گزارش فعالیت کاربران</h1>
        <span class="period-tag">{{ $pLabels[$dateFilter] ?? $dateFilter }}</span>
    </div>

    {{-- ══ Filters ══ --}}
    <form method="GET" action="{{ route('archiveuser_activity_report') }}" class="filter-card" id="filterForm">
        <div class="fg">
            <label>تاریخ </label>
            <select name="date_filter" id="dateFilterSel" onchange="toggleCustomDates(this.value)">
                <option value="today"  {{ $dateFilter=='today'  ?'selected':'' }}>امروز</option>
                <option value="week"   {{ $dateFilter=='week'   ?'selected':'' }}>این هفته</option>
                <option value="month"  {{ $dateFilter=='month'  ?'selected':'' }}>این ماه</option>
                <option value="year"   {{ $dateFilter=='year'   ?'selected':'' }}>امسال</option>
                <option value="custom" {{ $dateFilter=='custom' ?'selected':'' }}>سفارشی</option>
            </select>
        </div>

        <div class="fg" id="startDateWrap" style="{{ $dateFilter=='custom' ? '' : 'display:none' }}">
            <label>از تاریخ (شمسی)</label>
            <input type="text" id="start_date_shamsi" placeholder="مثال: 1403/07/01" autocomplete="off"
                value="{{ $startDate ? app(\App\Http\Controllers\Archive\ArchiveUserActivityReportController::class)->toShamsiPublic($startDate, false) : '' }}">
            <input type="hidden" name="start_date" id="start_date_gregorian" value="{{ $startDate }}">
        </div>

        <div class="fg" id="endDateWrap" style="{{ $dateFilter=='custom' ? '' : 'display:none' }}">
            <label>تا تاریخ (شمسی)</label>
            <input type="text" id="end_date_shamsi" placeholder="مثال: 1403/07/30" autocomplete="off"
                value="{{ $endDate ? app(\App\Http\Controllers\Archive\ArchiveUserActivityReportController::class)->toShamsiPublic($endDate, false) : '' }}">
            <input type="hidden" name="end_date" id="end_date_gregorian" value="{{ $endDate }}">
        </div>

        <div class="fg">
            <label>نوعیت کاربر</label>
            <select name="user_type" id="userTypeSel" onchange="this.form.submit()">
                <option value="all" {{ ($userType??'all')=='all' ? 'selected' : '' }}>— همه —</option>
                <option value="de"  {{ ($userType??'')=='de'    ? 'selected' : '' }}>✏️ درج‌کننده</option>
                <option value="qc"  {{ ($userType??'')=='qc'    ? 'selected' : '' }}>✅ کنترول‌کننده</option>
            </select>
        </div>

        <div class="fg">
            <label>کاربر</label>
            <select name="user_id">
                <option value="">— همه کاربران —</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ $userId==$u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn-filter btn-blue">🔍 جستجو</button>
        <a href="{{ route('archiveuser_activity_report') }}" class="btn-filter btn-gray">↺ پاک</a>
        <button type="button" class="btn-filter btn-green" id="exportExcelBtn">📥 خروجی اکسل</button>
    </form>

    {{-- ══ Stat cards ══ --}}
    <div class="stats-row">
        <div class="stat-card blue">
            <div class="stat-icon">📚</div>
            <div class="stat-info">
                <div class="val">{{ number_format($deUserReport['total_books']) }}</div>
                <div class="lbl">کتاب‌های درج‌شده</div>
            </div>
        </div>
        <div class="stat-card green">
            <div class="stat-icon">🎓</div>
            <div class="stat-info">
                <div class="val">{{ number_format($deUserReport['total_students']) }}</div>
                <div class="lbl">محصلان درج‌شده</div>
            </div>
        </div>
        <div class="stat-card orange">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <div class="val">{{ number_format($qcUserReport['total_books']) }}</div>
                <div class="lbl">کتاب‌های کنترول‌شده</div>
            </div>
        </div>
        <div class="stat-card purple">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <div class="val">{{ number_format($qcUserReport['total_students']) }}</div>
                <div class="lbl">محصلان کنترول‌شده</div>
            </div>
        </div>
    </div>

    {{-- ══ Panels ══ --}}
    <div class="panels-row">

        {{-- DE Panel --}}
        <div class="panel">
            <div class="panel-header blue-h">
                <h2>✏️ درج‌کنندگان</h2>
                <span class="badge badge-blue">{{ $deUserReport['total_users'] }} کاربر</span>
            </div>
            <div class="user-list">
                @if(count($deUserReport['users']) > 0)
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>نام کاربر</th>
                            <th>ایمیل</th>
                            <th>تعداد کتاب</th>
                            <th>تعداد محصل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deUserReport['users'] as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="text-align:right; padding-right:14px;">{{ $user['user_name'] }}</td>
                            <td>{{ $user['user_email'] }}</td>
                            <td>{{ number_format($user['total_books']) }}</td>
                            <td>{{ number_format($user['total_students']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state"><div class="icon">📭</div>هیچ داده‌ای یافت نشد</div>
                @endif
            </div>
        </div>

        {{-- QC Panel --}}
        <div class="panel">
            <div class="panel-header amber-h">
                <h2>✅ کنترول‌کنندگان</h2>
                <span class="badge badge-amber">{{ $qcUserReport['total_users'] }} کاربر</span>
            </div>
            <div class="user-list">
                @if(count($qcUserReport['users']) > 0)
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>نام کاربر</th>
                            <th>ایمیل</th>
                            <th>تعداد کتاب</th>
                            <th>تعداد محصل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($qcUserReport['users'] as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="text-align:right; padding-right:14px;">{{ $user['user_name'] }}</td>
                            <td>{{ $user['user_email'] }}</td>
                            <td>{{ number_format($user['total_books_controlled']) }}</td>
                            <td>{{ number_format($user['total_students_controlled']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state"><div class="icon">📭</div>هیچ داده‌ای یافت نشد</div>
                @endif
            </div>
        </div>

    </div>{{-- /.panels-row --}}
</div>{{-- /.rpt-wrap --}}
@endsection

@push('scripts')
<script>
// داده JSON برای Excel
const REPORT_DATA = {
    de: {
        users: @json($deUserReport['users']),
        total_books: {{ $deUserReport['total_books'] }},
        total_students: {{ $deUserReport['total_students'] }}
    },
    qc: {
        users: @json($qcUserReport['users']),
        total_books: {{ $qcUserReport['total_books'] }},
        total_students: {{ $qcUserReport['total_students'] }}
    },
    period: "{{ $pLabels[$dateFilter] ?? $dateFilter }}",
    date: "{{ now()->format('Y-m-d') }}",
    start_date: "{{ $startDate }}",
    end_date: "{{ $endDate }}"
};
</script>

{{-- کتابخانه Excel --}}
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
/* ─── فیلتر تاریخ سفارشی ─── */
function toggleCustomDates(val) {
    document.getElementById('startDateWrap').style.display = val === 'custom' ? '' : 'none';
    document.getElementById('endDateWrap').style.display   = val === 'custom' ? '' : 'none';
}

/* ─── تبدیل شمسی → میلادی ─── */
function shamsiToGregorian(jy, jm, jd) {
    jy = parseInt(jy); jm = parseInt(jm); jd = parseInt(jd);
    var jy2 = jy - 979;
    var j_day_no = 365 * jy2 + Math.floor(jy2 / 33) * 8 + Math.floor((jy2 % 33 + 3) / 4);
    var jmd = [31,31,31,31,31,31,30,30,30,30,30,29];
    for (var i = 0; i < jm - 1; i++) j_day_no += jmd[i];
    j_day_no += jd - 1;
    var g = j_day_no + 79;
    var gy = 1600 + 400 * Math.floor(g / 146097); g %= 146097;
    var leap = true;
    if (g >= 36525) { g--; gy += 100 * Math.floor(g / 36524); g %= 36524; if (g >= 365) g++; else leap = false; }
    gy += 4 * Math.floor(g / 1461); g %= 1461;
    if (g >= 366) { leap = false; g--; gy += Math.floor(g / 365); g %= 365; }
    var gmd = [31, leap ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    var gm = 0;
    for (gm = 0; gm < 12; gm++) { if (g < gmd[gm]) break; g -= gmd[gm]; }
    return gy + '-' + String(gm + 1).padStart(2,'0') + '-' + String(g + 1).padStart(2,'0');
}

function parseShamsiInput(val) {
    if (!val) return '';
    val = val.replace(/[۰-۹]/g, function(d){ return d.charCodeAt(0) - 1776; })
             .replace(/[٠-٩]/g, function(d){ return d.charCodeAt(0) - 1632; })
             .trim();
    var parts = val.split(/[\/\-\.]/);
    if (parts.length === 3 && parts[0].length === 4) return shamsiToGregorian(parts[0], parts[1], parts[2]);
    return '';
}

document.getElementById('start_date_shamsi').addEventListener('blur', function() {
    document.getElementById('start_date_gregorian').value = parseShamsiInput(this.value);
});
document.getElementById('end_date_shamsi').addEventListener('blur', function() {
    document.getElementById('end_date_gregorian').value = parseShamsiInput(this.value);
});
document.getElementById('filterForm').addEventListener('submit', function() {
    var sv = document.getElementById('start_date_shamsi').value;
    var ev = document.getElementById('end_date_shamsi').value;
    if (sv) document.getElementById('start_date_gregorian').value = parseShamsiInput(sv);
    if (ev) document.getElementById('end_date_gregorian').value   = parseShamsiInput(ev);
});

/* ─── اکسل (فقط خلاصه بدون جزئیات کتاب‌ها) ─── */
document.getElementById('exportExcelBtn').addEventListener('click', function () {
    const wb = XLSX.utils.book_new();
    
    // تنظیم عنوان گزارش
    var title = 'گزارش فعالیت کاربران';
    var periodText = '';
    if (REPORT_DATA.period === 'دوره سفارشی' && REPORT_DATA.start_date && REPORT_DATA.end_date) {
        periodText = ` (از ${REPORT_DATA.start_date} تا ${REPORT_DATA.end_date})`;
    } else {
        periodText = ` (${REPORT_DATA.period})`;
    }
    
    // ==================== شیت ۱: درج‌کنندگان ====================
    const deRows = [];
    
    // عنوان
    deRows.push(['گزارش فعالیت کاربران - درج‌کنندگان']);
    deRows.push(['تاریخ تهیه:', REPORT_DATA.date]);
    deRows.push([' تاریخ:', REPORT_DATA.period + periodText]);
    deRows.push([]);
    
    // هدر جدول
    deRows.push(['ردیف', 'نام کاربر', 'ایمیل', 'تعداد کتاب درج شده', 'تعداد محصل درج شده']);
    
    // داده‌ها
    REPORT_DATA.de.users.forEach(function(user, index) {
        deRows.push([
            index + 1,
            user.user_name,
            user.user_email,
            user.total_books,
            user.total_students
        ]);
    });
    
    // جمع کل
    deRows.push([]);
    deRows.push(['جمع کل', '', '', REPORT_DATA.de.total_books, REPORT_DATA.de.total_students]);
    
    const wsDE = XLSX.utils.aoa_to_sheet(deRows);
    wsDE['!cols'] = [{wch:8}, {wch:25}, {wch:30}, {wch:18}, {wch:18}];
    XLSX.utils.book_append_sheet(wb, wsDE, 'درج کنندگان');
    
    // ==================== شیت ۲: کنترول‌کنندگان ====================
    const qcRows = [];
    
    // عنوان
    qcRows.push(['گزارش فعالیت کاربران - کنترول‌کنندگان']);
    qcRows.push(['تاریخ تهیه:', REPORT_DATA.date]);
    qcRows.push(['تاریخ :', REPORT_DATA.period + periodText]);
    qcRows.push([]);
    
    // هدر جدول
    qcRows.push(['ردیف', 'نام کاربر', 'ایمیل', 'تعداد کتاب کنترل شده', 'تعداد محصل کنترل شده']);
    
    // داده‌ها
    REPORT_DATA.qc.users.forEach(function(user, index) {
        qcRows.push([
            index + 1,
            user.user_name,
            user.user_email,
            user.total_books_controlled,
            user.total_students_controlled
        ]);
    });
    
    // جمع کل
    qcRows.push([]);
    qcRows.push(['جمع کل', '', '', REPORT_DATA.qc.total_books, REPORT_DATA.qc.total_students]);
    
    const wsQC = XLSX.utils.aoa_to_sheet(qcRows);
    wsQC['!cols'] = [{wch:8}, {wch:25}, {wch:30}, {wch:18}, {wch:18}];
    XLSX.utils.book_append_sheet(wb, wsQC, 'کنترول کنندگان');
    
    // ذخیره فایل
    XLSX.writeFile(wb, `گزارش_فعالیت_کاربران_${REPORT_DATA.date}.xlsx`);
});
</script>
@endpush