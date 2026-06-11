{{-- resources/views/archivereport/bookreport.blade.php --}}
@extends('layouts.app')

@section('title', trans('general.archive_report'))

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Noto Naskh Arabic', sans-serif;
        background: #f0f2f5;
        direction: rtl;
    }
    
    .report-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        background: #fff;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    
    /* Header with Logo Styles */
    .header-with-logo {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding: 0 10px;
    }
    
    .logo-left, .logo-right {
        width: 80px;
        height: 80px;
    }
    
    .logo-left img, .logo-right img {
        width: 100%;
        height: auto;
        object-fit: contain;
    }
    
    .header-text {
        text-align: center;
        flex: 1;
    }
    
    .header-line {
        font-size: 14px;
        font-weight: 500;
        line-height: 1.8;
        color: #000;
    }
    
    .header-line:first-child {
        font-size: 16px;
        font-weight: 700;
    }
    
    .report-title {
        font-size: 15px;
        font-weight: 700;
        margin: 20px 0 10px;
        text-align: center;
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
        padding: 8px 20px;
        display: inline-block;
        width: auto;
    }
    
    .date-range {
        text-align: center;
        font-size: 13px;
        margin: 10px 0 20px;
        color: #333;
    }
    
    /* Summary Cards Styles - فقط برای صفحه نمایش */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .sum-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .sum-card .sum-val {
        font-size: 32px;
        font-weight: 800;
        color: #1e3a5f;
        margin-bottom: 8px;
    }
    
    .sum-card .sum-lbl {
        font-size: 13px;
        color: #666;
        font-weight: 500;
    }
    
    .total-card .sum-val { color: #185FA5; }
    .complete-card .sum-val { color: #0F6E56; }
    .remain-card .sum-val { color: #993C1D; }
    
    /* Table Styles */
    .report-table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
        font-size: 12px;
        font-family: 'Noto Naskh Arabic', sans-serif;
    }
    
    .report-table th,
    .report-table td {
        border: 1px solid #000;
        padding: 8px 6px;
        text-align: center;
        vertical-align: middle;
    }
    
    .report-table th {
        background: #f5f5f5;
        font-weight: 700;
        font-size: 12px;
    }
    
    .report-table td {
        font-size: 12px;
    }
    
    .report-table td.text-right {
        text-align: right;
        padding-right: 10px;
    }
    
    /* Signature Section */
    .signature {
        display: flex;
        justify-content: space-between;
        margin-top: 50px;
        padding: 0 10px;
    }
    
    .signature-box {
        text-align: center;
        width: 30%;
    }
    
    .signature-line {
        border-top: 1px solid #000;
        margin-top: 40px;
        padding-top: 8px;
        font-size: 12px;
    }
    
    .confirmation-text {
        text-align: right;
        margin: 25px 0 15px;
        font-size: 13px;
    }
    
    .footer-text {
        text-align: right;
        margin-top: 15px;
        font-size: 12px;
    }
    
    /* Print Styles - کارت‌های آماری در چاپ و PDF نمایش داده نشوند */
    @media print {
        body {
            background: #fff;
            padding: 0;
            margin: 0;
        }
        
        .report-page {
            padding: 10px;
            max-width: 100%;
            box-shadow: none;
        }
        
        .no-print {
            display: none !important;
        }
        
        /* مخفی کردن کارت‌های آماری در PDF */
        .summary-grid {
            display: none !important;
        }
        
        .report-table th,
        .report-table td {
            border: 1px solid #000 !important;
        }
        
        .report-table th {
            background: #f5f5f5 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .signature {
            margin-top: 30px;
        }
        
        .logo-left, .logo-right {
            width: 60px;
            height: 60px;
        }
    }
    
    /* Filter Section for Screen Only */
    .filter-card {
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 30px;
    }
    
    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 130px;
    }
    
    .filter-group label {
        font-size: 11px;
        color: #555;
        font-weight: 600;
    }
    
    .filter-group select,
    .filter-group input {
        font-family: 'Noto Naskh Arabic', sans-serif;
        font-size: 12px;
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        background: #fff;
        cursor: pointer;
    }
    
    .btn-rpt {
        font-family: 'Noto Naskh Arabic', sans-serif;
        font-size: 12px;
        padding: 6px 15px;
        border-radius: 6px;
        cursor: pointer;
        border: none;
    }
    
    .btn-primary {
        background: #0d6efd;
        color: white;
    }
    
    .btn-outline-secondary {
        background: transparent;
        border: 1px solid #6c757d;
        color: #6c757d;
    }
    
    .btn-outline-dark {
        background: transparent;
        border: 1px solid #212529;
        color: #212529;
    }
    
    .btn-success {
        background: #198754;
        color: white;
    }
    
    .btn-danger {
        background: #dc3545;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="report-page">

{{-- Screen Filter Section (hidden in print) --}}
<div class="filter-card no-print">
    <form method="GET" action="{{ route('archivebook_report') }}" id="filterForm">
        <div class="filter-row">
            <div class="filter-group">
                <label>از سال</label>
                <select name="year_from">
                    <option value="">— انتخاب —</option>
                    @foreach($archiveYears as $id => $year)
                        <option value="{{ $year }}" @selected(request('year_from') == $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>الی سال</label>
                <select name="year_to">
                    <option value="">— انتخاب —</option>
                    @foreach($archiveYears as $id => $year)
                        <option value="{{ $year }}" @selected(request('year_to') == $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>ولایت</label>
                <select name="province_id">
                    <option value="">— همه —</option>
                    @foreach($provinces as $id => $name)
                        <option value="{{ $id }}" @selected(request('province_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>پوهنتون</label>
                <select name="university_id" id="university_select">
                    <option value="">— همه —</option>
                    @foreach($universitiesList as $id => $name)
                        <option value="{{ $id }}" @selected(request('university_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>پوهنځی</label>
                <select name="faculty_id" id="faculty_select">
                    <option value="">— همه —</option>
                    @foreach($faculties as $id => $name)
                        <option value="{{ $id }}" @selected(request('faculty_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>دیپارتمنت</label>
                <select name="department_id" id="department_select">
                    <option value="">— همه —</option>
                    @foreach($departments as $id => $name)
                        <option value="{{ $id }}" @selected(request('department_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>مقطع تحصیلی</label>
                <select name="grade_id">
                    <option value="">— همه —</option>
                    @foreach($grades as $id => $name)
                        <option value="{{ $id }}" @selected(request('grade_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>وضعیت</label>
                <select name="status">
                    <option value="">— همه —</option>
                    <option value="registered" @selected(request('status') === 'registered')>حالت معمولی</option>
                    <option value="complete" @selected(request('status') === 'complete')>تکمیل شده</option>
                </select>
            </div>

            <div class="filter-group" style="flex-direction: row; gap: 8px;">
                <button type="submit" class="btn-rpt btn-primary">فلتر</button>
                <a href="{{ route('archivebook_report') }}" class="btn-rpt btn-outline-secondary">پاک</a>
                <button type="button" class="btn-rpt btn-outline-dark" onclick="window.print()">چاپ</button>
                <button type="button" class="btn-rpt btn-success" id="exportExcelBtn">خروجی Excel</button>
                <button type="button" class="btn-rpt btn-danger" id="exportPdfBtn">خروجی PDF</button>
            </div>
        </div>
    </form>
</div>

{{-- Summary Cards - فقط در صفحه نمایش داده می‌شود (در PDF مخفی است) --}}
<div class="summary-grid no-print">
    <div class="sum-card total-card">
        <div class="sum-val">{{ number_format($cardTotals['total_books']) }}</div>
        <div class="sum-lbl">📚 مجموع کتاب‌ها</div>
    </div>
    <div class="sum-card complete-card">
        <div class="sum-val">{{ number_format($cardTotals['complete']) }}</div>
        <div class="sum-lbl">✅ تکمیل شده</div>
    </div>
    <div class="sum-card remain-card">
        <div class="sum-val">{{ number_format($cardTotals['remaining']) }}</div>
        <div class="sum-lbl">⏳ باقی مانده</div>
    </div>
</div>

{{-- PDF Report Content --}}
<div id="pdfContent">

{{-- Header with Logo --}}
<div class="header-with-logo">
    <div class="logo-left">
        <img src="{{ ('img/wezarat-logo.jpg') }}" alt="لوگو وزارت">
    </div>
    <div class="header-text">
        <div class="header-line">امارت اسلامی افغانستان</div>
        <div class="header-line">وزارت تحصیلات عالی</div>
        <div class="header-line">معینیت امور محصلان</div>
        <div class="header-line">ریاست امور محصلان خصوصی</div>
        <div class="header-line">آمریت دیتابیس</div>
    </div>
    <div class="logo-right">
        <img src="{{('img/emarat-logo.jpg') }}" alt="لوگو وزارت">
    </div>
</div>

{{-- Date Range --}}
<div class="date-range">
    <h3>
        راپور وضعیت تعداد کتاب‌های اضافه شده در سیستم آرشیف
        
        از تاریخ:
        @if(request('year_from') || request('year_to'))
            @if(request('year_from')) {{ request('year_from') }} @else ابتدا @endif
            -
            @if(request('year_to')) {{ request('year_to') }} @else اکنون @endif
        @else
            همه سال‌ها
        @endif
    </h3>
</div>

{{-- Main Table --}}
<table class="report-table">
    <thead>
        <tr>
            <th style="width: 50px;">شماره</th>
            <th style="width: 170px;">اسم نهاد</th>
            <th style="width: 90px;">ولایت</th>
            <th style="width: 100px;">مقطع تحصیلی</th>
            <th style="width: 120px;">پوهنځی</th>
            <th style="width: 130px;">دیپارتمنت</th>
            <th style="width: 80px;">اضافه شده</th>
            <th style="width: 80px;">تکمیل شده</th>
            <th style="width: 80px;">باقی مانده</th>
        </tr>
    </thead>
    <tbody>
        @forelse($reportRows as $index => $row)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td class="text-right"><strong>{{ $row['university_name'] }}</strong></td>
            <td>{{ $row['province'] }}</td>
            <td>{{ $row['grade'] }}</td>
            <td>{{ $row['faculty'] }}</td>
            <td>{{ $row['department'] }}</td>
            <td>{{ number_format($row['total_books']) }}</td>
            <td>{{ number_format($row['complete']) }}</td>
            <td>{{ number_format($row['remaining']) }}</td>
        </tr>
        @empty
        <tr class="empty-row">
            <td colspan="9" style="text-align: center; padding: 40px;">هیچ داده‌ای پیدا نشد</td>
        </table>
        @endforelse
    </tbody>
    @if($reportRows->isNotEmpty())
    <tfoot>
        <tr>
            <td colspan="6" style="text-align: left; font-weight: 700;">مجموع کل</td>
            <td style="font-weight: 700;">{{ number_format($totals['total_books']) }}</td>
            <td style="font-weight: 700;">{{ number_format($totals['complete']) }}</td>
            <td style="font-weight: 700;">{{ number_format($totals['remaining']) }}</td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- Confirmation Text --}}
<div class="confirmation-text">
    قرار شرح فوق جدول هذا صحت است
</div>


{{-- Footer --}}
<div class="footer-text">
    با احترام
</div>

</div>{{-- end pdfContent --}}

</div>{{-- end report-page --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    // ==============================================
    // Export to Excel
    // ==============================================
    document.getElementById('exportExcelBtn')?.addEventListener('click', function() {
        const table = document.querySelector('.report-table');
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.table_to_sheet(table, { raw: true });
        
        ws['!cols'] = [
            {wch: 8}, {wch: 25}, {wch: 15}, {wch: 15}, {wch: 18}, {wch: 20}, {wch: 12}, {wch: 12}, {wch: 12}
        ];
        
        XLSX.utils.book_append_sheet(wb, ws, 'راپور کتاب‌ها');
        XLSX.writeFile(wb, `rapor_ketabha_${new Date().toISOString().slice(0,19)}.xlsx`);
    });

    // ==============================================
    // Export to PDF
    // ==============================================
    document.getElementById('exportPdfBtn')?.addEventListener('click', function() {
        const element = document.getElementById('pdfContent');
        const opt = {
            margin: [0.5, 0.5, 0.5, 0.5],
            filename: `rapor_ketabha_${new Date().toISOString().slice(0,19)}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, letterRendering: true },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
        };
        html2pdf().set(opt).from(element).save();
    });

    // ==============================================
    // Cascading/Dependent Filters
    // ==============================================

    // داده‌های پوهنځی‌ها و دیپارتمنت‌ها از سرور
    const facultiesData = @json($facultiesWithUniversity);
    const departmentsData = @json($departmentsWithFaculty);

    // عناصر DOM
    const universitySelect = document.getElementById('university_select');
    const facultySelect = document.getElementById('faculty_select');
    const departmentSelect = document.getElementById('department_select');

    // ذخیره گزینه‌های اصلی
    let originalFacultyOptions = [];
    let originalDepartmentOptions = [];

    // ذخیره گزینه‌های اصلی پوهنځی
    if (facultySelect) {
        for (let i = 0; i < facultySelect.options.length; i++) {
            const option = facultySelect.options[i];
            const facultyId = option.value;
            originalFacultyOptions.push({
                value: option.value,
                text: option.text,
                universityId: facultiesData[facultyId] || ''
            });
        }
    }

    // ذخیره گزینه‌های اصلی دیپارتمنت
    if (departmentSelect) {
        for (let i = 0; i < departmentSelect.options.length; i++) {
            const option = departmentSelect.options[i];
            const deptId = option.value;
            originalDepartmentOptions.push({
                value: option.value,
                text: option.text,
                facultyId: departmentsData[deptId] || ''
            });
        }
    }

    // فیلتر پوهنځی بر اساس پوهنتون انتخاب شده
    function filterFacultiesByUniversity(selectedUniversityId) {
        if (!facultySelect) return;
        
        facultySelect.innerHTML = '<option value="">— همه پوهنځی‌ها —</option>';
        
        if (!selectedUniversityId) {
            originalFacultyOptions.forEach(option => {
                if (option.value) {
                    const optionElement = document.createElement('option');
                    optionElement.value = option.value;
                    optionElement.textContent = option.text;
                    facultySelect.appendChild(optionElement);
                }
            });
        } else {
            originalFacultyOptions.forEach(option => {
                if (option.value && String(option.universityId) === String(selectedUniversityId)) {
                    const optionElement = document.createElement('option');
                    optionElement.value = option.value;
                    optionElement.textContent = option.text;
                    facultySelect.appendChild(optionElement);
                }
            });
        }
        
        const selectedFacultyId = facultySelect.value;
        filterDepartmentsByFaculty(selectedFacultyId);
    }

    // فیلتر دیپارتمنت بر اساس پوهنځی انتخاب شده
    function filterDepartmentsByFaculty(selectedFacultyId) {
        if (!departmentSelect) return;
        
        departmentSelect.innerHTML = '<option value="">— همه دیپارتمنت‌ها —</option>';
        
        if (!selectedFacultyId) {
            originalDepartmentOptions.forEach(option => {
                if (option.value) {
                    const optionElement = document.createElement('option');
                    optionElement.value = option.value;
                    optionElement.textContent = option.text;
                    departmentSelect.appendChild(optionElement);
                }
            });
        } else {
            originalDepartmentOptions.forEach(option => {
                if (option.value && String(option.facultyId) === String(selectedFacultyId)) {
                    const optionElement = document.createElement('option');
                    optionElement.value = option.value;
                    optionElement.textContent = option.text;
                    departmentSelect.appendChild(optionElement);
                }
            });
        }
    }

    // رویداد تغییر پوهنتون
    if (universitySelect) {
        universitySelect.addEventListener('change', function() {
            const selectedUniversityId = this.value;
            filterFacultiesByUniversity(selectedUniversityId);
        });
    }

    // رویداد تغییر پوهنځی
    if (facultySelect) {
        facultySelect.addEventListener('change', function() {
            const selectedFacultyId = this.value;
            filterDepartmentsByFaculty(selectedFacultyId);
        });
    }

    // اجرای اولیه برای تنظیم فیلترها در بارگذاری صفحه
    document.addEventListener('DOMContentLoaded', function() {
        const selectedUniversityId = universitySelect?.value;
        const selectedFacultyId = facultySelect?.value;
        
        if (selectedUniversityId) {
            filterFacultiesByUniversity(selectedUniversityId);
            
            if (selectedFacultyId && facultySelect) {
                setTimeout(() => {
                    facultySelect.value = selectedFacultyId;
                    filterDepartmentsByFaculty(selectedFacultyId);
                    
                    const selectedDepartmentId = '{{ request("department_id") }}';
                    if (selectedDepartmentId && departmentSelect) {
                        setTimeout(() => {
                            departmentSelect.value = selectedDepartmentId;
                        }, 100);
                    }
                }, 100);
            }
        }
    });
</script>
@endpush