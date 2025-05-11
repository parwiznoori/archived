@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <style>

        .modal .modal-dialog {
            margin-top: 100px!important;
        }
    </style>
    <div class="portlet box">
        <div class="portlet-body">

            <!-- BEGIN FORM -->
            {!! Form::open(['route' => 'archive_doc_form.store', 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}
            <div class="form-body" id="app">

                <!-- Form Name -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group {{ $errors->has('form_name') ? 'has-error' : '' }}">
                            {!! Form::label('form_name', trans('general.archive_doc_form_type'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-8">
                                {!! Form::text('form_name', null, ['class' => 'form-control ']) !!}
                                @error('form_name')
                                <span class="help-block"><strong>{{ $message }}</strong></span>
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
                            <div class="col-sm-8" style="z-index: 100000!important;">
                                {!! Form::textarea('content', null, [ 'class' => 'form-control summernote']) !!}
                                @error('content')
                                <span class="help-block"><strong>{{ $message }}</strong></span>
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
                                ], old('variable', []), [
                                    'class' => 'form-control select2',
                                    'multiple' => 'multiple'
                                ]) !!}
                                @error('variable')
                                <span class="help-block"><strong>{{ $message }}</strong></span>
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
                                {!! Form::checkbox('status', 1, null, ['class' => 'form-control']) !!}
                                @error('status')
                                <span class="help-block"><strong>{{ $message }}</strong></span>
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
                            <a href="{{ route('archivedata.index') }}" class="btn default">{{ trans('general.cancel') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
        <!-- END FORM -->

            <hr>

            <!-- Data Table -->
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>نوع مکتوب</th>
                    <th>وضعیت</th>
                    <th>تصحیح</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($archiveFormTemplate as $template)
                    <tr>
                        <td>{{ $template->id }}</td>
                        <td>{{ $template->form_name }}</td>
                        <td style="color: {{ $template->status == 1 ? 'blue' : 'red' }}">
                            {{ $template->status == 1 ? 'فعال' : 'غیر فعال' }}
                        </td>
                        <td>
                            <a href="{{ route('archive_doc_form.edit', $template->id) }}" class="btn btn-primary" target="_blank">تصحیح</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>




@endsection
