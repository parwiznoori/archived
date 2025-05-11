@extends('layouts.app')

@section('content')
    <div class="portlet box">        
        <div class="portlet-body">
            <!-- BEGIN FORM-->            
            {!! Form::model($permissions, ['route' => ['permissions.update', $permissions->id], 'method' => 'patch', 'class' => 'form-horizontal']) !!}            
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                {!! Form::label('name', trans('models/permissions.fields.name'), ['class' => 'control-label col-sm-3']) !!}
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
                            <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                                {!! Form::label('title', trans('models/permissions.fields.title'), ['class' => 'control-label col-sm-3']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('title', null, ['class' => 'form-control']) !!}
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('title') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
    
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('guard_name') ? ' has-error' : '' }}">
                                {!! Form::label('guard_name', trans('models/permissions.fields.guard_name'), ['class' => 'control-label col-sm-3']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('guard_name', null, ['class' => 'form-control']) !!}
                                    @if ($errors->has('guard_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('guard_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('is_restricted') ? ' has-error' : '' }}">
                                {!! Form::label('is_restricted', trans('general.is_restricted'), ['class' => 'control-label col-sm-3']) !!}
                                <div class="col-sm-8">
                                    {!! Form::selectRange('is_restricted', 0 , 2 ,null, ['class' => 'form-control select2']) !!}
                                   
                                    @if ($errors->has('is_restricted'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('is_restricted') }}</strong>
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
                            <a href="{{ route('permissions.index') }}" class="btn default">{{ trans('general.cancel') }}</a>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!-- END FORM-->
        </div>
    </div>
@endsection('content')