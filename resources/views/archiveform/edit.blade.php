@extends('layouts.app')

@section('content')

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="portlet box">
        <div class="portlet-body">
            <!-- BEGIN FORM -->
            {!! Form::open(['route' => ['archive_doc_form.update', $archiveFormTemplate->id], 'method' => 'PUT', 'class' => 'form-horizontal']) !!}

            <div class="form-body" id="app">
                <!-- Form Name -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group {{ $errors->has('form_name') ? 'has-error' : '' }}">
                            {!! Form::label('form_name', trans('general.archive_doc_form_type'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-8">
                                {!! Form::text('form_name', $archiveFormTemplate->form_name, ['class' => 'form-control']) !!}
                                @error('form_name')
                                <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group {{ $errors->has('content') ? 'has-error' : '' }}">
                            {!! Form::label('content', trans('general.content'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::textarea('content', old('content', $archiveFormTemplate->content), [ 'class' => 'form-control summernote ']) !!}
                                @error('content')
                                <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Variables -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group {{ $errors->has('variable') ? 'has-error' : '' }}">
                            {!! Form::label('variable[]', trans('general.archivedata'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-8">
                                {!! Form::select('variable[]', [
                                    'name' => 'نام',
                                    'father_name' => 'نام پدر',
                                    'year_of_inclusion' => 'سال شمولیت',
                                    'university_id' => 'پوهنتون',
                                    'faculty_id' => 'پوهنحی',
                                    'graduated_year' => 'سال فراغت',
                                    'department_id' => 'دیپارتمنت',
                                     'time' => 'تایم درسی',
                                    'monograph_date' => 'تاریخ دفاع مونوگراف',
                                    'grade_id' => 'درجه'
                                ], old('variable', explode(',', $archiveFormTemplate->variable)), [
                                    'class' => 'form-control select2',
                                    'multiple' => 'multiple'
                                ]) !!}
                                @error('variable')
                                <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            {!! Form::label('status', trans('general.status'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::checkbox('status', 1, old('status', $archiveFormTemplate->status), ['class' => 'form-control']) !!}
                                @error('status')
                                <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions fluid">
                    <div class="row">
                        <div class="col-md-offset-1 col-md-12">
                            <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                            <a href="{{ route('archive_doc_form.create') }}" class="btn default">{{ trans('general.cancel') }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <br>
        {!! Form::close() !!}
        <!-- END FORM -->
        </div>
    </div>



@endsection
