@extends('layouts.app')

@section('content')
    <div class="portlet box">
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::model($archive, [
                'route' => ['archive.update', $archive],
                'method' => 'patch',
                'enctype' => 'multipart/form-data',
                'class' => 'form-horizontal'
            ]) !!}
            <div class="form-body" id="app">

                <!-- University Selection -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('university_id') ? ' has-error' : '' }}">
                            {!! Form::label('university_id', trans('general.university'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::select('university_id', $universities, null, [
                                    'class' => 'form-control select2',
                                    'placeholder' => trans('general.select')
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

                <!-- Department Selection -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('department_id') ? ' has-error' : '' }}">
                            {!! Form::label('department_id', trans('general.department'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::select('department_id[]', $departments, $archive->departments, [
                                    'class' => 'form-control select2-ajax',
                                    'multiple' => 'multiple',
                                    'remote-url' => route('api.departmentArchive'),
                                    'remote-param' => '[name="university_id"]'
                                ]) !!}
                                @if ($errors->has('department_id'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('department_id') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Book Name -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('book_name') ? ' has-error' : '' }}">
                            {!! Form::label('book_name', trans('general.book_name'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('book_name', null, ['class' => 'form-control']) !!}
                                @if ($errors->has('book_name'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('book_name') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('archive_year_id') ? ' has-error' : '' }}">
                            {!! Form::label('archive_year_id', trans('general.book_year'), ['class' => 'control-label
                            col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::select('archive_year_id', $archiveyears, null, ['class' => 'form-control select2',
                                'placeholder' => trans('general.select')]) !!}
                                @if ($errors->has('archive_year_id'))
                                    <span class="help-block">
                        <strong>{{ $errors->first('archive_year_id') }}</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Book PDF -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('path') ? ' has-error' : '' }}">
                            {!! Form::label('path', trans('general.pdf'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::file('path', null, ['class' => 'form-control']) !!}
                                @if ($errors->has('path'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('path') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Book Description -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('book_description') ? ' has-error' : '' }}">
                            {!! Form::label('book_description', trans('general.book_description'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::textarea('book_description', null, ['class' => 'form-control']) !!}
                                @if ($errors->has('book_description'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('book_description') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Form Actions -->
            <br>
            <div class="form-actions fluid">
                <div class="row">
                    <div class="col-md-offset-2 col-md-8">
                        <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                        <a href="{{ route('archive.index') }}" class="btn default">{{ trans('general.cancel') }}</a>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
@endsection('content')
