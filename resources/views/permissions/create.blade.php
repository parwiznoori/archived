@extends('layouts.app')

@section('content')
    <div class="portlet box">
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::open(['route' => 'permissions.store', 'method' => 'post', 'class' => 'form-horizontal']) !!}
            <div class="form-body" id="app">
                
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
                
                
                          
                <hr>
                <div class="form-actions fluid">
                    <div class="row">
                        <div class="col-md-offset-2 col-md-8">
                            <button type="submit" class="btn green">{{ trans('crud.save') }}</button>
                            <a href="{{ route('permissions.index') }}" class="btn default">{{ trans('general.cancel') }}</a>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!-- END FORM-->
            </div>
        </div>
        @endsection('content')

        @push('scripts')
            <script>
                $(function () {
                    $('.select2').change(function () {
                        $('.select2-ajax').val(null).trigger('change');
                    })
                })
            </script>
    @endpush