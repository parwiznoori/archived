@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
       
        <div class="portlet"> 
                   
            <div class="portlet-body">
                @if (session('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                        
                    </div>
                @endif
                <!-- BEGIN FORM-->            
                {!! Form::open(['route' => 'kankor_results.store_enrollment_type', 'method' => 'post', 'class' => 'form-horizontal']) !!}
                <div class="form-body" id="app">
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group {{ $errors->has('keyword') ? ' has-error' : '' }}">
                                {!! Form::label('keyword', trans('general.keyword'), ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('keyword', null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                 
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group {{ $errors->has('enrollment_type_id') ? ' has-error' : '' }}">
                                {!! Form::label('enrollment_type_id', trans('general.enrollment_type'), ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-8">
                                    {!! Form::select('enrollment_type_id', $enrollment_types, old('enrollment_type_id'), ['class' => 'form-control select2']) !!}
                                    @if ($errors->has('enrollment_type_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('enrollment_type_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    <hr>
                    <div class="form-actions fluid">
                        <div class="row">
                            <div class="col-md-offset-1 col-md-12">
                                <button type="submit"  class="btn green" >{{ trans('general.update_enrollment_type') }}</button>
                                <a href="{{ route('noticeboard') }}" class="btn default">{{ trans('general.cancel') }}</a>
                            </div>
                        </div>
                       
                    </div>
                    <br>
                {!! Form::close() !!}
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>


@endsection('content')
