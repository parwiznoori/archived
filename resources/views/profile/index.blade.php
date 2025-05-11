@extends('layouts.app')


@section('content')

    <div class="portlet box">
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-5" style="margin-right:250px;">
                    @if (Session::has('success'))
                        <p class="alert alert-info text-center">{{ Session::get('success') }}</p>

                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                     <!-- BEGIN FORM-->
                    {!! Form::open(['route' => auth('user')->check() ? 'profile.password.store' : (auth('teacher')->check() ? 'teacher.profile.password.store' : 'student.profile.password.store'), 'method' => 'put', 'class' => 'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group {{ $errors->has('old_password') ? ' has-error' : '' }}">
                            {!! Form::label('old_password', trans('general.old_password'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-9">
                                {!! Form::password('old_password', ['class' => 'form-control ltr']) !!}
                                @if ($errors->has('old_password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('old_password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                            {!! Form::label('password', trans('general.new_password'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-9">
                                {!! Form::password('password', ['class' => 'form-control ltr']) !!}
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            {!! Form::label('password_confirmation', trans('general.password_confirmation'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-9">
                                {!! Form::password('password_confirmation', ['class' => 'form-control ltr']) !!}
                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-actions fluid">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                    <!-- END FORM-->
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info" >
                        <h4 style="text-align: center">{{ trans('general.password_conditions') }}</h4>
                        <hr>
                        <p><h5 style="text-align: center"> {{ trans('general.dear_users') }} </h5> </p>
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
           
        </div>
    </div>
@endsection('content')
