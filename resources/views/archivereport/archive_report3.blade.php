@extends('layouts.app')
@section('content')

 <div class="portlet box">
    <div class="portlet-body">
        <div class="panel-body">
        <div class="col-md-12">
            <div class="card">
                    {{-- <div style="margin-top: 20px; padding: 20px; border: 2px solid lightgray; background-color: #f9f9f9; border-radius: 8px;"> --}}
                        {!! Form::open(['method' => 'post', 'route' => 'reportresult3', 'class' => 'form-horizontal', 'target' => 'new']) !!}
                        {{-- <div class="form-body" id="app"> --}}
                            <br>
                            <input type="hidden" id="reporttype" name="reporttype"/>
                            <input type="hidden" id="report_type" name="report_type" value="1"/>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('start_date', trans('general.start_dates'), ['class' => 'control-label col-md-3']) !!}
                                        <div class="col-md-8">
                                            {!! Form::text('start_date', null, ['class' => 'form-control datepicker', 'placeholder' => trans('general.select')]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('end_date', trans('general.end_dates'), ['class' => 'control-label col-md-3']) !!}
                                        <div class="col-md-8">
                                            {!! Form::text('end_date', null, ['class' => 'form-control datepicker', 'placeholder' => trans('general.select')]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('doc_type', trans('general.archive_doc_type'), ['class' => 'control-label col-md-3']) !!}
                                        <div class="col-md-8">
                                            {!! Form::select('doc_type', [ 
                                                '1' => 'دیپلوم',
                                                '2' => 'ترانسکریپت',
                                                '3' => 'ستاژ',
                                                
                                            ], null, ['class' => 'form-control select2', 'placeholder' => trans('general.select')]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('user_id', trans('general.users'), ['class' => 'control-label col-md-3']) !!}
                                        <div class="col-md-8">
                                            {!! Form::select('user_id', $reportarchivedoc, null, ['class' => 'form-control select2', 'placeholder' => trans('general.select')]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="form-actions fluid">
                                <div class="row">
                                    <div class="col-md-offset-1 col-md-12">
                                        <button type="submit" class="btn green">{{ trans('general.generate_report') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>

        <div id="report-content">
            {{-- Your report display logic --}}
        </div>
    </div>
</div>
@endsection
