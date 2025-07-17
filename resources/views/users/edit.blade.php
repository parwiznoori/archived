@extends('layouts.app')

@section('content')
    <div class="portlet box">        
        <div class="portlet-body">
            <!-- BEGIN FORM-->            
            {!! Form::model($user, ['route' => ['users.update', $user], 'method' => 'patch', 'class' => 'form-horizontal']) !!}            
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-7">
                             <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                {!! Form::label('name', trans('general.name'), ['class' => 'control-label col-sm-2']) !!}                                
                                <div class="col-sm-10">
                                    {!! Form::text('name', null, ['class' => 'form-control']) !!}     
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif                                                                                                   
                                </div>
                            </div>

                        </div>
                        <div class="col-md-5">
                           <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                {!! Form::label('email', trans('general.email'), ['class' => 'control-label col-sm-2']) !!}                                
                                <div class="col-sm-10">
                                    {!! Form::email('email', null, ['class' => 'form-control ltr']) !!}     
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif                                                                                                   
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                             <div class="form-group {{ $errors->has('position') ? ' has-error' : '' }}">
                                {!! Form::label('position', trans('general.position'), ['class' => 'control-label col-sm-2']) !!}                                
                                <div class="col-sm-10">
                                    {!! Form::text('position', null, ['class' => 'form-control']) !!}     
                                    @if ($errors->has('position'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('position') }}</strong>
                                        </span>
                                    @endif                                                                                                   
                                </div>
                            </div>

                        </div>
                         <div class="col-md-5">
                            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                {!! Form::label('password', trans('general.password'), ['class' => 'control-label col-sm-2']) !!}                                
                                <div class="col-sm-10">
                                    {!! Form::password('password', ['class' => 'form-control ltr']) !!}     
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif                                                                                                   
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                             <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                                {!! Form::label('phone', trans('general.phone'), ['class' => 'control-label col-sm-2']) !!}                                
                                <div class="col-sm-10">
                                    {!! Form::text('phone', null, ['class' => 'form-control ltr']) !!}     
                                    @if ($errors->has('phone'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('phone') }}</strong>
                                        </span>
                                    @endif                                                                                                   
                                </div>
                            </div>

                        </div>
                        <div class="col-md-5">
                            <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                {!! Form::label('password_confirmation', trans('general.password_confirmation'), ['class' => 'control-label col-sm-2']) !!}                                
                                <div class="col-sm-10">
                                    {!! Form::password('password_confirmation', ['class' => 'form-control ltr']) !!}     
                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                    @endif                                                                                                   
                                </div>
                            </div>
                        </div>
                    </div>
                    
                   
                   
                      <div class="row">
                        <div class="col-md-7">

                            <div class="form-group {{ $errors->has('user_type') ? ' has-error' : '' }}">
                                {!! Form::label('user_type', trans('general.user_type'), ['class' => 'control-label col-sm-2']) !!}                                
                                <div class="col-sm-10">
                                    {!! Form::select('user_type', $user_types, null, ['class' => 'form-control']) !!}
                                                                                                                                    
                                </div>
                            </div>

                            @if (auth()->user()->allUniversities() || auth()->user()->user_type == 2)
                               
                                <div class="form-group {{ $errors->has('university') ? ' has-error' : '' }}">
                                    {!! Form::label('university', trans('general.university'), ['class' => 'control-label col-sm-2']) !!}                                
                                    <div class="col-sm-10">
                                        {!! Form::select('university_id[]', $universities, $universityIds, ['class' => 'form-control select2' , "multiple" =>"multiple"]) !!}     
                                        @if ($errors->has('university'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('university') }}</strong>
                                            </span>
                                        @endif                                                                                                   
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="university_id" value="{{  auth()->user()->universities->pluck('id')->first() }}">
                            @endif                    
                            {{-- <div class="form-group {{ $errors->has('department') ? ' has-error' : '' }}">
                                {!! Form::label('department', trans('general.department'), ['class' => 'control-label col-sm-2']) !!}                                
                                <div class="col-sm-10">
                                    {!! Form::select('departments[]', $departments, null, ['class' => 'form-control select2-ajax', 'remote-url' => route('api.departments'), 'remote-param' => '[name="university_id[]"]', "multiple" =>"multiple"]) !!}
                                    
                                        <span class="help-block">
                                            <strong>در صورت خالی بودن دیپارتمنت, تمامی دیپارتمنت ها قابل دسترس می باشد.</strong>
                                        </span>
                                                                                                                                    
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('grade') ? ' has-error' : '' }}">
                                {!! Form::label('grade', trans('general.grade'), ['class' => 'control-label col-sm-2']) !!}                                
                                <div class="col-sm-10">
                                    {!! Form::select('grades[]', $grades, null, ['class' => 'form-control select2', "multiple" =>"multiple"]) !!}
                                    
                                        <span class="help-block">
                                            <strong>در صورت خالی بودن مقطع تحصیلی, تمامی مقاطع تحصیلی قابل دسترس می باشد.</strong>
                                        </span>
                                                                                                                                    
                                </div>
                            </div> --}}

                            <div class="form-group {{ $errors->has('active') ? ' has-error' : '' }}">
                                {!! Form::label('active', trans('general.status'), ['class' => 'control-label col-sm-2']) !!}                                
                                <div class="col-sm-10">                 
                                    <div >
                                        <label class="checkbox-inline">
                                        <input type="checkbox" name="active" value="1" checked> {{ trans('general.active') }}                               
                                        </label>                                                       
                                    </div>                                               
                                </div>
                            </div>


                        </div>
                        <div class="col-md-5">

                            <div class="alert alert-info" >
                                <h4 style="text-align: center">{{ trans('general.password_conditions') }}</h4>
                                <hr>
                                <ul>
                                    <li>
                                        <p><h6> {{ trans('general.password_must_at_least_8_character') }} </h6></p>
                                    </li>
                                    <li>
                                        <p><h6> {{ trans('general.password_must_has_one_lowercase_english_letter') }} </h6></p>
                                    </li>
                                    <li>
                                        <p><h6> {{ trans('general.password_must_has_one_uppercase_english_letter') }} </h6></p>
                                    </li>
                                    <li>
                                        <p><h6> {{ trans('general.password_must_has_one_digit') }} </h6></p>
                                    </li>
                                    <li>
                                        <p><h6> {{ trans('general.password_must_has_one_symbol') }} </h6></p>
                                    </li>
                                </ul>
                            </div>
                            
                        </div>
                    </div>
                   
                    <hr>
                    <div class="row">
                        <div class="form-group">
                            {!! Form::label('roles[]', trans('general.roles'), ['class' => 'control-label col-sm-1']) !!}
                            <div class="col-sm-11">
                            
                                @foreach($roles as $role)
                                <div class="checkbox-list col-sm-3">
                                    <label>
                                        {!! Form::checkbox('roles[]', $role->id, $user->roles->contains($role->id)) !!}  {{ $role->title }}
                                    </label>                            
                                </div>
                                @endforeach
                            </div>
                        </div>

                    </div>  

                   
                    <hr>
                </div>
                <div class="form-actions fluid">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                            <a href="{{ route('users.index') }}" class="btn default">{{ trans('general.cancel') }}</a>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!-- END FORM-->
        </div>
    </div>
@endsection('content')

@push('scripts')
<script>
    // $(function () {
    //     $('.select2').change(function () {
    //         $('.select2-ajax').val(null).trigger('change');
    //     })
    // })
</script>
<script>
    $(document).ready(function() {
        $('#university').select2();
        $('#departments').select2();

        $('#university').on('change', function() {
            var universityId = $(this).val();
            if (universityId) {
                $.ajax({
                    url: $("#departments").attr('remote-url') + ($("#departments").attr('remote-param') ? '/' + $($("#departments").attr('remote-param')).val() : ''),
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        //console.log(data);
                        $('#departments').empty();
                        $('#departments').append('<option value="">انتخاب</option>');
                        $.each(data, function(key,value) {
                            // console.log(key,value);
                            $('#departments').append('<option value="' + value.id + '">' + value.text + '</option>');
                        });
                    }
                });
            } else {
                $('#departments').empty();
                $('#departments').append('<option value="">Select Department</option>');
            }
        });
    });
</script>
@endpush