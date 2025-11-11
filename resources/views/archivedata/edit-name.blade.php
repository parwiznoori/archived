@extends('layouts.app')

@section('content')
<style>
    .preview-image {
        transition: all 0.3s ease;
        border-radius: 4px;
    }
    .preview-image:hover {
        transform: scale(1.05);
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .btn-view {
        border-radius: 4px 0 0 4px;
        min-width: 80px;
    }
    .btn-download {
        border-radius: 0 4px 4px 0;
        min-width: 80px;
    }
    .image-preview-container {
        max-width: 400px;
    }
</style>
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>

<div class="portlet box">
    <div class="portlet-body">
        <div class="panel-body">
            <h1>اصلاح مشخصات ( {{ $archiveData->name }} )</h1>
            
            <div id="message" class="alert" style="display: none;"></div>
            
            <form id="updateNameForm" action="{{ route('archivedata.update-name', $archiveData->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Current Values Column -->
                   <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                مقادیر فعلی
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>نام فعلی:</label>
                                    <input type="text" class="form-control" value="{{ $archiveData->name }}" readonly>
                                </div>
                                
                                <div class="form-group">
                                    <label>نام پدر فعلی:</label>
                                    <input type="text" class="form-control" value="{{ $archiveData->father_name }}" readonly>
                                </div>
                                
                                <div class="form-group">
                                    <label>نام پدرکلان فعلی:</label>
                                    <input type="text" class="form-control" value="{{ $archiveData->grandfather_name }}" readonly>
                                </div>
                                
                                <div class="form-group">
                                    <label>تاریخ تولد فعلی:</label>
                                    <input type="text" class="form-control" value="{{ $archiveData->birth_date }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    <!-- Previous Values Column -->
                     <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                مقادیر قبلی
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>نام قبلی:</label>
                                    <input type="text" class="form-control" value="{{ $archiveData->previous_name ?? 'N/A' }}" readonly>
                                </div>
                                
                                <div class="form-group">
                                    <label>نام پدر قبلی:</label>
                                    <input type="text" class="form-control" value="{{ $archiveData->previous_father_name ?? 'N/A' }}" readonly>
                                </div>
                                
                                <div class="form-group">
                                    <label>نام پدرکلان قبلی:</label>
                                    <input type="text" class="form-control" value="{{ $archiveData->previous_grandfather_name ?? 'N/A' }}" readonly>
                                </div>
                                
                                <div class="form-group">
                                    <label>تاریخ تولد قبلی:</label>
                                    <input type="text" class="form-control" value="{{ $archiveData->previous_birth_date ?? 'N/A' }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- New Values Form -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        مقادیر جدید
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="new_name">نام جدید:</label>
                                    <input type="text" class="form-control" id="new_name" name="name">
                                </div>
                                
                                <div class="form-group">
                                    <label for="new_father_name">نام پدر جدید:</label>
                                    <input type="text" class="form-control" id="new_father_name" name="father_name">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="new_grandfather_name">نام پدرکلان جدید:</label>
                                    <input type="text" class="form-control" id="new_grandfather_name" name="grandfather_name">
                                </div>
                                
                                <div class="form-group">
                                    <label for="new_birth_date">تاریخ تولد جدید:</label>
                                    <input type="text" class="form-control" id="new_birth_date" name="birth_date">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="updateName_desc">تشریحات:</label>
                            <textarea class="form-control" id="updateName_desc" name="updateName_desc" rows="3"></textarea>
                           
                        </div>
                        <div class="form-group">
                            <label for="updateName_img">تصویر سند تغیر نام:</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="updateName_img" name="updateName_img" accept="image/jpeg,image/png,image/jpg,image/gif">
                                {{-- <label class="custom-file-label" for="updateName_img">فایل را انتخاب کنید</label> --}}
                            </div>
                            
                            {{-- <small class="form-text text-muted">فرمت‌های مجاز: JPG, PNG, GIF - حداکثر حجم: 5MB</small> --}}
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> ذخیره تغییرات
                        </button>
                    </div>
                </div>
            </form>
        </div>
      
<div class="image-preview-container mt-3">
    @if($archiveData->updateName_img)
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <img src="{{ asset($archiveData->updateName_img) }}" 
                             alt="سند تغیر نام"
                             class="img-thumbnail preview-image"
                             style="width: 100%; height: auto; object-fit: cover;">
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex flex-column">
                            <small class="text-muted mb-1">
                                <i class="fas fa-file-image mr-1"></i>
                            </small>
                            <div class="btn-group btn-group-sm mt-2">
                                <a href="{{ asset($archiveData->updateName_img) }}" 
                                   target="_blank"
                                   class="btn btn-outline-primary btn-view"
                                   data-toggle="tooltip"
                                   title="مشاهده تصویر در اندازه کامل">
                                     مشاهده
                                </a>
                                <a href="{{ asset($archiveData->updateName_img) }}" 
                                   download="سند_تغیر_نام_{{ $archiveData->name }}_{{ date('Y-m-d') }}.jpg"
                                   class="btn btn-outline-secondary btn-download"
                                   data-toggle="tooltip"
                                   title="دانلود تصویر">
                                   دانلود
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-light border text-center py-2">
            
            تصویری موجود نیست
        </div>
    @endif
</div>




    </div>
</div>

@if($archiveData->updateName_img)
    <!-- Image preview section -->
@endif

<script>
$(document).ready(function() {
    $('#updateNameForm').submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        $('#message').hide().removeClass('alert-success alert-danger');

        let submitBtn = $('button[type="submit"]');
        submitBtn.prop('disabled', true)
                 .html('<i class="fa fa-spinner fa-spin"></i> در حال ذخیره...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.success, 'success');
                    // Update the form with new values if needed
                    if (response.new_values) {
                        // Update current values display
                        $('input[readonly]').each(function() {
                            const fieldName = $(this).attr('name') || $(this).attr('id');
                            if (fieldName && response.new_values[fieldName]) {
                                $(this).val(response.new_values[fieldName]);
                            }
                        });
                    }
                } else if (response.info) {
                    showMessage(response.info, 'info');
                }
                submitBtn.prop('disabled', false)
                         .html('<i class="fa fa-save"></i> ذخیره تغییرات');

                if ($('#updateName_img')[0].files.length > 0) {
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr) {
                let errorMessage = 'خطا در ذخیره سازی';
                try {
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                        if (xhr.responseJSON.details) {
                            errorMessage += ' - ' + xhr.responseJSON.details;
                        }
                    } else if (xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).flat().join('<br>');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
                showMessage(errorMessage, 'danger');
                submitBtn.prop('disabled', false)
                         .html('<i class="fa fa-save"></i> ذخیره تغییرات');
            }
        });
    });

    function showMessage(text, type) {
        const messageDiv = $('#message');
        messageDiv.html(text)
                 .removeClass('alert-success alert-danger alert-info')
                 .addClass('alert-' + type)
                 .fadeIn()
                 .delay(5000)
                 .fadeOut();
    }
});

</script>
@endsection