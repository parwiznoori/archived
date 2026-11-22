@extends('layouts.app')

@push('styles')
    <link href="{{ asset('css/archive-csv.css') }}?v={{ filemtime(public_path('css/archive-csv.css')) }}" rel="stylesheet">
@endpush

@section('content')
<div class="csv-workspace" dir="rtl">


    <div class="csv-hero">
        <div><h2>{{ $archive->book_name }}</h2><p>{{ $universities[$archive->university_id] ?? '—' }}</p></div>
        <a href="{{ route('downloadTemplate') }}" class="csv-btn"><i class="fa fa-download" aria-hidden="true"></i> دانلود قالب CSV</a>
    </div>

    @foreach (['error' => 'danger', 'success' => 'success', 'message' => 'info'] as $key => $style)
        @if(session($key))<div class="alert alert-{{ $style }}">{{ session($key) }}</div>@endif
    @endforeach
    @if($errors->any())
        <div class="alert alert-danger" role="alert"><strong>فایل قابل پردازش نیست</strong><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    @if($report = session('csv_report'))
        <section class="csv-panel" aria-label="نتیجه آپلود">
            <h3>نتیجه بررسی فایل CSV</h3>
            <div class="csv-grid">
                <div class="csv-stat good"><strong>{{ $report['inserted'] }}</strong>محصل درج شد</div>
                <div class="csv-stat warn"><strong>{{ $report['duplicate'] }}</strong>محصل از قبل موجود / تکراری</div>
                <div class="csv-stat bad"><strong>{{ $report['error'] }}</strong>ردیف نیازمند اصلاح</div>
                <div class="csv-stat"><strong>{{ $report['empty'] }}</strong>ردیف صفحه بدون محصل</div>
            </div>
            <p class="csv-hint mt-3">ردیف‌های سالم درج شدند. خطاها را اصلاح و فایل را دوباره آپلود کنید؛ محصلین موجود دوباره درج نمی‌شوند. شماره ردیف از عنوان ستون‌ها (ردیف ۱) حساب می‌شود.</p>
            <div class="csv-scroll">
                <table><thead><tr><th>صفحه فایل</th><th>درج شد</th><th>تکراری</th><th>خطا</th><th>بدون محصل</th></tr></thead><tbody>
                @foreach($report['pages'] as $page => $counts)
                    <tr><td>{{ $page }}</td><td>{{ $counts['inserted'] }}</td><td>{{ $counts['duplicate'] }}</td><td>{{ $counts['error'] }}</td><td>{{ $counts['empty'] }}</td></tr>
                @endforeach
                </tbody></table>
            </div>
            <details open class="mt-3"><summary>جزئیات ردیف‌ها و ستون‌های مشکل‌دار ({{ $report['total'] }} ردیف)</summary>
                <div class="csv-scroll"><table><thead><tr><th>ردیف CSV</th><th>صفحه فایل</th><th>نام محصل</th><th>نتیجه</th><th>توضیح / روش اصلاح</th></tr></thead><tbody>
                @foreach($report['details'] as $detail)
                    <tr><td>{{ $detail['row'] }}</td><td>{{ $detail['page'] ?? '—' }}</td><td>{{ $detail['name'] ?? '—' }}</td><td><span class="csv-label {{ $detail['status'] }}">{{ ['inserted'=>'درج شد','duplicate'=>'تکراری','error'=>'خطا','empty'=>'بدون محصل'][$detail['status']] }}</span></td><td>{{ $detail['message'] }}</td></tr>
                @endforeach
                </tbody></table></div>
            </details>
        </section>
    @endif

    <section class="csv-panel">
        <h3>مشخصات کتاب</h3>
        <div class="csv-grid">
            <div class="csv-stat"><span class="csv-hint">شناسه کتاب / archive_id</span><strong>{{ $archive->id }} <button type="button" class="csv-copy" data-copy="{{ $archive->id }}">کپی</button></strong></div>
            <div class="csv-stat"><span class="csv-hint">{{ $universities[$archive->university_id] ?? 'پوهنتون' }}</span><strong>{{ $archive->university_id }} <button type="button" class="csv-copy" data-copy="{{ $archive->university_id }}">کپی</button></strong><span class="csv-hint">university_id</span></div>
            <div class="csv-stat"><span class="csv-hint">صفحات کتاب</span><strong>{{ $totalPages }}</strong></div>
        </div>
        <div class="table-responsive mt-3"><table><thead><tr><th>پوهنځی</th><th>faculty_id</th><th>دیپارتمنت</th><th>department_id</th></tr></thead><tbody>
            @foreach($archiveDepartments as $item)<tr><td>{{ $item->faculty->name ?? '—' }}</td><td>{{ $item->faculty_id }} <button type="button" class="csv-copy" data-copy="{{ $item->faculty_id }}">کپی</button></td><td>{{ $item->department->name ?? '—' }}</td><td>{{ $item->department_id }} <button type="button" class="csv-copy" data-copy="{{ $item->department_id }}">کپی</button></td></tr>@endforeach
        </tbody></table></div>
    </section>

    <section class="csv-panel">
        <h3>آپلود فایل CSV</h3>
        <details class="mb-3"><summary>راهنمای تکمیل ستون‌ها</summary>
            <ul class="csv-hint">
                <li>فایل را با قالب CSV UTF-8 ذخیره کنید؛ کاما، نقطه‌ویرگول و Tab پشتیبانی می‌شوند.</li>
                <li><code>archiveimage_id</code> شماره صفحه در کتاب است، نه شناسه داخلی تصویر.</li>
                <li><code>column_number</code> شماره محصل در صفحه است؛ برای صفحه بدون محصل عدد صفر وارد کنید.</li>
                <li>برای محصل، ستون‌های شماره صفحه، شماره محصل، پوهنتون، پوهنځی، دیپارتمنت، نام، نام پدر، سال شمولیت و سال فراغت ضروری هستند.</li>
                <li>ستون‌های اختیاری می‌توانند خالی باشند. نام انگلیسی عنوان ستون‌ها را حفظ کنید؛ ترتیب آن‌ها قابل تغییر است.</li>
                <li><code>grade_id</code>: ۱ چهارده‌پاس، ۲ لیسانس، ۳ ماستر. <code>semester_type_id</code>: ۱ بهاری، ۲ خزانی.</li>
                <li>محصل با مشخصات یکسان در همین کتاب تکراری است؛ در پیام نتیجه، صفحه موجود نمایش داده می‌شود.</li>
            </ul>
        </details>
        <form id="uploadForm" action="{{ route('archivedata.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="archive_id" value="{{ $archive->id }}">
            <div class="csv-drop" id="dropArea">
                <i class="fas fa-file-csv fa-2x mb-3" aria-hidden="true"></i>
                <div><label for="csv_file"><strong>فایل CSV را انتخاب کنید یا اینجا رها کنید</strong></label></div>
                <input type="file" name="csv_file" id="csv_file" accept=".csv,text/csv" required>
                <div id="fileLabel" class="csv-hint" aria-live="polite">حداکثر حجم فایل: ۹ مگابایت</div>
                <div id="fileError" class="text-danger mt-2" role="alert"></div>
            </div>
            <div class="csv-actions"><button type="submit" class="csv-btn primary" id="submitBtn">بررسی و درج محصلین</button><a href="{{ route('archive.index') }}" class="csv-btn">بازگشت به کتاب‌ها</a></div>
        </form>
        <div class="csv-danger-zone"><div><strong>حذف محصلین این کتاب</strong><div class="csv-hint">تمام محصلین کتاب حذف می‌شوند. پس از حذف، می‌توانید CSV را دوباره درج کنید.</div></div>
            <form action="{{ route('import.undoBookUpload', $archive->id) }}" method="POST" onsubmit="return confirm('تمام محصلین این کتاب حذف شوند؟')">@csrf<button type="submit" class="csv-btn danger">حذف کل محصلین کتاب</button></form>
        </div>
    </section>

    <section class="csv-panel"><h3>صفحات کتاب</h3>
        <div class="csv-pages">
            @foreach($archiveImages as $image)
                <div class="csv-page {{ $image->archivedatas->isNotEmpty() ? 'filled' : '' }}">صفحه {{ $image->book_pagenumber }} · {{ $image->archivedatas->count() }} محصل {{ $image->status_id == 2 ? '(بدون محصل)' : '' }}</div>
            @endforeach
        </div>
        <details class="mt-3"><summary>مشاهده محصلین درج‌شده در صفحات</summary><div class="csv-scroll"><table><thead><tr><th>صفحه</th><th>شماره محصل</th><th>نام و تخلص</th></tr></thead><tbody>
            @foreach($archiveImages as $image)
                @foreach($image->archivedatas as $student)
                    <tr><td>{{ $image->book_pagenumber }}</td><td>{{ $student->column_number }}</td><td>{{ $student->name }} {{ $student->last_name }}</td></tr>
                @endforeach
            @endforeach
        </tbody></table></div></details>
    </section>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('uploadForm'), input = document.getElementById('csv_file'), area = document.getElementById('dropArea'), label = document.getElementById('fileLabel'), error = document.getElementById('fileError'), button = document.getElementById('submitBtn');
    document.querySelectorAll('.csv-copy').forEach(button => button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(button.dataset.copy);
            button.textContent = 'کپی شد';
            setTimeout(() => { button.textContent = 'کپی'; }, 1800);
        } catch (_) { button.textContent = 'کپی نشد'; }
    }));
    function validate() {
        const file = input.files[0];
        error.textContent = '';
        label.textContent = file ? file.name : 'حداکثر حجم فایل: ۹ مگابایت';
        if (!file) { error.textContent = 'لطفاً یک فایل CSV انتخاب کنید.'; return false; }
        if (!file.name.toLowerCase().endsWith('.csv')) { error.textContent = 'فقط فایل CSV قابل قبول است.'; return false; }
        if (file.size > 9 * 1024 * 1024) { error.textContent = 'حجم فایل نباید بیشتر از ۹ مگابایت باشد.'; return false; }
        return true;
    }
    input.addEventListener('change', validate);
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(event => area.addEventListener(event, e => { e.preventDefault(); e.stopPropagation(); area.classList.toggle('active', event === 'dragenter' || event === 'dragover'); }));
    area.addEventListener('drop', e => { if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; validate(); } });
    form.addEventListener('submit', e => { if (!validate()) { e.preventDefault(); return; } button.disabled = true; button.textContent = 'در حال بررسی و درج…'; });
});
</script>
@endsection
