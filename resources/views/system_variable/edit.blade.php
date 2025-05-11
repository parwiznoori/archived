@extends('layouts.app')

@section('content')
    <div class="portlet box">        
        <div class="portlet-body">
            <!-- BEGIN FORM-->            
            {!! Form::model($systemVariable, ['route' => ['systemVariable.update', $systemVariable->id], 'method' => 'patch', 'class' => 'form-horizontal']) !!}            
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                {!! Form::label('name', trans('models/systemVariable.fields.name'), ['class' => 'control-label col-sm-3']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('default_value') ? ' has-error' : '' }}">
                                {!! Form::label('default_value', trans('models/systemVariable.fields.default_value'), ['class' => 'control-label col-sm-3']) !!}
                                <div class="col-sm-8">
                                    {!! Form::number('default_value', null, ['class' => 'form-control']) !!}
                                    @if ($errors->has('default_value'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('default_value') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
    
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('user_value') ? ' has-error' : '' }}">
                                {!! Form::label('user_value', trans('models/systemVariable.fields.user_value'), ['class' => 'control-label col-sm-3']) !!}
                                <div class="col-sm-8">
                                    {!! Form::number('user_value', null, ['class' => 'form-control']) !!}
                                    @if ($errors->has('user_value'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('user_value') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
                                {!! Form::label('description', trans('models/systemVariable.fields.description'), ['class' => 'control-label col-sm-3']) !!}
                                <div class="col-sm-8">
                                    {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('description') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
    
                    </div>
                    
                </div>
                <div class="form-actions fluid">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                            <a href="{{ route('systemVariable.index') }}" class="btn default">{{ trans('general.cancel') }}</a>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!-- END FORM-->
        </div>
    </div>
@endsection('content')