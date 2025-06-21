
@extends('layouts.app')

@section('content')

 <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script> 
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<div class="portlet box">
        <div class="portlet-body">
            <div class="panel-body">

        <h1>اصلاح مشخصات ( {{ $archiveData->name }} )</h1>
        
        <div id="message" class="alert" style="display: none;"></div>
        
        <form id="updateNameForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Current Values Column -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
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
                                <input type="text" class="form-control" id="new_name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="new_father_name">نام پدر جدید:</label>
                                <input type="text" class="form-control" id="new_father_name" name="father_name" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_grandfather_name">نام پدرکلان جدید:</label>
                                <input type="text" class="form-control" id="new_grandfather_name" name="grandfather_name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="new_birth_date">تاریخ تولد جدید:</label>
                                <input type="text" class="form-control" id="new_birth_date" name="birth_date" required>
                            </div>
                        </div>
                    </div>
                    

                      <div class="form-group">
                        <label for="updateName_desc">تشریحات:</label>
                        <textarea class="form-control" id="updateName_desc" name="updateName_desc" rows="3"></textarea>
                        <small class="text-muted">توضیحات اختیاری درباره تغییر نام</small>
                    </div>

                    <div class="form-group">
                        <label for="updateName_img">تصویر سند تغیر نام:</label>
                        <input type="file" class="form-control-file" id="updateName_img" name="updateName_img">
                        <small class="text-muted">(اختیاری) فقط تصاویر با فرمت JPG, PNG تا 5MB</small>
                    </div>

        
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> ذخیره تغییرات
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


@if($archiveData->updateName_img)
    <div class="document-preview-container mb-4">
        <div class="document-image-wrapper">
            <!-- Main Image Display -->
            <img src="{{ asset($archiveData->updateName_img) }}" 
                 alt="سند تغیر نام" 
                 class="document-image img-responsive border "
                 id="documentImage">
            
            <!-- Image Actions -->
            <div class="document-actions mt-2">
                <a href="{{ asset($archiveData->updateName_img) }}" 
                   class="btn btn-sm btn-outline-primary"
                   target="_blank"
                   download="سند_تغیر_نام_{{ $archiveData->name }}_{{ date('Y-m-d') }}.jpg">
                    دانلود سند
                </a>
                
            </div>
        </div>
    </div>

@endif




<script>
$(document).ready(function() {
    $('#updateNameForm').submit(function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        // Add debugging
        console.log("Form data being sent:", {
            name: $('#new_name').val(),
            father_name: $('#new_father_name').val(),
            grandfather_name: $('#new_grandfather_name').val(),
            birth_date: $('#new_birth_date').val(),
            hasFile: $('#updateName_img')[0].files.length > 0
        });

        $.ajax({
            url: "{{ route('archivedata.update-name', $archiveData->id) }}",
            type: 'POST', // Laravel needs POST for FormData with PUT method
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT' // Tell Laravel this is a PUT request
            },
            success: function(response) {
                console.log("Success response:", response);
                // Update all fields
                $('.card-body input[readonly]').eq(0).val(response.new_values.name);
                $('.card-body input[readonly]').eq(1).val(response.new_values.father_name);
                $('.card-body input[readonly]').eq(2).val(response.new_values.grandfather_name);
                $('.card-body input[readonly]').eq(3).val(response.new_values.birth_date);
                
                $('.card-body input[readonly]').eq(4).val(response.new_values.previous_name);
                $('.card-body input[readonly]').eq(5).val(response.new_values.previous_father_name);
                $('.card-body input[readonly]').eq(6).val(response.new_values.previous_grandfather_name);
                $('.card-body input[readonly]').eq(7).val(response.new_values.previous_birth_date);
                
                showMessage(response.success, 'success');
                $('#updateNameForm')[0].reset();
            },
            error: function(xhr, status, error) {
                console.log("Error:", xhr.responseText);
                let errorMessage = xhr.responseJSON.error || 'خطا در ارتباط با سرور';
                showMessage(errorMessage, 'danger');
            }
        });
    });
    
    function showMessage(text, type) {
        const messageDiv = $('#message');
        messageDiv.removeClass('alert-success alert-danger')
                 .addClass('alert-' + type)
                 .text(text)
                 .fadeIn();
        
        setTimeout(() => messageDiv.fadeOut(), 5000);
    }
});
</script>
@endsection