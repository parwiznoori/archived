@extends('layouts.app')

@section('content')
    <div class="portlet box">        
        <div class="portlet-body">
            <!-- BEGIN FORM-->            
            {!! Form::open(['route' => 'migration.process', 'method' => 'post', 'class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) !!}
                <div class="form-body" id="app">
                <div class="row ">
                    <div class="col-md-12">
                            <div class="form-group {{ $errors->has('file') ? ' has-error' : '' }}">
                                {!! Form::label('file', trans('general.file'), ['class' => 'control-label col-sm-2']) !!}
                                <div class="col-sm-8">
                                    {!! Form::file('file',['class' => 'form-control' ]) !!}
                                    @if ($errors->has('file'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('file') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>      
                <hr>
                <div class="form-actions fluid">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-6">
                            <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                            <a href="{{ route('announcements.index') }}" class="btn default">{{ trans('general.cancel') }}</a>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!-- END FORM-->
        </div>
    </div>
@endsection('content')
