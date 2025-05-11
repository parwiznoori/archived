@extends('layouts.app')

@section('content')
    <div class="portlet box">        
        <div class="portlet-body">
            <!-- BEGIN FORM-->            
            {!! Form::model($university, ['route' => ['universities.update', $university],'files' => true, 'method' => 'patch', 'class' => 'form-horizontal']) !!}            
                <div class="form-body">

                    <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                        {!! Form::label('name', trans('models/universities.fields.name'), ['class' => 'control-label col-sm-3']) !!}                                
                        <div class="col-sm-4">
                            {!! Form::text('name', null, ['class' => 'form-control']) !!}     
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif                                                                                                   
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('name_eng') ? ' has-error' : '' }}">
                        {!! Form::label('name_eng', trans('models/universities.fields.name_eng'), ['class' => 'control-label col-sm-3']) !!}                                
                        <div class="col-sm-4">
                            {!! Form::text('name_eng', null, ['class' => 'form-control']) !!}     
                            @if ($errors->has('name_eng'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name_eng') }}</strong>
                                </span>
                            @endif                                                                                                   
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('chairman') ? ' has-error' : '' }}">
                        {!! Form::label('chairman', trans('models/universities.fields.chairman'), ['class' => 'control-label col-sm-3']) !!}                                
                        <div class="col-sm-4">
                            {!! Form::text('chairman', null, ['class' => 'form-control']) !!}     
                            @if ($errors->has('chairman'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('chairman') }}</strong>
                                </span>
                            @endif                                                                                                   
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('student_affairs') ? ' has-error' : '' }}">
                        {!! Form::label('student_affairs', trans('models/universities.fields.student_affairs'), ['class' => 'control-label col-sm-3']) !!}                                
                        <div class="col-sm-4">
                            {!! Form::text('student_affairs', null, ['class' => 'form-control']) !!}     
                            @if ($errors->has('student_affairs'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('student_affairs') }}</strong>
                                </span>
                            @endif                                                                                                   
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('domain') ? ' has-error' : '' }}">
                        {!! Form::label('domain', trans('models/universities.fields.domain'), ['class' => 'control-label col-sm-3', 'disabled']) !!}                                
                        <div class="col-sm-4">
                            {!! Form::text('domain', null, ['class' => 'form-control ltr']) !!}     
                            @if ($errors->has('domain'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('domain') }}</strong>
                                </span>
                            @endif                                                                                                   
                        </div>
                    </div>  
                    <div class="form-group {{ $errors->has('province_id') ? ' has-error' : '' }}">
                        {!! Form::label('province_id', trans('models/universities.fields.province_id'), ['class' => 'control-label col-sm-3']) !!}                                
                        <div class="col-sm-4">
                            {!! Form::select('province_id',$provinces, null, ['class' => 'form-control ltr  select2']) !!}     
                            @if ($errors->has('province_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('province_id') }}</strong>
                                </span>
                            @endif                                                                                                   
                        </div>
                    </div>    
                    <div class="form-group {{ $errors->has('address') ? ' has-error' : '' }}">
                        {!! Form::label('address', trans('models/universities.fields.address'), ['class' => 'control-label col-sm-3']) !!}                                
                        <div class="col-sm-4">
                            {!! Form::text('address', null, ['class' => 'form-control ']) !!}     
                            @if ($errors->has('address'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('address') }}</strong>
                                </span>
                            @endif                                                                                                   
                        </div>
                    </div>    
                    <div class="form-group {{ $errors->has('website_url') ? ' has-error' : '' }}">
                        {!! Form::label('website_url', trans('models/universities.fields.website_url'), ['class' => 'control-label col-sm-3']) !!}                                
                        <div class="col-sm-4">
                            {!! Form::text('website_url', null, ['class' => 'form-control ']) !!}     
                            @if ($errors->has('website_url'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('website_url') }}</strong>
                                </span>
                            @endif                                                                                                   
                        </div>
                    </div>    
                    <div class="form-group {{ $errors->has('phone_number') ? ' has-error' : '' }}">
                        {!! Form::label('phone_number', trans('models/universities.fields.phone_number'), ['class' => 'control-label col-sm-3']) !!}                                
                        <div class="col-sm-4">
                            {!! Form::text('phone_number', null, ['class' => 'form-control']) !!}     
                            @if ($errors->has('phone_number'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('phone_number') }}</strong>
                                </span>
                            @endif                                                                                                   
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                        {!! Form::label('email', trans('models/universities.fields.email'), ['class' => 'control-label col-sm-3']) !!}                                
                        <div class="col-sm-4">
                            {!! Form::text('email', null, ['class' => 'form-control']) !!}     
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif                                                                                                   
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('logo') ? ' has-error' : '' }}">
                        {!! Form::label('logo', trans('models/universities.fields.logo_url'), ['class' => 'control-label col-sm-3']) !!}                                
                        <div class="col-sm-4">
                            {!! Form::file('logo', ['class' => 'form-control']) !!}   
                            
                            @if( isset($university->logo_url))
                            <img src="{{ $university->logo() }}" width="100px" height="100px">
                                
                            @endif

                            @if ($errors->has('logo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('logo') }}</strong>
                                </span>
                            @endif                                                                                                   
                        </div>
                    </div>      
                   
                </div>
                <div class="form-actions fluid">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                            <a href="{{ route('universities.index') }}" class="btn default">{{ trans('general.cancel') }}</a>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!-- END FORM-->
        </div>
    </div>
@endsection('content')