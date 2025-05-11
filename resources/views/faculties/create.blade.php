@extends('layouts.app')

@section('content')
<div class="portlet box">
    <div class="portlet-body">
        <!-- BEGIN FORM-->
        {!! Form::open(['route' => ['faculties.store', $university], 'method' => 'post', 'class' =>
        'form-horizontal']) !!}
        <div class="form-body">
            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                {!! Form::label('name', trans('general.name'), ['class' => 'control-label col-sm-3']) !!}
                <div class="col-sm-4">
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group {{ $errors->has('name_en') ? ' has-error' : '' }}">
                {!! Form::label('name_en', trans('general.name_eng'), ['class' => 'control-label col-sm-3']) !!}
                <div class="col-sm-4">
                    {!! Form::text('name_en', null, ['class' => 'form-control']) !!}
                    @if ($errors->has('name_en'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name_en') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group {{ $errors->has('chairman') ? ' has-error' : '' }}">
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
            </div>

        </div>
        <div class="form-actions fluid">
            <div class="row">
                <div class="col-md-offset-3 col-md-9">
                    <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                    <a href="{{ route('faculties.index', $university) }}"
                        class="btn default">{{ trans('general.cancel') }}</a>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
        <!-- END FORM-->
    </div>
</div>
@endsection('content')