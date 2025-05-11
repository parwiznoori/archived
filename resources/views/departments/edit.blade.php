@extends('layouts.app')

@section('content')
<div class="portlet box">
    <div class="portlet-body">
        <!-- BEGIN FORM-->
        {!! Form::model($department, ['route' => ['departments.update', $university, $department], 'method' => 'patch',
        'class' => 'form-horizontal']) !!}
        <div class="form-body">
            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                {!! Form::label('name', trans('models/department.fields.name'), ['class' => 'control-label col-sm-3']) !!}
                <div class="col-sm-4">
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="form-group {{ $errors->has('department_eng') ? ' has-error' : '' }}">
                {!! Form::label('department_eng', trans('models/department.fields.department_eng'), ['class' => 'control-label col-sm-3']) !!}
                <div class="col-sm-4">
                    {!! Form::text('department_eng', null, ['class' => 'form-control']) !!}
                    @if ($errors->has('department_eng'))
                    <span class="help-block">
                        <strong>{{ $errors->first('department_eng') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="form-group {{ $errors->has('abbreviation_eng') ? ' has-error' : '' }}">
                {!! Form::label('abbreviation_eng', trans('models/department.fields.abbreviation_eng'), ['class' => 'control-label col-sm-3']) !!}
                <div class="col-sm-4">
                    {!! Form::text('abbreviation_eng', null, ['class' => 'form-control']) !!}
                    @if ($errors->has('abbreviation_eng'))
                    <span class="help-block">
                        <strong>{{ $errors->first('abbreviation_eng') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="form-group {{ $errors->has('number_of_semesters') ? ' has-error' : '' }}">
                {!! Form::label('number_of_semesters', trans('models/department.fields.number_of_semesters'), ['class' => 'control-label col-sm-3']) !!}
                <div class="col-sm-4">
                   
                    {!! Form::selectRange('number_of_semesters',$MIN_SEMESTER ? $MIN_SEMESTER : 1 ,$MAX_SEMESTER ? $MAX_SEMESTER :12 ,null, ['class' => 'form-control select2']) !!}
                    @if ($errors->has('number_of_semesters'))
                    <span class="help-block">
                        <strong>{{ $errors->first('number_of_semesters') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="form-group {{ $errors->has('min_credits_for_graduated') ? ' has-error' : '' }}">
                {!! Form::label('min_credits_for_graduated', trans('models/department.fields.min_credits_for_graduated'), ['class' => 'control-label col-sm-3']) !!}
                <div class="col-sm-4">
                    {!! Form::selectRange('min_credits_for_graduated',$MIN_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR ? $MIN_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR : 136 ,$MAX_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR ? $MAX_TOTAL_CREDITS_FOR_GRADUATION_BACHELOR :200 ,null, ['class' => 'form-control select2']) !!}
                    @if ($errors->has('min_credits_for_graduated'))
                    <span class="help-block">
                        <strong>{{ $errors->first('min_credits_for_graduated') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            
            {{-- <div class="form-group {{ $errors->has('chairman') ? ' has-error' : '' }}">
                {!! Form::label('chairman', trans('general.faculty_chairman'), ['class' => 'control-label col-sm-3'])
                !!}
                <div class="col-sm-4">
                    {!! Form::text('chairman', null, ['class' => 'form-control']) !!}
                    @if ($errors->has('chairman'))
                    <span class="help-block">
                        <strong>{{ $errors->first('chairman') }}</strong>
                    </span>
                    @endif
                </div>
            </div> --}}
            <div class="form-group {{ $errors->has('department_student_affairs') ? ' has-error' : '' }}">
                {!! Form::label('department_student_affairs', trans('models/department.fields.department_student_affairs'), ['class' =>
                'control-label col-sm-3']) !!}
                <div class="col-sm-4">
                    {!! Form::text('department_student_affairs', null, ['class' => 'form-control']) !!}
                    @if ($errors->has('department_student_affairs'))
                    <span class="help-block">
                        <strong>{{ $errors->first('department_student_affairs') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            {{-- <div class="form-group {{ $errors->has('department_type') ? ' has-error' : '' }}">
                {!! Form::label('department_type', trans('general.department_type'), ['class' => 'control-label
                col-sm-3']) !!}
                <div class="col-sm-4">
                    {!! Form::select('department_type', [trans('general.Daily') => trans('general.Daily'), trans('general.Nightly') =>
                    trans('general.Nightly')],
                    null, ['class' => 'form-control']) !!}
                    @if ($errors->has('department_type'))
                    <span class="help-block">
                        <strong>{{ $errors->first('department_type') }}</strong>
                    </span>
                    @endif
                </div>
            </div> --}}

            <div class="form-group {{ $errors->has('department_type_id') ? ' has-error' : '' }}">
                {!! Form::label('department_type_id', trans('models/department.fields.department_type_id'), ['class' => 'control-label col-sm-3']) !!}
                <div class="col-sm-4">
                    {!! Form::select('department_type_id',$departmentTypes,null, ['class' => 'form-control select2']) !!}
                    @if ($errors->has('department_type_id'))
                    <span class="help-block">
                        <strong>{{ $errors->first('department_type_id') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group {{ $errors->has('faculty_id') ? ' has-error' : '' }}">
                {!! Form::label('faculty_id', trans('models/department.fields.faculty_id'), ['class' => 'control-label col-sm-3']) !!}
                <div class="col-sm-4">
                    {!! Form::select('faculty_id',$faculties,null, ['class' => 'form-control select2']) !!}
                    @if ($errors->has('faculty_id'))
                    <span class="help-block">
                        <strong>{{ $errors->first('faculty_id') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            {{-- <div class="form-group {{ $errors->has('faculty') ? ' has-error' : '' }}">
                {!! Form::label('faculty', trans('general.faculty'), ['class' => 'control-label col-sm-3']) !!}
                <div class="col-sm-4">
                    {!! Form::text('faculty', null, ['class' => 'form-control']) !!}
                    @if ($errors->has('faculty'))
                    <span class="help-block">
                        <strong>{{ $errors->first('faculty') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="form-group {{ $errors->has('faculty_eng') ? ' has-error' : '' }}">
                {!! Form::label('faculty_eng', trans('general.faculty_eng'), ['class' => 'control-label col-sm-3']) !!}
                <div class="col-sm-4">
                    {!! Form::text('faculty_eng', null, ['class' => 'form-control']) !!}
                    @if ($errors->has('faculty_eng'))
                    <span class="help-block">
                        <strong>{{ $errors->first('faculty_eng') }}</strong>
                    </span>
                    @endif
                </div>
            </div> --}}
            
            <div class="form-group {{ $errors->has('grade_id') ? ' has-error' : '' }}">
                {!! Form::label('grade_id', trans('models/department.fields.grade_id'), ['class' => 'control-label col-sm-3']) !!}
                <div class="col-sm-4">
                    {!! Form::select('grade_id', $grades, null, ['class' => 'form-control select2']) !!}
                    @if ($errors->has('grade_id'))
                    <span class="help-block">
                        <strong>{{ $errors->first('grade_id') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="form-actions fluid">
            <div class="row">
                <div class="col-md-offset-3 col-md-9">
                    <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                    <a href="{{ route('departments.index', $university) }}"
                        class="btn default">{{ trans('general.cancel') }}</a>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
        <!-- END FORM-->
    </div>
</div>
@endsection('content')