@extends('layouts.app')

@section('content')
@if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
<style>
    .alert {
        transition: all 0.3s ease;
        direction: rtl;
        text-align: right;
    }
    .alert i {
        font-size: 1.2em;
        vertical-align: middle;
        margin-left: 8px;
    }

    .results-table {
        width: 100%;
        border-collapse: collapse;
        direction: rtl;
        font-family: 'Tahoma', 'Arial', sans-serif;
        margin-top: 30px;
    }

    .results-table th, 
    .results-table td {
        border: 1px solid #dee2e6;
        padding: 12px;
        text-align: center;
    }

    .results-table th {
        background-color: #f8f9fa;
        font-weight: bold;
        color: #495057;
    }

    .results-table tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .results-table tr:hover {
        background-color: #e9ecef;
    }

    .table-title {
        text-align: center;
        margin: 20px 0;
        font-size: 18px;
        font-weight: bold;
        color: #343a40;
    }

    .no-data {
        text-align: center;
        padding: 20px;
        color: #6c757d;
    }
    
    .card-header {
        direction: rtl;
        text-align: right;
    }
    
    .form-label {
        text-align: right;
        display: block;
        direction: rtl;
    }
    
    .section-title {
        direction: rtl;
        text-align: right;
    }
    
    .form-control, .form-select {
        text-align: right;
        direction: rtl;
    }
    
    .input-group-text {
        direction: ltr;
    }
</style>

