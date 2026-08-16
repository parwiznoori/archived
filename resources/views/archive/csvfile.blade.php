@extends('layouts.app')

@section('content')
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if (session('row_errors'))
        <div class="alert alert-danger">
            <strong>{{ trans('general.validation_errors') }}</strong>
            <ul>
                @foreach (session('row_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

       @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

   
    <style>
        /* حذف تمام پس‌زمینه‌ها */
        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            background: #ffffff;
        }
        
        .card-header {
            background: transparent;
            border-bottom: 2px solid #4e73df;
            padding: 15px 20px;
        }
        
        .card-body {
            padding: 20px;
            background: transparent;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            font-weight: 600;
            color: #4a5568;
            border-top: none;
            background: transparent;
        }
        
        .table td {
            border-top: 1px solid #e2e8f0;
        }
        
        .copy-btn {
            padding: 4px 10px;
            font-size: 12px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        
        .copy-btn:hover {
            transform: translateY(-1px);
        }
        
        .page-badge {
            display: inline-block;
            width: 36px;
            height: 36px;
            line-height: 36px;
            text-align: center;
            background: white;
            border: 2px solid #4e73df;
            color: #4e73df;
            border-radius: 6px;
            margin: 3px;
            font-weight: 600;
            transition: all 0.3s;
            cursor: default;
        }
        
        .page-badge:hover {
            background: #4e73df;
            color: white;
            transform: translateY(-2px);
        }
        
        .page-badge.empty {
            border-color: #a0aec0;
            color: #a0aec0;
        }
        
        .page-badge.filled {
            border-color: #38a169;
            color: #38a169;
        }
        
        .info-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .code-example {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            direction: ltr;
            text-align: left;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.4;
            overflow-x: auto;
            white-space: nowrap;
        }
        
        .badge-primary-custom {
            background: #4e73df;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
        }
        
        .badge-success-custom {
            background: #38a169;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
        }
        
        .file-upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 6px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .file-upload-area:hover {
            border-color: #4e73df;
        }
        
        .btn-custom-primary {
            background: #4e73df;
            border-color: #4e73df;
            color: white;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.3s;
        }
        
        .btn-custom-primary:hover {
            background: #2d50c3;
            border-color: #2d50c3;
            transform: translateY(-1px);
        }
        
        .btn-custom-secondary {
            background: #718096;
            border-color: #718096;
            color: white;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.3s;
        }
        
        .btn-custom-secondary:hover {
            background: #4a5568;
            border-color: #4a5568;
            transform: translateY(-1px);
        }
        
        .btn-custom-danger {
            background: #e53e3e;
            border-color: #e53e3e;
            color: white;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.3s;
        }
        
        .btn-custom-danger:hover {
            background: #c53030;
            border-color: #c53030;
            transform: translateY(-1px);
        }
        
        .section-title {
            color: #4a5568;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .custom-file-label {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .btn-delete-book {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            border-color: #c53030;
            color: white;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.3s;
        }
        
        .btn-delete-book:hover {
            background: linear-gradient(135deg, #c53030 0%, #9b2c2c 100%);
            border-color: #9b2c2c;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
        }
        
        /* استایل برای SweetAlert فارسی */
        .swal2-title {
            direction: rtl !important;
            text-align: right !important;
        }
        
        .swal2-html-container {
            direction: rtl !important;
            text-align: right !important;
        }
        
        .swal2-confirm, .swal2-cancel {
            font-family: inherit !important;
        }
    </style>

    <div class="container-fluid">
        <!-- بخش اطلاعات آرشیف -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="m-0 font-weight-bold text-gray-800">📚 اطلاعات آرشیف</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>نام آرشیف</th>
                                <th>ID آرشیف</th>
                                <th>پوهنتون</th>
                                <th>ایدی پوهنتون</th>
                                <th>نام پوهنځی</th>
                                <th>ایدی پوهنځی</th>
                                <th>نام دیپارتمنت</th>
                                <th>ایدی دیپارتمنت</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-light">
                                <td><strong>{{ $archive->book_name }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <strong>{{ $archive->id }}</strong>
                                        <button class="btn btn-sm btn-outline-primary copy-btn ml-2" 
                                                data-copy="{{ $archive->id }}">
                                            کپی
                                        </button>
                                    </div>
                                </td>
                                <td>{{ $universities[$archive->university_id] ?? '---' }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        {{ $archive->university_id }}
                                        <button class="btn btn-sm btn-outline-primary copy-btn ml-2" 
                                                data-copy="{{ $archive->university_id }}">
                                            کپی
                                        </button>
                                    </div>
                                </td>
                                <td colspan="4" class="text-center">
                                    <span class="badge badge-light">جزئیات پوهنځی و دیپارتمنت</span>
                                </td>
                            </tr>
                            @foreach ($archiveDepartments as $archiveDepartment)
                                <tr>
                                    <td colspan="3"></td>
                                    <td></td>
                                    <td>{{ $archiveDepartment->faculty->name ?? '---' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            {{ $archiveDepartment->faculty_id }}
                                            <button class="btn btn-sm btn-outline-secondary copy-btn ml-2" 
                                                    data-copy="{{ $archiveDepartment->faculty_id }}">
                                                کپی
                                            </button>
                                        </div>
                                    </td>
                                    <td>{{ $archiveDepartment->department->name ?? '---' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            {{ $archiveDepartment->department_id }}
                                            <button class="btn btn-sm btn-outline-secondary copy-btn ml-2" 
                                                    data-copy="{{ $archiveDepartment->department_id }}">
                                                کپی
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- بخش نمایش صفحات کتاب -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="m-0 font-weight-bold text-gray-800">📄 صفحات کتاب ({{ $totalPages }} صفحه)</h5>
            </div>
            <div class="card-body">
                @php
                    $archiveImages = \App\Models\Archiveimage::where('archive_id', $archive->id)
                        ->orderBy('book_pagenumber')
                        ->get();
                    
                    $pageStudents = [];
                    foreach ($archiveImages as $image) {
                        $students = \App\Models\Archivedata::where('archiveimage_id', $image->id)
                            ->select('name', 'last_name', 'column_number')
                            ->get();
                        $pageStudents[$image->book_pagenumber] = $students;
                    }
                @endphp
                
                <div class="mb-4">
                    <div class="d-flex flex-wrap mb-4">
                        @foreach($archiveImages as $image)
                            @php
                                $studentCount = $pageStudents[$image->book_pagenumber]->count();
                                $statusClass = $studentCount > 0 ? 'filled' : ($image->status_id == 2 ? 'empty' : '');
                            @endphp
                            <div class="page-badge {{ $statusClass }}" 
                                 title="صفحه {{ $image->book_pagenumber }} | محصلین: {{ $studentCount }}">
                                {{ $image->book_pagenumber }}
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="alert alert-light border">
                        <div class="row">
                            <div class="col-12">
                                
                                <ul class="mb-0">
                                    <h5 class="font-weight-bold text-dark mb-3">راهنما:</h5>
                                    <li>در فایل CSV ستون <code>archiveimage_id</code> باید شماره صفحه را وارد کنید</li>
                                    <li>ستون <code>column_number</code> تعداد محصل در صفحه است (برای صفحه خالی: <strong>0</strong>)</li>
                                    <li>ابتدا قالب را دانلود و سپس پر کنید</li>
                                    <li>ترتیب ستون‌ها را تغییر ندهید</li>
                                </ul>
                            </div>
                        </div>
                           <!-- بخش کدهای سیستم -->
                <div class="card mb-4">
                   
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="info-box">
                                    <h5 class="font-weight-bold text-dark mb-3">🎓 مقطع تحصیلی</h5>
                                    <div>
                                        <span class="badge badge-primary-custom mb-3 d-block">1 = چهارده پاس</span>
                                        <span class="badge badge-primary-custom mb-3 d-block">2 = لیسانس</span>
                                        <span class="badge badge-primary-custom d-block">3 = ماستر</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-box">
                                    <h5 class="font-weight-bold text-dark mb-3">📅 سمستر</h5>
                                    <div>
                                        <span class="badge badge-success-custom mb-3 d-block">1 = سمستر بهاری</span>
                                        <span class="badge badge-success-custom d-block">2 = سمستر خزانی</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    </div>
                </div>

             
                
                <br>
                <!-- دکمه دانلود قالب -->
                <div class="text-left mb-4">
                    <a href="{{ route('downloadTemplate') }}" class="btn btn-custom-primary btn-lg">
                        📥 دانلود قالب فایل CSV
                    </a>
                </div>
                
                @if($archiveImages->count() > 0)
                    <div class="row">
                        @foreach($archiveImages as $image)
                            @if($pageStudents[$image->book_pagenumber]->count() > 0)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-left">
                                        <div class="card-header py-2">
                                            <h6 class="m-0 font-weight-bold">
                                                صفحه {{ $image->book_pagenumber }}
                                                <span class="badge badge-primary float-right">
                                                    {{ $pageStudents[$image->book_pagenumber]->count() }} محصل
                                                </span>
                                            </h6>
                                        </div>
                                        <div class="card-body py-3">
                                            <ul class="list-unstyled mb-0">
                                                @foreach($pageStudents[$image->book_pagenumber] as $student)
                                                    <li class="mb-2">
                                                        <div class="font-weight-bold">{{ $student->name }} {{ $student->last_name }}</div>
                                                        <div class="text-muted small">شماره: {{ $student->column_number }}</div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- بخش آپلود فایل -->
        <div class="card">
            <div class="card-header">
                <h5 class="m-0 font-weight-bold text-gray-800">⬆️ آپلود فایل CSV</h5>
            </div>
            <div class="card-body">
                {!! Form::open(['route' => 'archivedata.import', 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'id' => 'uploadForm']) !!}
                
                <div class="file-upload-area mb-4" id="dropArea">
                    <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-3"></i>
                    <h5 class="font-weight-bold text-dark mb-3">فایل CSV را انتخاب کنید</h5>
                    <p class="text-muted mb-3">حداکثر حجم: 9MB</p>
                    <div class="custom-file" style="max-width: 400px; margin: 0 auto;">
                        {!! Form::file('csv_file', [
                            'class' => 'custom-file-input', 
                            'id' => 'csv_file',
                            'accept' => '.csv',
                            'required' => true
                        ]) !!}
                        {{-- <label class="custom-file-label text-left" for="csv_file" id="fileLabel">
                            انتخاب فایل CSV
                        </label> --}}
                    </div>
                    @if ($errors->has('csv_file'))
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="fas fa-exclamation-circle"></i> {{ $errors->first('csv_file') }}
                        </div>
                    @endif
                </div>
                <br>
                <!-- فیلدهای مخفی -->
                {!! Form::hidden('university_id', $archive->university_id) !!}
                {!! Form::hidden('archive_id', $archive->id) !!}
                {!! Form::hidden('department_id', $archive->department_id) !!}

                <!-- دکمه‌های عملیات -->
                <div class="form-actions text-center pt-4 border-top">
                    <button type="submit" class="btn btn-custom-primary btn-lg mx-2 mb-2" id="submitBtn">
                        📤 آپلود فایل
                    </button>

                    <a href="{{ route('archive.index') }}" class="btn btn-custom-secondary btn-lg mx-2 mb-2">
                        ❌ انصراف
                    </a>

                    {{-- <!-- حذف آخرین آپلود -->
                    <a href="{{ route('import.undoLastUpload', $archive->id)) }}"
                         data-book-name="{{ $archive->book_name }}" 
                       class="btn btn-warning btn-lg mx-2 mb-2"
                       onclick="return confirm('آیا از حذف آخرین آپلود مطمئن هستید؟')">
                        ↩️ لغو آخرین آپلود
                    </a> --}}

                    <!-- حذف کل کتاب -->
                    <button type="button" 
                            class="btn btn-delete-book btn-lg mx-2 mb-2"
                            id="deleteBookBtn"
                            data-url="{{ route('import.undoBookUpload', $archive->id) }}"
                            data-book-name="{{ $archive->book_name }}">
                        🗑️ حذف کل کتاب
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- اسکریپت‌ها -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // چاپ صفحه (اگر دکمه چاپ وجود دارد)
            const printButton = document.getElementById('printButton');
            if (printButton) {
                printButton.addEventListener('click', function() {
                    window.print();
                });
            }

            // عملکرد کپی کردن
            document.querySelectorAll('.copy-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const text = this.getAttribute('data-copy');
                    
                    if (!text) return;
                    
                    navigator.clipboard.writeText(text).then(() => {
                        const originalText = this.innerHTML;
                        this.innerHTML = 'کپی شد ✓';
                        this.classList.add('btn-success');
                        this.classList.remove('btn-outline-primary', 'btn-outline-secondary');
                        
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.classList.remove('btn-success');
                            if (originalText.includes('کپی')) {
                                this.classList.add('btn-outline-primary');
                            } else {
                                this.classList.add('btn-outline-secondary');
                            }
                        }, 2000);
                    });
                });
            });

            // نمایش نام فایل
            const fileInput = document.getElementById('csv_file');
            const fileLabel = document.getElementById('fileLabel');
            const dropArea = document.getElementById('dropArea');
            
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        fileLabel.textContent = this.files[0].name;
                        dropArea.style.borderColor = '#38a169';
                        
                        const fileName = this.files[0].name.toLowerCase();
                        if (!fileName.endsWith('.csv')) {
                            alert('فقط فایل‌های CSV مجاز هستند');
                            this.value = '';
                            fileLabel.textContent = 'انتخاب فایل CSV';
                            dropArea.style.borderColor = '#cbd5e0';
                        }
                    }
                });
            }

            // اعتبارسنجی فرم
            const form = document.getElementById('uploadForm');
            const submitBtn = document.getElementById('submitBtn');
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!fileInput || !fileInput.files.length) {
                        e.preventDefault();
                        alert('لطفا یک فایل CSV انتخاب کنید');
                        return false;
                    }
                    
                    const file = fileInput.files[0];
                    if (file.size > 9 * 1024 * 1024) {
                        e.preventDefault();
                        alert('حجم فایل نباید بیشتر از 9 مگابایت باشد');
                        return false;
                    }
                    
                    if (!file.name.toLowerCase().endsWith('.csv')) {
                        e.preventDefault();
                        alert('فقط فایل‌های CSV قابل قبول هستند');
                        return false;
                    }
                    
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span>در حال آپلود...';
                    }
                    
                    return true;
                });
            }

            // ==================== دکمه حذف کل کتاب ====================
            const deleteBookBtn = document.getElementById('deleteBookBtn');
            
            if (deleteBookBtn) {
                deleteBookBtn.addEventListener('click', function() {
                    const url = this.getAttribute('data-url');
                    const bookName = this.getAttribute('data-book-name');
                    
                    // استفاده از SweetAlert برای تایید
                    Swal.fire({
                        title: '⚠️ حذف کل کتاب',
                        html: `
                            <div style="text-align: right; direction: rtl; font-family: Tahoma;">
                                <p style="font-size: 16px; margin-bottom: 15px;">آیا مطمئن هستید که می‌خواهید تمام محصلین این کتاب را حذف کنید؟</p>
                                <p style="font-size: 15px; margin-bottom: 10px;">
                                    <strong style="color: #4a5568;">📚 نام کتاب:</strong> 
                                    <span style="color: #2d3748; font-weight: bold;">${bookName}</span>
                                </p>
                                <p style="color: #e53e3e; font-weight: bold; font-size: 14px; border: 1px solid #e53e3e; padding: 10px; border-radius: 5px; background: #fff5f5;">
                                    ⚠️ این عمل تمام محصلین کتاب را حذف کرده و قابل بازگشت نیست!
                                </p>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: '🗑️ بله، حذف کن',
                        cancelButtonText: '❌ انصراف',
                        reverseButtons: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        customClass: {
                            popup: 'animated fadeIn',
                            title: 'text-right',
                            htmlContainer: 'text-right'
                        },
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // نمایش پیام لودینگ
                            Swal.fire({
                                title: 'در حال حذف...',
                                html: `
                                    <div style="text-align: center;">
                                        <div class="spinner-border text-danger mb-3" role="status" style="width: 3rem; height: 3rem;">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <p style="font-size: 16px;">در حال حذف محصلین کتاب "${bookName}"</p>
                                        <p style="font-size: 14px; color: #718096;">لطفاً چند لحظه صبر کنید...</p>
                                    </div>
                                `,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    // پس از 500 میلی‌ثانیه به صفحه حذف هدایت شو
                                    setTimeout(() => {
                                        window.location.href = url;
                                    }, 500);
                                }
                            });
                        }
                    });
                });
            }
            
            // لاگ برای دیباگ
            console.log('Page loaded successfully');
            console.log('Delete button:', deleteBookBtn);
            if (deleteBookBtn) {
                console.log('Delete URL:', deleteBookBtn.getAttribute('data-url'));
                console.log('Book Name:', deleteBookBtn.getAttribute('data-book-name'));
            }
        });

        // تابع برای درگ و دراپ (اختیاری)
        if (document.getElementById('dropArea')) {
            const dropArea = document.getElementById('dropArea');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                dropArea.style.borderColor = '#4e73df';
                dropArea.style.backgroundColor = 'rgba(78, 115, 223, 0.05)';
            }
            
            function unhighlight() {
                dropArea.style.borderColor = '#cbd5e0';
                dropArea.style.backgroundColor = 'transparent';
            }
            
            dropArea.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length > 0) {
                    fileInput.files = files;
                    fileLabel.textContent = files[0].name;
                    
                    const fileName = files[0].name.toLowerCase();
                    if (!fileName.endsWith('.csv')) {
                        alert('فقط فایل‌های CSV مجاز هستند');
                        fileInput.value = '';
                        fileLabel.textContent = 'انتخاب فایل CSV';
                    }
                }
            }
        }
    </script>
@endsection