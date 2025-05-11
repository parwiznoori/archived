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

            {!! Form::open(['route' => 'develope.migrate.migration_store_department_faculty', 'method' => 'post', 'class' => 'form-horizontal']) !!}
            <div class="form-body" id="app">
                <div class="row col-md-6">
                    <p>source faculty</p>
                    <div class="row">
                        @if(auth()->user()->allUniversities())
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('university') ? ' has-error' : '' }}">
                                    {!! Form::label('university', trans('general.university'), ['class' => 'control-label col-sm-3']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::select('university', $universities, null, ['class' => 'form-control select2', 'placeholder' => trans('general.select')]) !!}
                                        @if ($errors->has('university'))
                                            <span class="help-block">
                                    <strong>{{ $errors->first('university') }}</strong>
                                </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            {!! Form::hidden('university', auth()->user()->university_id) !!}
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group {{ $errors->has('faculty') ? ' has-error' : '' }}">
                                {!! Form::label('faculty', trans('general.faculty'), ['class' => 'control-label col-sm-3']) !!}
                                <div class="col-sm-9">
                                    {!! Form::select('faculty',$faculties, null, ['class' => 'form-control select2-ajax', 'remote-url' => route('api.faculties'), 'remote-param' => '[name="university"]']) !!}
                                    @if ($errors->has('faculty'))
                                        <span class="help-block">
                                <strong>{{ $errors->first('faculty') }}</strong>
                            </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             
                <div class="row col-md-6">
                    <p>merge faculty</p>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group {{ $errors->has('faculty_merge') ? ' has-error' : '' }}">
                                {!! Form::label('faculty_merge', trans('general.faculty'), ['class' => 'control-label col-sm-3']) !!}
                                <div class="col-sm-9">
                                    {!! Form::select('faculty_merge',$faculties, null, ['class' => 'form-control select2-ajax', 'remote-url' => route('api.faculties'), 'remote-param' => '[name="university"]']) !!}
                                    @if ($errors->has('faculty_merge'))
                                        <span class="help-block">
                                <strong>{{ $errors->first('faculty_merge') }}</strong>
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