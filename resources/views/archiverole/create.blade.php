@extends('layouts.app')

@section('content')
    <div class="portlet box">
        <div class="portlet-body">
            {!! Form::open(['route' => 'archiverole.store', 'method' => 'post', 'class' => 'form-horizontal']) !!}
            <div class="form-body col-sm-12" id="app">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('role_id') ? ' has-error' : '' }}">
                            {!! Form::label('role_id', trans('general.role'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::select('role_id', $archiveRoles->pluck('title', 'id'), null, [
                                    'class' => 'form-control', 
                                    'placeholder' => 'انتخاب وظایف',
                                    'id' => 'role_id'
                                ]) !!}
                                @if ($errors->has('role_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('role_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

               

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('university_id') ? ' has-error' : '' }}">
                            {!! Form::label('university_id', trans('general.university'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::select('university_id', $universities, null, [
                                    'class' => 'form-control select2',
                                    'placeholder' => 'انتخاب پوهنتون',
                                    'id' => 'university_id'
                                ]) !!}
                                @if ($errors->has('university_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('university_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('archive_ids') ? ' has-error' : '' }}">
                            {!! Form::label('archive_ids', trans('general.book_name'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                <select name="archive_ids[]" id="archive_ids" 
                                        class="form-control select2-two-paramter-ajax-multiple" 
                                        multiple="multiple"
                                        remote-url="{{ route('api.archiveBookRoleLoadMultiple') }}"
                                        remote-param1="[name='university_id']"
                                        remote-param2="[name='role_id']"
                                        placeholder="انتخاب کتاب‌ها">
                                </select>
                                @if ($errors->has('archive_ids'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('archive_ids') }}</strong>
                                    </span>
                                @endif
                                @if ($errors->has('archive_ids.*'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('archive_ids.*') }}</strong>
                                    </span>
                                @endif
                                <small class="text-muted">می‌توانید چندین کتاب را انتخاب کنید</small>
                            </div>
                        </div>
                    </div>
                </div>

                 <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('user_id') ? ' has-error' : '' }}">
                            {!! Form::label('user_id', trans('general.users'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::select('user_id', $archiveUsers, null, [
                                    'class' => 'form-control select2-ajax-archive-user',
                                    'remote-url' => route('api.archiveUserRoleLoad'), 
                                    'remote-param' => '[name="role_id"]',
                                    'placeholder' => 'انتخاب یوزر',
                                    'id' => 'user_id'
                                ]) !!}
                                @if ($errors->has('user_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('user_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <br>
            <div class="form-actions fluid">
                <div class="row">
                    <div class="col-md-offset-2 col-md-8">
                        <button type="submit" class="btn btn-success">{{ trans('general.save') }}</button>
                        <a href="{{ route('archiverole.index') }}" class="btn btn-default">{{ trans('general.cancel') }}</a>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    
    // ============= راه‌اندازی Select2 برای کتاب‌ها =============
    if ($("#archive_ids").length > 0) {
        
        $("#archive_ids").select2({
            language: "fa",
            multiple: true,
            allowClear: true,
            placeholder: "انتخاب کتاب‌ها",
            minimumInputLength: 0,
            width: '100%',
            dropdownAutoWidth: true,
            
            ajax: {
                url: function () {
                    let base = $("#archive_ids").attr('remote-url');
                    let uParam = $('#university_id').val();
                    let rParam = $('#role_id').val();
                    
                    console.log('University:', uParam, 'Role:', rParam);
                    
                    if (!uParam || !rParam) {
                        return null;
                    }
                    
                    return base + '/' + uParam + '/' + rParam;
                },
                
                dataType: 'json',
                delay: 500,
                
                data: function (params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1
                    };
                },
                
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    
                    let results = [];
                    let pagination = { more: false };
                    
                    if (data && data.results) {
                        results = data.results;
                        pagination.more = data.pagination && data.pagination.more ? true : false;
                    } else if (Array.isArray(data)) {
                        results = data;
                    }
                    
                    return {
                        results: results,
                        pagination: pagination
                    };
                },
                
                cache: true,
                
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Response:', xhr.responseText);
                }
            },
            
            templateResult: function (data) {
                if (data.loading) {
                    return $('<span class="loading">در حال جستجو...</span>');
                }
                return $('<span>').text(data.text || data.book_name || 'نامشخص');
            },
            
            templateSelection: function (data) {
                return data.text || data.book_name || data.id || 'انتخاب کنید';
            }
        });
        
        // فعال/غیرفعال کردن اولیه
        if (!$('#university_id').val() || !$('#role_id').val()) {
            $('#archive_ids').prop('disabled', true);
        }
    }
    
    // ============= راه‌اندازی Select2 برای کاربران =============
    if ($(".select2-ajax-archive-user").length > 0) {
        $(".select2-ajax-archive-user").select2({
            language: "fa",
            allowClear: true,
            placeholder: "انتخاب یوزر",
            width: '100%',
            ajax: {
                url: function () {
                    let base = $(".select2-ajax-archive-user").attr('remote-url');
                    let roleId = $('#role_id').val();
                    
                    if (!roleId) return null;
                    
                    return base + '/' + roleId;
                },
                dataType: 'json',
                delay: 500,
                data: function(params) {
                    return {
                        q: params.term || ''
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                error: function(xhr, status, error) {
                    console.error('User AJAX Error:', error);
                }
            }
        });
    }
    
    // ============= راه‌اندازی Select2 ساده برای دانشگاه =============
    if ($("#university_id").length > 0) {
        $("#university_id").select2({
            language: "fa",
            allowClear: true,
            placeholder: "انتخاب پوهنتون",
            width: '100%'
        });
    }
    
    // ============= Event Handlers =============
    // وقتی role_id یا university_id تغییر کرد
    $('#role_id, #university_id').on('change', function() {
        console.log('Role or University changed');
        
        // پاک کردن انتخاب کتاب‌ها
        $('#archive_ids').val(null).trigger('change');
        
        // فعال/غیرفعال کردن select کتاب
        let roleVal = $('#role_id').val();
        let uniVal = $('#university_id').val();
        
        if (roleVal && uniVal) {
            $('#archive_ids').prop('disabled', false);
            
            // رفرش کردن select2 برای بارگذاری مجدد
            $('#archive_ids').select2({
                ajax: {
                    url: function () {
                        let base = $("#archive_ids").attr('remote-url');
                        return base + '/' + uniVal + '/' + roleVal;
                    }
                }
            });
        } else {
            $('#archive_ids').prop('disabled', true);
        }
        
        // رفرش کردن select کاربر
        if ($('.select2-ajax-archive-user').length > 0) {
            $('.select2-ajax-archive-user').val(null).trigger('change');
        }
    });
    
});
</script>
@endpush

@push('styles')
<style>
    /* استایل برای Select2 */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d2d6de;
        border-radius: 0;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #3c8dbc;
    }
    .loading {
        color: #999;
    }
</style>
@endpush