<div class="portlet box">
    <div class="portlet-body">
        <div class="panel-body">
            {{-- <div class="row justify-content-center"> --}}
                {{-- <div class="col-md-12"> --}}
                    {{-- <div class="card"> --}}
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                 ثبت اطلاعات باقیداری
                            </h4>
                        </div>

                        <div class="card-body">
                            <!-- Enhanced Alert Messages -->
                     

                            <form method="POST" action="{{ route('archive_baqidari_update', $archive->id) }}" enctype="multipart/form-data" class="needs-validation" novalidate>                                @csrf
                            @csrf
                                <!-- Basic Information Section -->
                                 <div class="row g-3">  
                                  
                                     
                                        <div class="col-md-4">
                                            <label for="subject" class="form-label">مضمون / مونوگراف </label>
                                            <select class="form-control select2" id="subject" name="subject" required>
                                                <option value="">-- انتخاب نوع سند --</option>
                                                <option value="مونوگراف" {{ old('subject', $data->subject ?? '') == 'مونوگراف' ? 'selected' : '' }}>مونوگراف</option>
                                                <option value="مضمون" {{ old('subject', $data->subject ?? '') == 'مضمون' ? 'selected' : '' }}>مضمون</option>
                                            </select>
                                            </div>
                                    
                                   
                                        <div class="col-md-4">
                                            <label for="semester" class="form-label ">سمستر</label>
                                            <select class="form-select select2 form-control " id="semester" name="semester" required>
                                                <option value="">سمستر را انتخاب کنید</option>
                                                @for ($i = 1; $i <= 8; $i++)
                                                    <option value="semester{{ $i }}" 
                                                        {{ old('semester', $data->semester ?? '') == "semester$i" ? 'selected' : '' }}>
                                                        سمستر {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                        
                                        </div>
                                            <div class="col-md-4">
                                            <label for="chance_number" class="form-label">نوع چانس</label>
                                            <select class="form-control select2" id="chance" name="chance" required>
                                                <option value="">-- انتخاب نوع چانس --</option>
                                                <option value="چانس اول" {{ old('chance', $data->chance ?? '') == 'چانس اول' ? 'selected' : '' }}>چانس اول</option>
                                                <option value="چانس دوم" {{ old('chance', $data->chance ?? '') == 'چانس دوم' ? 'selected' : '' }}>چانس دوم</option>
                                                <option value="چانس سوم" {{ old('chance', $data->chance ?? '') == 'چانس سوم' ? 'selected' : '' }}>چانس سوم</option>
                                                <option value="چانس چهارم" {{ old('chance', $data->chance ?? '') == 'چانس چهارم' ? 'selected' : '' }}>چانس چهارم</option>
                                            </select>
                                        </div>
                                      
                                        <div class="col-md-4">
                                            <label for="credit" class="form-label">تعداد کریدت</label>
                                            <input type="number" step="0.01" class="form-control" id="credit" name="credit" 
                                                value="{{ old('credit', $data->credit ?? '') }}">
                                        </div>
                                            
                                        <div class="col-md-4">
                                            <label for="chance_number" class="form-label">نمبر چانس</label>
                                            <input type="number" step="0.01" class="form-control" id="chance_number" name="chance_number" 
                                                value="{{ old('chance_number', $data->chance_number ?? '') }}" required>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="zarib_chance" class="form-label">ضریب کریدت چانس دوم</label>
                                            <input type="number" step="0.01" class="form-control" id="zarib_chance" name="zarib_chance" 
                                                value="{{ old('zarib_chance', $data->zarib_chance ?? '') }}">
                                        </div>

                                       
                                    </div>
                                </div>

                                <!-- Credit Information Section -->
                                <div class="mb-4 p-3 ">
                                   
                                    <h5 class="mb-3 text-primary section-title"></h5>
                                    <div class="row g-3">

                                      


                                           <div class="col-md-4">
                                            <label for="title" class="form-label">نام مونوگراف / مضمون</label>
                                            <input type="text" class="form-control" id="title" name="title" 
                                                value="{{ old('title', $data->title ?? '') }}" 
                                                placeholder="عنوان را وارد کنید">
                                           </div>


                                        <div class="col-md-4">
                                            <label for="monoghraph" class="form-label">نمبر مونوگراف / مضمون</label>
                                            <input type="number" step="0.01" class="form-control" id="monoghraph" name="monoghraph" 
                                                value="{{ old('monoghraph', $data->monoghraph ?? '') }}">
                                        </div>

                                        

                                        <div class="col-md-4">
                                            <label for="zarib_credite" class="form-label">ضریب کریدت</label>
                                            <input type="number" step="0.01" class="form-control" id="zarib_credite" name="zarib_credite" 
                                                value="{{ old('zarib_credite', $data->zarib_credite ?? '') }}">
                                        </div>

                                       
                                    </div>
                                </div>

                                <!-- Semester 1 Data -->
                                <div class="mb-4 p-3 ">
                                    <h5 class="mb-3 text-primary section-title">
                                        <i class="fas fa-calendar-alt"></i> نتیجه سمستر  (قبل از دفاع)
                                    </h5>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="total_credit" class="form-label">مجموع کریدت</label>
                                            <input type="number" step="0.01" class="form-control" id="total_credit" name="total_credit" 
                                                value="{{ old('total_credit', $data->total_credit ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="total_numbers" class="form-label">مجموع نمرات</label>
                                            <input type="number" step="0.01" class="form-control" id="total_numbers" name="total_numbers" 
                                                value="{{ old('total_numbers', $data->total_numbers ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="semester_percentage" class="form-label">فیصدی سمستر</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="semester_percentage" 
                                                    name="semester_percentage" min="0" max="100"
                                                    value="{{ old('semester_percentage', $data->semester_percentage ?? '') }}">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Semester 2 Data -->
                                <div class="mb-4 p-3">
                                    <h5 class="mb-3 text-primary section-title">
                                        <i class="fas fa-calendar-alt"></i> نتیجه سمستر  (بعد از دفاع)
                                    </h5>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="total_credit2" class="form-label">مجموع کریدت</label>
                                            <input type="number" step="0.01" class="form-control" id="total_credit2" name="total_credit2" 
                                                value="{{ old('total_credit2', $data->total_credit2 ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="total_numbers2" class="form-label">مجموع نمرات</label>
                                            <input type="number" step="0.01" class="form-control" id="total_numbers2" name="total_numbers2" 
                                                value="{{ old('total_numbers2', $data->total_numbers2 ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="semester_percentage2" class="form-label">فیصدی سمستر</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="semester_percentage2" 
                                                    name="semester_percentage2" min="0" max="100"
                                                    value="{{ old('semester_percentage2', $data->semester_percentage2 ?? '') }}">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Semester 3 Data -->
                                <div class="mb-4 p-3 ">
                                    <h5 class="mb-3 text-primary section-title">
                                        <i class="fas fa-calendar-alt"></i> نتیجه هشت سمستر (قبل از دفاع)
                                    </h5>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="total_credit3" class="form-label">مجموع کریدت</label>
                                            <input type="number" step="0.01" class="form-control" id="total_credit3" name="total_credit3" 
                                                value="{{ old('total_credit3', $data->total_credit3 ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="total_numbers3" class="form-label">مجموع نمرات</label>
                                            <input type="number" step="0.01" class="form-control" id="total_numbers3" name="total_numbers3" 
                                                value="{{ old('total_numbers3', $data->total_numbers3 ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="eight_semester_percentage3" class="form-label">فیصدی هشت سمستر</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="eight_semester_percentage3" 
                                                    name="eight_semester_percentage3" min="0" max="100"
                                                    value="{{ old('eight_semester_percentage3', $data->eight_semester_percentage3 ?? '') }}">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Semester 4 Data -->
                                <div class="mb-4 p-3 ">
                                    <h5 class="mb-3 text-primary section-title">
                                        <i class="fas fa-calendar-alt"></i> نتیجه هشت سمستر (بعد از دفاع)
                                    </h5>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="total_credit4" class="form-label">مجموع کریدت</label>
                                            <input type="number" step="0.01" class="form-control" id="total_credit4" name="total_credit4" 
                                                value="{{ old('total_credit4', $data->total_credit4 ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="total_numbers4" class="form-label">مجموع نمرات</label>
                                            <input type="number" step="0.01" class="form-control" id="total_numbers4" name="total_numbers4" 
                                                value="{{ old('total_numbers4', $data->total_numbers4 ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="eight_semester_percentage4" class="form-label">فیصدی هشت سمستر</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="eight_semester_percentage4" 
                                                    name="eight_semester_percentage4" min="0" max="100"
                                                    value="{{ old('eight_semester_percentage4', $data->eight_semester_percentage4 ?? '') }}">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                 <!-- Image Upload -->
                                        <div class="form-group row {{ $errors->has('baqidari_img') ? 'has-error' : '' }}">
                                            {!! Form::label('baqidari_img', trans('general.baqidari_img'), ['class' => 'col-md-4 col-form-label font-weight-bold']) !!}
                                            <div class="col-md-8">
                                                <div class="custom-file">
                                                    {!! Form::file('baqidari_img', [
                                                        'class' => 'custom-file-input',
                                                        'id' => 'baqidariImageUpload'
                                                    ]) !!}
                                                    <label class="custom-file-label" for="baqidariImageUpload">
                                                        
                                                    </label>
                                                </div>
                                            
                                                @if ($errors->has('baqidari_img'))
                                                    <span class="text-danger small">{{ $errors->first('baqidari_img') }}</span>
                                                @endif
                                            
                                            </div>
                                        </div>

                                <!-- Form Submission -->
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    
                                    <button type="submit" class="btn btn-primary">
                                         ذخیره اطلاعات
                                    </button>
                                      <a href="{{ route('archivedata.index') }}" class="btn btn-outline-secondary px-4 ml-2">
                                    {{ trans('general.cancel') }}
                                </a>
                                </div>
                            </form>
                            
                            
                   
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap validation script -->
<script>
    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 150);
            }, 5000);
        });

        // Form validation
        const forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms)
            .forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
    });
</script>
@endsection