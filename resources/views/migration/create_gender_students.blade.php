@extends('layouts.app')

@section('content')
    <div class="portlet box">
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            <div class="row" id="successMessage">
                <div class="col-md-5 col-md-offset-1">
                    @if(Session::has('message'))
                        <p class="alert alert-info text-center">{{ Session::get('message') }}</p><br>
                    @endif
                </div>
            </div>

            {!! Form::open(['route' => 'develope.migrate.migration_store_gender_students', 'method' => 'post', 'class' => 'form-horizontal']) !!}
            <div class="form-body" id="app">
                <div class="row col-md-6">
                    <p>source faculty</p>
                    <div class="row">
                       
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('genders') ? ' has-error' : '' }}">
                                    {!! Form::label('genders', trans('general.genders'), ['class' => 'control-label col-sm-3']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::select('genders', $genders, null, ['class' => 'form-control select2', 'placeholder' => trans('general.select')]) !!}
                                        @if ($errors->has('genders'))
                                            <span class="help-block">
                                    <strong>{{ $errors->first('genders') }}</strong>
                                </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                       
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group {{ $errors->has('genders_merge') ? ' has-error' : '' }}">
                                {!! Form::label('genders_merge', trans('general.genders_merge'), ['class' => 'control-label col-sm-3']) !!}
                                <div class="col-sm-9">
                                    {!! Form::select('genders_merge',['Male' => trans('general.Male'), 'Female' =>
                                    trans('general.Female')], null, ['class' => 'form-control']) !!}
                                    @if ($errors->has('genders_merge'))
                                        <span class="help-block">
                                <strong>{{ $errors->first('genders_merge') }}</strong>
                            </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             
                
                <div class="form-actions fluid">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-6">
                            <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                            
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
                });
                setTimeout(function() {
                    $('#successMessage').fadeOut('fast');
                }, 7000); // <-- time in milliseconds
            </script>
    @endpush