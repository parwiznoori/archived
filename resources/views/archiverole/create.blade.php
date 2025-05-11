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
                                {!! Form::select('role_id', $archiveRoles->pluck('title', 'id'), null, ['class' => 'form-control', 'placeholder' => 'انتخاب وظایف']) !!}
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
                        <div class="form-group {{ $errors->has('user_id') ? ' has-error' : '' }}">
                            {!! Form::label('user_id', trans('general.users'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::select('user_id', $archiveUsers, null, ['class' => 'form-control select2-ajax-archive-user',
                                'remote-url' => route('api.archiveUserRoleLoad'), 'remote-param' => '[name="role_id"]','placeholder' => 'انتخاب یوزر']) !!}
                                @if ($errors->has('user_id'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('user_id') }}</strong></span>
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
                                {!! Form::select('university_id', $universities, null, ['class' => 'form-control select2',
                                'remote-param' => '[name="role_id"]',
                                'placeholder' => 'انتخاب پوهنتون']) !!}
                                @if ($errors->has('university_id'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('university_id') }}</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('archive_id') ? ' has-error' : '' }}">
                            {!! Form::label('archive_id', trans('general.book_name'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::select('archive_id', $archives, null, [
                                    'class' => 'form-control select2-two-paramter-ajax',
                                    'remote-url' => route('api.archiveBookRoleLoad'),
                                    'remote-param1' => '[name="university_id"]',
                                    'remote-param2' => '[name="role_id"]',
                                    'placeholder' => 'انتخاب کتاب'
                                ]) !!}
                                @if ($errors->has('archive_id'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('archive_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


{{-- 
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('archive_id') ? ' has-error' : '' }}">
                            {!! Form::label('archive_id', trans('general.book_name'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::select('archive_id', $archives->pluck('book_name', 'id'), null, ['class' => 'form-control select2-ajax-archive-book',
                                'remote-url' => route('api.archiveBookRoleLoad'), 'remote-param' => '[name="role_id"]','placeholder' => 'انتخاب کتاب']) !!}
                                @if ($errors->has('archive_id'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('archive_id') }}</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div> --}}





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
