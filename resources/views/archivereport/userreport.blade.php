@extends('layouts.app')

@section('content')
<div class="report-page">

{{-- Screen Filter Section (hidden in print) --}}
<div class="filter-card no-print">
        {{-- Header --}}
        <div class="report-premium-header">
            <div>
                <h2 class="report-premium-title">راپور فعالیت یوزرهای آرشیف</h2>
                <p class="report-premium-subtitle">گزارش جامع فعالیت‌ها بر اساس کتاب‌ها، محصلان و نقش کاربران</p>
            </div>
            <div>
                <a href="{{ route('archiveuser_report.excel', request()->all()) }}" class="btn-excel-premium">
                    خروجی Excel
                </a>
            </div>
        </div>

        {{-- بخش فیلتر --}}
        <div class="filter-premium-card">
            <div class="filter-premium-header">
                فلتر اطلاعات
            </div>
            <div class="filter-premium-body">
                <form method="GET" action="{{ route('archiveuser_report') }}" id="filterForm">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="filter-premium-label">از سال</label>
                            <select name="from_year" class="filter-premium-select">
                                <option value="">همه سال‌ها</option>
                                @foreach($allYears as $year)
                                    <option value="{{ $year }}" {{ request('from_year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="filter-premium-label">تا سال</label>
                            <select name="to_year" class="filter-premium-select">
                                <option value="">همه سال‌ها</option>
                                @foreach($allYears as $year)
                                    <option value="{{ $year }}" {{ request('to_year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="filter-premium-label">پوهنتون</label>
                            <select name="university_id" class="filter-premium-select">
                                <option value="">همه پوهنتون‌ها</option>
                                @foreach($universitiesList as $id => $name)
                                    <option value="{{ $id }}" {{ request('university_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="filter-premium-label">ولایت</label>
                            <select name="province_id" class="filter-premium-select">
                                <option value="">همه ولایات</option>
                                @foreach($provinces as $id => $name)
                                    <option value="{{ $id }}" {{ request('province_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="filter-premium-label">نقش</label>
                            <select name="filter_role" class="filter-premium-select" id="filter_role">
                                <option value="all" {{ request('filter_role') == 'all' ? 'selected' : '' }}>همه نقش‌ها</option>
                                <option value="inserter" {{ request('filter_role') == 'inserter' ? 'selected' : '' }}>درج کننده</option>
                                <option value="controller" {{ request('filter_role') == 'controller' ? 'selected' : '' }}>کنترول کننده</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="filter-premium-label">یوزر</label>
                            <select name="filter_user_id" class="filter-premium-select" id="filter_user_id">
                                <option value="all">همه یوزرها</option>
                                @foreach($userListForFilter as $id => $name)
                                    <option value="{{ $id }}" {{ request('filter_user_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                   
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <br>
                            <div class="d-flex gap-4 w-100">
                                <button type="submit" class="btn-filter-premium-submit"> فلتر</button>
                                <a href="{{ route('archiveuser_report') }}" class="btn-filter-premium-reset">پاک کردن</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- کارت‌های آماری --}}
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stats-premium-card stats-premium-blue">
                    <div class="stats-premium-number">{{ $totals['total_users'] }}</div>
                    <div class="stats-premium-label"> تعداد کل یوزرها آرشیف</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-premium-card stats-premium-green">
                    <div class="stats-premium-number">{{ $totals['total_books'] }}</div>
                    <div class="stats-premium-label">تعداد کل کتاب‌ها</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-premium-card stats-premium-teal">
                    <div class="stats-premium-number">{{ $totals['total_inserters'] }}</div>
                    <div class="stats-premium-label">درج کننده‌ها</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-premium-card stats-premium-orange">
                    <div class="stats-premium-number">{{ $totals['total_controllers'] }}</div>
                    <div class="stats-premium-label">کنترول کننده‌ها</div>
                </div>
            </div>
        </div>

        {{-- جدول اصلی --}}
        <div class="table-premium-wrapper">
            <div class="table-premium-header">
                <h5 class="mb-0">گزارش تفصیلی فعالیت یوزرها آرشیف</h5>
                <span class="table-premium-count">{{ count($reportRows) }} رکورد</span>
            </div>
            <div class="table-responsive">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>نام یوزر</th>
                            <th>ایمیل</th>
                            <th>نقش</th>
                            <th>تعداد کتاب‌ها</th>
                            <th>تعداد محصلان</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportRows as $index => $report)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="user-premium-cell">
                                    <div class="user-premium-avatar">{{ substr($report['user_name'], 0, 1) }}</div>
                                    <span>{{ $report['user_name'] }}</span>
                                </div>
                            </td>
                            <td>{{ $report['user_email'] }}</td>
                            <td>
                                @if($report['role'] == 'درج کننده')
                                    <span class="badge-premium badge-primary">درج کننده</span>
                                @elseif($report['role'] == 'کنترول کننده')
                                    <span class="badge-premium badge-warning">کنترول کننده</span>
                                @elseif($report['role'] == 'درج کننده / کنترول کننده')
                                    <span class="badge-premium badge-success">درج کننده / کنترول کننده</span>
                                @else
                                    <span class="badge-premium badge-secondary">بدون فعالیت</span>
                                @endif
                            </td>
                            <td><span class="number-premium-badge">{{ $report['books_count'] }}</span></td>
                            <td><span class="number-premium-badge">{{ $report['students_count'] }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-premium-state">
                                    <p>هیچ اطلاعاتی وجود ندارد</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-premium-footer">
                            <td colspan="4" class="text-end fw-bold">مجموع کل:</td>
                            <td class="fw-bold text-primary">{{ $totals['total_books'] }}</td>
                            <td>-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- جدول پوهنتون‌ها --}}
        @if(count($universityReport) > 0)
        <div class="table-premium-wrapper mt-4">
            <div class="table-premium-header">
                <h5 class="mb-0">گزارش خلاصه بر اساس پوهنتون</h5>
                <span class="table-premium-count">{{ count($universityReport) }} پوهنتون</span>
            </div>
            <div class="table-responsive">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>پوهنتون</th>
                            <th>ولایت</th>
                            <th>تعداد کتاب‌ها</th>
                            <th>تعداد درج کننده‌ها</th>
                            <th>تعداد کنترول کننده‌ها</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($universityReport as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $row['university_name'] }}</td>
                            <td>{{ $row['province'] }}</td>
                            <td><span class="number-premium-badge">{{ $row['total_books'] }}</span></td>
                            <td><span class="number-premium-badge">{{ $row['inserters_count'] }}</span></td>
                            <td><span class="number-premium-badge">{{ $row['controllers_count'] }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-premium-footer">
                            <td colspan="3" class="text-end fw-bold">مجموع کل:</td>
                            <td class="fw-bold text-primary">{{ $universityTotals['total_books'] }}</td>
                            <td class="fw-bold text-primary">{{ $universityTotals['total_inserters'] }}</td>
                            <td class="fw-bold text-primary">{{ $universityTotals['total_controllers'] }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    /* استایل‌های حرفه‌ای و مینیمال - بدون نیاز به آیکون */
    
    /* کانتینر اصلی */
    .report-premium-container {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 20px 40px -15px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    
    /* Header */
    .report-premium-header {
        padding: 32px 32px 24px 32px;
        border-bottom: 1px solid #eef2f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        background: #ffffff;
    }
    
    .report-premium-title {
        font-size: 24px;
        font-weight: 600;
        color: #1a1f36;
        margin: 0 0 6px 0;
        letter-spacing: -0.3px;
    }
    
    .report-premium-subtitle {
        font-size: 14px;
        color: #5b6e8c;
        margin: 0;
    }
    
    .btn-excel-premium {
        background: #0f973d;
        color: white;
        padding: 8px 24px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.2s;
        display: inline-block;
        border: none;
        cursor: pointer;
    }
    
    .btn-excel-premium:hover {
        background: #0b7a31;
        transform: translateY(-1px);
        color: white;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(15, 151, 61, 0.2);
    }
    
    /* کارت فیلتر */
    .filter-premium-card {
        margin: 24px 32px;
        background: #f8fafc;
        border-radius: 20px;
        border: 1px solid #eef2f6;
        overflow: hidden;
    }
    
    .filter-premium-header {
        padding: 16px 24px;
        background: #ffffff;
        border-bottom: 1px solid #eef2f6;
        font-weight: 600;
        font-size: 15px;
        color: #1a1f36;
    }
    
    .filter-premium-body {
        padding: 24px;
    }
    
    .filter-premium-label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: #5b6e8c;
        margin-bottom: 8px;
    }
    
    .filter-premium-select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: white;
        font-size: 14px;
        color: #1a1f36;
        transition: all 0.2s;
    }
    
    .filter-premium-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .btn-filter-premium-submit {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        width: 100%;
    }
    
    .btn-filter-premium-submit:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }
    
    .btn-filter-premium-reset {
        background: white;
        color: #5b6e8c;
        border: 1px solid #e2e8f0;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 500;
        font-size: 14px;
        text-decoration: none;
        text-align: center;
        width: 100%;
        display: inline-block;
        transition: all 0.2s;
    }
    
    .btn-filter-premium-reset:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #1a1f36;
        text-decoration: none;
    }
    
    /* کارت‌های آماری */
    .stats-premium-card {
        background: white;
        padding: 24px;
        border-radius: 20px;
        text-align: center;
        transition: all 0.2s;
        border: 1px solid #eef2f6;
    }
    
    .stats-premium-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px -8px rgba(0,0,0,0.08);
    }
    
    .stats-premium-number {
        font-size: 36px;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 8px;
    }
    
    .stats-premium-label {
        font-size: 13px;
        font-weight: 500;
        color: #5b6e8c;
    }
    
    .stats-premium-blue .stats-premium-number { color: #3b82f6; }
    .stats-premium-green .stats-premium-number { color: #0f973d; }
    .stats-premium-teal .stats-premium-number { color: #14b8a6; }
    .stats-premium-orange .stats-premium-number { color: #f59e0b; }
    
    /* جدول */
    .table-premium-wrapper {
        margin: 0 32px 32px 32px;
        background: white;
        border-radius: 20px;
        border: 1px solid #eef2f6;
        overflow: hidden;
    }
    
    .table-premium-header {
        padding: 16px 24px;
        background: #fafbfc;
        border-bottom: 1px solid #eef2f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    
    .table-premium-count {
        font-size: 12px;
        font-weight: 500;
        background: #eef2f6;
        padding: 4px 12px;
        border-radius: 30px;
        color: #5b6e8c;
    }
    
    .table-premium {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table-premium thead th {
        background: #ffffff;
        padding: 14px 20px;
        font-size: 12px;
        font-weight: 600;
        color: #5b6e8c;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #eef2f6;
    }
    
    .table-premium tbody td {
        padding: 14px 20px;
        font-size: 14px;
        color: #1a1f36;
        border-bottom: 1px solid #f0f2f5;
    }
    
    .table-premium tbody tr:hover {
        background: #fafbfc;
    }
    
    .user-premium-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .user-premium-avatar {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 13px;
    }
    
    .badge-premium {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .badge-primary {
        background: #eff6ff;
        color: #1e40af;
    }
    
    .badge-warning {
        background: #fffbeb;
        color: #b45309;
    }
    
    .badge-success {
        background: #ecfdf5;
        color: #065f46;
    }
    
    .badge-secondary {
        background: #f3f4f6;
        color: #4b5563;
    }
    
    .number-premium-badge {
        display: inline-block;
        background: #f3f4f6;
        padding: 4px 12px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 13px;
        color: #1f2937;
    }
    
    .table-premium-footer td {
        background: #fafbfc;
        padding: 12px 20px;
        border-top: 1px solid #eef2f6;
        font-weight: 500;
    }
    
    .empty-premium-state {
        text-align: center;
        padding: 48px;
        color: #9ca3af;
    }
    
    /* واکنش‌گرا */
    @media (max-width: 768px) {
        .report-premium-header {
            padding: 24px 20px;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .filter-premium-card {
            margin: 16px 20px;
        }
        
        .filter-premium-body {
            padding: 20px;
        }
        
        .table-premium-wrapper {
            margin: 0 20px 20px 20px;
        }
        
        .report-premium-title {
            font-size: 20px;
        }
        
        .stats-premium-number {
            font-size: 28px;
        }
        
        .stats-premium-card {
            padding: 18px;
        }
        
        .btn-filter-premium-submit,
        .btn-filter-premium-reset {
            padding: 8px 16px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#filter_role').on('change', function() {
            var role = $(this).val();
            var universityId = $('select[name="university_id"]').val();
            var provinceId = $('select[name="province_id"]').val();
            var fromYear = $('select[name="from_year"]').val();
            var toYear = $('select[name="to_year"]').val();
            
            $.ajax({
                url: '{{ route("archiveuser_report.getUsersByRole") }}',
                type: 'GET',
                data: {
                    role: role,
                    university_id: universityId,
                    province_id: provinceId,
                    from_year: fromYear,
                    to_year: toYear
                },
                success: function(response) {
                    var $userSelect = $('#filter_user_id');
                    $userSelect.empty();
                    $userSelect.append('<option value="all">همه یوزرها</option>');
                    
                    $.each(response.users, function(id, name) {
                        $userSelect.append('<option value="' + id + '">' + name + '</option>');
                    });
                }
            });
        });
    });
</script>
@endpush